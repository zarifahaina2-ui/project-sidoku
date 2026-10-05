<?php

namespace App\Providers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(
            ['dashboard', 'dokumen.index', 'dokumen.show', 'clustering.index'],
            function ($view) {
                $view->with('namaCluster', Cache::get('nama_cluster', []));
            }
        );
    }
}