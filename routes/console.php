<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('portfolio:sync-content', function () {
    \App\Models\HomeContent::storeContent(\App\Models\HomeContent::defaultContent());
    $this->info('Home content successfully synchronized with config/home_content.php!');
})->purpose('Synchronize database home content with config defaults');
