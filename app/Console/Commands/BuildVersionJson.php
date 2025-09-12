<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BuildVersionJson extends Command
{
    protected $signature = 'app:build-version-json {--env= : Override env name}';
    protected $description = 'Generate public/build/version.json for local dev';

    public function handle(): int
    {
        $sha = trim(shell_exec('git rev-parse --short=8 HEAD') ?? '') ?: null;
        $version = env('APP_VERSION', '0.0.0');
        $envName = $this->option('env') ?: app()->environment();

        @mkdir(public_path('build'), 0777, true);
        $payload = json_encode([
            'version'  => $version,
            'sha'      => $sha,
            'env'      => $envName,
            'built_at' => now('UTC')->toIso8601String(),
        ], JSON_PRETTY_PRINT);

        file_put_contents(public_path('build/version.json'), $payload);
        $this->info('Wrote public/build/version.json: '.$payload);

        return self::SUCCESS;
    }
}
