<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class AppVersion
{
    public static function get(): array
    {
        // If running before DB is ready (e.g. package:discover) or cache table missing, skip DB cache
        try {
            $cacheIsDatabase = config('cache.default') === 'database';
            if ($cacheIsDatabase && ! Schema::hasTable('cache')) {
                return self::readFile();
            }

            return Cache::remember('app.version.json', 300, fn () => self::readFile());
        } catch (\Throwable $e) {
            // any cache/DB error → just read from file
            return self::readFile();
        }
    }

    protected static function readFile(): array
    {
        $path = public_path('build/version.json');
        if (! is_file($path)) {
            return [
                'version'  => '0.0.0',
                'sha'      => null,
                'env'      => app()->environment(),
                'built_at' => null,
            ];
        }

        $data = json_decode(file_get_contents($path) ?: '[]', true) ?: [];

        return [
            'version'  => $data['version']  ?? '0.0.0',
            'sha'      => $data['sha']      ?? null,
            'env'      => $data['env']      ?? app()->environment(),
            'built_at' => $data['built_at'] ?? null,
        ];
    }
}
