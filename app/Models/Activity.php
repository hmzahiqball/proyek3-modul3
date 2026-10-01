<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'code',
        'title',
        'description',
        'poster_path',
        'start_at',
        'end_at',
        'capacity',
        'registered_count',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'date',
            'end_at' => 'date',
            'capacity' => 'integer',
            'registered_count' => 'integer',
        ];
    }

    /**
     * Relationship: setiap Activity dimiliki oleh satu Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function scopeFilterByStatus($query, $status)
    {
        $validStatuses = ['draft', 'published', 'completed'];

        return $query->when(in_array($status, $validStatuses, true), function ($q) use ($status) {
            $q->where('status', $status);
        });
    }

    public function scopeSearch($query, ?string $search)
    {
        return $query->when($search, function ($q) use ($search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        });
    }

    public function scopeFilterByCategory($query, $categoryId)
    {
        return $query->when($categoryId, fn ($q) => $q->where('category_id', $categoryId));
    }

    public function scopeSortByStart($query, ?string $sort)
    {
        return $query->orderBy('start_at', $sort === 'oldest' ? 'asc' : 'desc');
    }
}
