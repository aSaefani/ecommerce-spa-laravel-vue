<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'type', 'value', 'min_order', 'max_uses', 'used_count', 'valid_from', 'valid_until'])]
class Discount extends Model
{
    protected function casts(): array
    {
        return [
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
        ];
    }

    public function isValid(): bool
    {
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }
        return now()->between($this->valid_from, $this->valid_until);
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->min_order) {
            return 0;
        }
        if ($this->type === 'percentage') {
            return round($subtotal * ($this->value / 100), 2);
        }
        return min($this->value, $subtotal);
    }
}
