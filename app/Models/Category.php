<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Relationship: satu Category memiliki banyak Activity.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
