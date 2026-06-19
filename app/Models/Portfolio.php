<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'service_id',
        'video_url',
        'video_aspect_ratio',
        'image',
        'span',
        'show_in_portfolio',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'service_id' => 'integer',
            'show_in_portfolio' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the service associated with this portfolio item.
     */
    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
