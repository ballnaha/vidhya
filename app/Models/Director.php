<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Director extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'first_name',
        'last_name',
        'eyebrow',
        'role',
        'stats',
        'bio_title_white',
        'bio_title_gradient',
        'bio_image',
        'bio_alt',
        'bio',
        'works_eyebrow',
        'works_title_white',
        'works_title_muted',
        'works',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stats' => 'array',
            'bio' => 'array',
            'works' => 'array',
        ];
    }
}
