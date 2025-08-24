<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $commit = config('app.commit');

        // Fallback: try to read commit at runtime (won’t work on some hosts)
        if (!$commit) {
            try {
                $out = @exec('git rev-parse --short=8 HEAD');
                if (!empty($out)) {
                    $commit = trim($out);
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        View::share('build', [
            'version'    => config('app.version', 'dev'),
            'commit'     => $commit,                 // 8-char short hash
            'source_url' => config('app.source_url'),
            'author'     => config('app.author'),
            'env'        => app()->environment(),    // optional
        ]);
    }
}
