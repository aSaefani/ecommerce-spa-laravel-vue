<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['category_id', 'name', 'description', 'price', 'stock', 'image'])]
class Product extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function getAverageRating(): float
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getReviewCount(): int
    {
        return $this->reviews()->count();
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}
