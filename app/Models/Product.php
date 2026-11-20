<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Services\ImageStorage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'stock_quantity',
        'image',
        'image_file_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return app(ImageStorage::class)->url($this->image);
    }
}
