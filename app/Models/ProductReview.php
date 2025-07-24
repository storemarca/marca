<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'order_item_id',
        'rating',
        'title',
        'review',
        'pros',
        'cons',
        'verified_purchase',
        'is_approved',
        'is_featured',
        'helpful_votes',
        'unhelpful_votes',
        'approved_at',
    ];

    protected $casts = [
        'pros' => 'array',
        'cons' => 'array',
        'verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the product that was reviewed.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the customer who wrote the review.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order item associated with this review.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the images for this review.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductReviewImage::class);
    }

    /**
     * Get the votes for this review.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(ProductReviewVote::class);
    }

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include featured reviews.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include verified purchase reviews.
     */
    public function scopeVerified($query)
    {
        return $query->where('verified_purchase', true);
    }

    /**
     * Scope a query to only include reviews for a specific product.
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope a query to only include reviews by a specific customer.
     */
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Approve the review.
     */
    public function approve(): void
    {
        $this->is_approved = true;
        $this->approved_at = now();
        $this->save();
    }

    /**
     * Unapprove the review.
     */
    public function unapprove(): void
    {
        $this->is_approved = false;
        $this->approved_at = null;
        $this->save();
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(): void
    {
        $this->is_featured = !$this->is_featured;
        $this->save();
    }

    /**
     * Record a helpful vote.
     */
    public function addHelpfulVote(): void
    {
        $this->helpful_votes++;
        $this->save();
    }

    /**
     * Record an unhelpful vote.
     */
    public function addUnhelpfulVote(): void
    {
        $this->unhelpful_votes++;
        $this->save();
    }

    /**
     * Remove a helpful vote.
     */
    public function removeHelpfulVote(): void
    {
        $this->helpful_votes = max(0, $this->helpful_votes - 1);
        $this->save();
    }

    /**
     * Remove an unhelpful vote.
     */
    public function removeUnhelpfulVote(): void
    {
        $this->unhelpful_votes = max(0, $this->unhelpful_votes - 1);
        $this->save();
    }
} 