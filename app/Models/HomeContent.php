<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['content'])]
class HomeContent extends Model
{
    private const SINGLETON_ID = 1;

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultContent(): array
    {
        return config('home_content', []);
    }

    /**
     * @return array<string, mixed>
     */
    public static function resolvedContent(): array
    {
        $defaults = static::defaultContent();
        $storedContent = static::query()->find(self::SINGLETON_ID)?->content;

        if (! is_array($storedContent)) {
            return $defaults;
        }

        $resolved = static::mergeWithDefaults($defaults, $storedContent);

        return is_array($resolved) ? $resolved : $defaults;
    }

    /**
     * @param array<string, mixed> $content
     */
    public static function storeContent(array $content): self
    {
        $payload = static::mergeWithDefaults(static::defaultContent(), $content);

        return static::query()->updateOrCreate(
            ['id' => self::SINGLETON_ID],
            ['content' => $payload]
        );
    }

    private static function mergeWithDefaults(mixed $defaults, mixed $stored): mixed
    {
        if (! is_array($defaults) || ! is_array($stored)) {
            return $stored;
        }

        if (array_is_list($defaults) || array_is_list($stored)) {
            return $stored;
        }

        $merged = $defaults;

        foreach ($stored as $key => $value) {
            if (! array_key_exists($key, $defaults)) {
                $merged[$key] = $value;
                continue;
            }

            $merged[$key] = static::mergeWithDefaults($defaults[$key], $value);
        }

        return $merged;
    }
}
