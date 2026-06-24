<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    public const HOME_HERO_YOUTUBE_URL = 'home_hero_youtube_url';
    public const DEFAULT_HOME_HERO_YOUTUBE_URL = 'https://youtu.be/lboLBQ2QaeE';

    public const HOME_HERO_VIDEO_PATH = 'home_hero_video_path';
    public const DEFAULT_HOME_HERO_VIDEO_PATH = '/images/showreels_720.mp4';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function valueFor(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, ?string $value): self
    {
        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function homeHeroYoutubeUrl(): string
    {
        return static::valueFor(static::HOME_HERO_YOUTUBE_URL, static::DEFAULT_HOME_HERO_YOUTUBE_URL)
            ?: static::DEFAULT_HOME_HERO_YOUTUBE_URL;
    }

    public static function youtubeIdFromUrl(?string $url): ?string
    {
        if (! $url) {
            return 'lboLBQ2QaeE';
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/|v\/))([^#&?\/]*)/', $url, $matches) && strlen($matches[1]) === 11) {
            return $matches[1];
        }

        return null;
    }

    public static function homeHeroYoutubeId(): string
    {
        return static::youtubeIdFromUrl(static::homeHeroYoutubeUrl()) ?: 'lboLBQ2QaeE';
    }

    public static function homeHeroVideoPath(): string
    {
        return static::valueFor(static::HOME_HERO_VIDEO_PATH, static::DEFAULT_HOME_HERO_VIDEO_PATH)
            ?: static::DEFAULT_HOME_HERO_VIDEO_PATH;
    }
}