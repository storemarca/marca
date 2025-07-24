<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductReviewImage;
use App\Models\ProductReviewVote;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Notifications\ReviewApproved;

class ReviewService
{
    /**
     * Create a new product review
     *
     * @param array $data
     * @param array $images
     * @return ProductReview
     */
    public function createReview(array $data, array $images = []): ProductReview
    {
        DB::beginTransaction();
        
        try {
            $customer = Customer::findOrFail($data['customer_id']);
            $product = Product::findOrFail($data['product_id']);
            
            // Check if the customer has already reviewed this product
            $existingReview = ProductReview::where('product_id', $product->id)
                ->where('customer_id', $customer->id)
                ->first();
                
            if ($existingReview) {
                throw new Exception('You have already reviewed this product.');
            }
            
            // Check if this is a verified purchase
            $verifiedPurchase = false;
            $orderItem = null;
            
            if (isset($data['order_item_id'])) {
                $orderItem = $customer->orders()
                    ->whereHas('items', function ($query) use ($product, $data) {
                        $query->where('id', $data['order_item_id'])
                            ->where('product_id', $product->id);
                    })
                    ->exists();
                    
                if ($orderItem) {
                    $verifiedPurchase = true;
                }
            } else {
                // Check if customer has purchased this product
                $hasPurchased = $customer->orders()
                    ->whereHas('items', function ($query) use ($product) {
                        $query->where('product_id', $product->id);
                    })
                    ->exists();
                    
                if ($hasPurchased) {
                    $verifiedPurchase = true;
                }
            }
            
            // Create the review
            $review = new ProductReview([
                'product_id' => $product->id,
                'customer_id' => $customer->id,
                'order_item_id' => $data['order_item_id'] ?? null,
                'rating' => $data['rating'],
                'title' => $data['title'] ?? null,
                'review' => $data['review'],
                'pros' => $data['pros'] ?? null,
                'cons' => $data['cons'] ?? null,
                'verified_purchase' => $verifiedPurchase,
                'is_approved' => false, // Reviews need approval by default
            ]);
            
            $review->save();
            
            // Process images if any
            if (!empty($images)) {
                foreach ($images as $image) {
                    $this->addReviewImage($review, $image);
                }
            }
            
            DB::commit();
            
            return $review;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create product review: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update an existing product review
     *
     * @param ProductReview $review
     * @param array $data
     * @param array $images
     * @return ProductReview
     */
    public function updateReview(ProductReview $review, array $data, array $images = []): ProductReview
    {
        DB::beginTransaction();
        
        try {
            // Update review data
            $review->rating = $data['rating'] ?? $review->rating;
            $review->title = $data['title'] ?? $review->title;
            $review->review = $data['review'] ?? $review->review;
            $review->pros = $data['pros'] ?? $review->pros;
            $review->cons = $data['cons'] ?? $review->cons;
            
            // If the review was already approved, updating it will require re-approval
            if ($review->is_approved) {
                $review->is_approved = false;
                $review->approved_at = null;
            }
            
            $review->save();
            
            // Process new images if any
            if (!empty($images)) {
                foreach ($images as $image) {
                    $this->addReviewImage($review, $image);
                }
            }
            
            DB::commit();
            
            return $review;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update product review: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Add an image to a product review
     *
     * @param ProductReview $review
     * @param UploadedFile $image
     * @return ProductReviewImage
     */
    public function addReviewImage(ProductReview $review, UploadedFile $image): ProductReviewImage
    {
        // Generate a unique filename
        $filename = uniqid('review_') . '.' . $image->getClientOriginalExtension();
        $thumbnailFilename = 'thumb_' . $filename;
        
        // Store the original image
        $path = $image->storeAs('product_reviews', $filename, 'public');
        
        // Create and store thumbnail
        $thumbnail = Image::make($image->getRealPath())
            ->resize(200, 200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
        $thumbnailPath = 'product_reviews/' . $thumbnailFilename;
        Storage::disk('public')->put($thumbnailPath, (string) $thumbnail->encode());
        
        // Create the image record
        $reviewImage = new ProductReviewImage([
            'product_review_id' => $review->id,
            'image_path' => $path,
            'thumbnail_path' => $thumbnailPath,
            'is_approved' => false, // Images need approval by default
        ]);
        
        $reviewImage->save();
        
        return $reviewImage;
    }
    
    /**
     * Delete a review image
     *
     * @param ProductReviewImage $image
     * @return bool
     */
    public function deleteReviewImage(ProductReviewImage $image): bool
    {
        // Delete the files from storage
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        if ($image->thumbnail_path && Storage::disk('public')->exists($image->thumbnail_path)) {
            Storage::disk('public')->delete($image->thumbnail_path);
        }
        
        // Delete the record
        return $image->delete();
    }
    
    /**
     * Record a vote on a review
     *
     * @param ProductReview $review
     * @param int $customerId
     * @param bool $isHelpful
     * @return ProductReviewVote
     */
    public function voteOnReview(ProductReview $review, int $customerId, bool $isHelpful): ProductReviewVote
    {
        // Check if the customer has already voted on this review
        $existingVote = ProductReviewVote::where('product_review_id', $review->id)
            ->where('customer_id', $customerId)
            ->first();
            
        if ($existingVote) {
            // If the vote is the same, just return it
            if ($existingVote->is_helpful === $isHelpful) {
                return $existingVote;
            }
            
            // Update the vote and the review's vote counts
            DB::transaction(function () use ($existingVote, $review, $isHelpful) {
                // Remove the old vote count
                if ($existingVote->is_helpful) {
                    $review->removeHelpfulVote();
                } else {
                    $review->removeUnhelpfulVote();
                }
                
                // Update the vote
                $existingVote->is_helpful = $isHelpful;
                $existingVote->save();
                
                // Add the new vote count
                if ($isHelpful) {
                    $review->addHelpfulVote();
                } else {
                    $review->addUnhelpfulVote();
                }
            });
            
            return $existingVote;
        }
        
        // Create a new vote
        $vote = new ProductReviewVote([
            'product_review_id' => $review->id,
            'customer_id' => $customerId,
            'is_helpful' => $isHelpful,
        ]);
        
        DB::transaction(function () use ($vote, $review, $isHelpful) {
            $vote->save();
            
            // Update the review's vote count
            if ($isHelpful) {
                $review->addHelpfulVote();
            } else {
                $review->addUnhelpfulVote();
            }
        });
        
        return $vote;
    }
    
    /**
     * Remove a vote from a review
     *
     * @param ProductReview $review
     * @param int $customerId
     * @return bool
     */
    public function removeVote(ProductReview $review, int $customerId): bool
    {
        $vote = ProductReviewVote::where('product_review_id', $review->id)
            ->where('customer_id', $customerId)
            ->first();
            
        if (!$vote) {
            return false;
        }
        
        DB::transaction(function () use ($vote, $review) {
            // Update the review's vote count
            if ($vote->is_helpful) {
                $review->removeHelpfulVote();
            } else {
                $review->removeUnhelpfulVote();
            }
            
            // Delete the vote
            $vote->delete();
        });
        
        return true;
    }
    
    /**
     * Approve a review
     *
     * @param ProductReview $review
     * @return ProductReview
     */
    public function approveReview(ProductReview $review): ProductReview
    {
        $review->approve();
        
        // Send notification to the customer
        $review->customer->notify(new ReviewApproved($review));
        
        return $review;
    }
    
    /**
     * Unapprove a review
     *
     * @param ProductReview $review
     * @return ProductReview
     */
    public function unapproveReview(ProductReview $review): ProductReview
    {
        $review->unapprove();
        return $review;
    }
    
    /**
     * Approve a review image
     *
     * @param ProductReviewImage $image
     * @return ProductReviewImage
     */
    public function approveImage(ProductReviewImage $image): ProductReviewImage
    {
        $image->approve();
        return $image;
    }
    
    /**
     * Unapprove a review image
     *
     * @param ProductReviewImage $image
     * @return ProductReviewImage
     */
    public function unapproveImage(ProductReviewImage $image): ProductReviewImage
    {
        $image->unapprove();
        return $image;
    }
} 