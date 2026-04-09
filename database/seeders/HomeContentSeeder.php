<?php

namespace Database\Seeders;

use App\Models\HomeContent;
use Illuminate\Database\Seeder;

class HomeContentSeeder extends Seeder
{
    /**
     * Seed the portfolio home content with configured defaults.
     */
    public function run(): void
    {
        HomeContent::storeContent(HomeContent::defaultContent());
    }
}
