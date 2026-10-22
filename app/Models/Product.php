<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'price',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Return the formatted price string (e.g. "$420.00"), or null if price was not loaded.
     */
    public function formattedPrice(): ?string
    {
        if ($this->price === null) {
            return null;
        }

        return static::formatPrice($this->price);
    }

    /**
     * Consistent wholesale currency formatting ($X,XXX.XX).
     */
    public static function formatPrice(float|int|string $amount): string
    {
        return '$'.number_format((float) $amount, 2);
    }
}
