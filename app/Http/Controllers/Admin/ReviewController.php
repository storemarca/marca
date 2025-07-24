<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\ProductReviewImage;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $reviewService;
    
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }
    
    /**
     * Display a listing of the reviews.
     */
    public function index(Request $request)
    {
        $query = ProductReview::with(['product', 'customer']);
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }
        
        // Filter by rating
        if ($request->has('rating') && $request->rating > 0) {
            $query->where('rating', $request->rating);
        }
        
        // Filter by product
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        // Sort
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);
        
        $reviews = $query->paginate(20);
        
        return view('admin.reviews.index', compact('reviews'));
    }
    
    /**
     * Display the specified review.
     */
    public function show(ProductReview $review)
    {
        $review->load(['product', 'customer', 'images', 'orderItem']);
        
        return view('admin.reviews.show', compact('review'));
    }
    
    /**
     * Update the status of a review.
     */
    public function approve(ProductReview $review)
    {
        $this->reviewService->approveReview($review);
        
        return redirect()->back()->with('success', 'Review approved successfully.');
    }
    
    /**
     * Update the status of a review.
     */
    public function unapprove(ProductReview $review)
    {
        $this->reviewService->unapproveReview($review);
        
        return redirect()->back()->with('success', 'Review unapproved successfully.');
    }
    
    /**
     * Toggle featured status of a review.
     */
    public function toggleFeatured(ProductReview $review)
    {
        $review->toggleFeatured();
        
        $status = $review->is_featured ? 'featured' : 'unfeatured';
        
        return redirect()->back()->with('success', "Review {$status} successfully.");
    }
    
    /**
     * Delete a review.
     */
    public function destroy(ProductReview $review)
    {
        // Delete all images associated with the review
        foreach ($review->images as $image) {
            $this->reviewService->deleteReviewImage($image);
        }
        
        // Delete the review
        $review->delete();
        
        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully.');
    }
    
    /**
     * Approve a review image.
     */
    public function approveImage(ProductReviewImage $image)
    {
        $this->reviewService->approveImage($image);
        
        return redirect()->back()->with('success', 'Image approved successfully.');
    }
    
    /**
     * Unapprove a review image.
     */
    public function unapproveImage(ProductReviewImage $image)
    {
        $this->reviewService->unapproveImage($image);
        
        return redirect()->back()->with('success', 'Image unapproved successfully.');
    }
    
    /**
     * Delete a review image.
     */
    public function destroyImage(ProductReviewImage $image)
    {
        $this->reviewService->deleteReviewImage($image);
        
        return redirect()->back()->with('success', 'Image deleted successfully.');
    }
    
    /**
     * Export reviews to CSV.
     */
    public function export(Request $request)
    {
        $query = ProductReview::with(['product', 'customer']);
        
        // Apply filters
        if ($request->has('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }
        
        if ($request->has('rating') && $request->rating > 0) {
            $query->where('rating', $request->rating);
        }
        
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $reviews = $query->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reviews-' . date('Y-m-d') . '.csv"',
        ];
        
        $columns = ['ID', 'Product', 'Customer', 'Rating', 'Title', 'Review', 'Status', 'Date'];
        
        $callback = function() use ($reviews, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->id,
                    $review->product->name,
                    $review->customer->name,
                    $review->rating,
                    $review->title,
                    $review->review,
                    $review->is_approved ? 'Approved' : 'Pending',
                    $review->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
} 