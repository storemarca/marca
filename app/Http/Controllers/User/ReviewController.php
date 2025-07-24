<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    protected $reviewService;
    
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
        $this->middleware('auth');
    }
    
    /**
     * Display a form to create a new review.
     */
    public function create(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $orderItem = null;
        
        // If order_item_id is provided, verify it belongs to the user and product
        if ($request->has('order_item_id')) {
            $orderItem = Auth::user()->customer->orders()
                ->whereHas('items', function ($query) use ($request, $product) {
                    $query->where('id', $request->order_item_id)
                        ->where('product_id', $product->id);
                })
                ->first();
                
            if (!$orderItem) {
                return redirect()->route('user.products.show', $product->slug)
                    ->with('error', 'You cannot review this product from this order.');
            }
        }
        
        // Check if user has already reviewed this product
        $existingReview = ProductReview::where('product_id', $product->id)
            ->where('customer_id', Auth::user()->customer->id)
            ->first();
            
        if ($existingReview) {
            return redirect()->route('user.reviews.edit', $existingReview->id)
                ->with('info', 'You have already reviewed this product. You can edit your review.');
        }
        
        return view('user.reviews.create', compact('product', 'orderItem'));
    }
    
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_item_id' => 'nullable|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'review' => 'required|string|min:10',
            'pros' => 'nullable|array',
            'pros.*' => 'string|max:255',
            'cons' => 'nullable|array',
            'cons.*' => 'string|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:2048',
        ]);
        
        try {
            // Add customer ID to the data
            $validated['customer_id'] = Auth::user()->customer->id;
            
            // Create the review
            $review = $this->reviewService->createReview(
                $validated,
                $request->hasFile('images') ? $request->file('images') : []
            );
            
            return redirect()->route('user.products.show', $review->product->slug)
                ->with('success', 'Your review has been submitted and is pending approval.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the specified review.
     */
    public function edit(ProductReview $review)
    {
        // Ensure the review belongs to the authenticated user
        if ($review->customer_id !== Auth::user()->customer->id) {
            abort(403);
        }
        
        $product = $review->product;
        
        return view('user.reviews.edit', compact('review', 'product'));
    }
    
    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, ProductReview $review)
    {
        // Ensure the review belongs to the authenticated user
        if ($review->customer_id !== Auth::user()->customer->id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'review' => 'required|string|min:10',
            'pros' => 'nullable|array',
            'pros.*' => 'string|max:255',
            'cons' => 'nullable|array',
            'cons.*' => 'string|max:255',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:2048',
        ]);
        
        try {
            // Update the review
            $review = $this->reviewService->updateReview(
                $review,
                $validated,
                $request->hasFile('images') ? $request->file('images') : []
            );
            
            return redirect()->route('user.products.show', $review->product->slug)
                ->with('success', 'Your review has been updated and is pending approval.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Remove the specified review from storage.
     */
    public function destroy(ProductReview $review)
    {
        // Ensure the review belongs to the authenticated user
        if ($review->customer_id !== Auth::user()->customer->id) {
            abort(403);
        }
        
        $productSlug = $review->product->slug;
        
        // Delete all images associated with the review
        foreach ($review->images as $image) {
            $this->reviewService->deleteReviewImage($image);
        }
        
        // Delete the review
        $review->delete();
        
        return redirect()->route('user.products.show', $productSlug)
            ->with('success', 'Your review has been deleted.');
    }
    
    /**
     * Delete a review image.
     */
    public function deleteImage(Request $request, ProductReview $review)
    {
        // Ensure the review belongs to the authenticated user
        if ($review->customer_id !== Auth::user()->customer->id) {
            abort(403);
        }
        
        $imageId = $request->image_id;
        $image = $review->images()->findOrFail($imageId);
        
        $this->reviewService->deleteReviewImage($image);
        
        return redirect()->back()
            ->with('success', 'Image deleted successfully.');
    }
    
    /**
     * Vote on a review.
     */
    public function vote(Request $request, ProductReview $review)
    {
        $validated = $request->validate([
            'is_helpful' => 'required|boolean',
        ]);
        
        try {
            $this->reviewService->voteOnReview(
                $review,
                Auth::user()->customer->id,
                $validated['is_helpful']
            );
            
            return response()->json([
                'success' => true,
                'helpful_votes' => $review->helpful_votes,
                'unhelpful_votes' => $review->unhelpful_votes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
    
    /**
     * Remove a vote from a review.
     */
    public function removeVote(ProductReview $review)
    {
        try {
            $result = $this->reviewService->removeVote(
                $review,
                Auth::user()->customer->id
            );
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'helpful_votes' => $review->helpful_votes,
                    'unhelpful_votes' => $review->unhelpful_votes,
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No vote found to remove.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
} 