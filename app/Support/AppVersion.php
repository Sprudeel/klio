<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class AppVersion
{
    public static function get(): array
    {
        return Cache::remember('app.version.json', 300, function () {
            $path = public_path('build/version.json');
            if (! is_file($path)) {
                return [
                    'version'  => '0.0.0',
                    'sha'      => null,
                    'built_at' => null,
                    'env' => app()->environment(),
                ];
            }

            $raw = file_get_contents($path);
            $data = json_decode($raw ?: '[]', true) ?: [];

            return [
                'version'  => $data['version']  ?? '0.0.0',
                'sha'      => $data['sha']      ?? null,
                'env' => $data['env'] ?? app()->environment(),
                'built_at' => $data['built_at'] ?? null,
            ];
        });
    }
}
