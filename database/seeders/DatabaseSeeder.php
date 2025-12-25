<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Modules\Currency\database\seeders\CurrencySeeder;
use Modules\GlobalSetting\database\seeders\EmailTemplateSeeder;
use Modules\GlobalSetting\database\seeders\GlobalSettingInfoSeeder;


use Modules\Language\database\seeders\LanguageSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            CurrencySeeder::class,
            GlobalSettingInfoSeeder::class,
            EmailTemplateSeeder::class,
            RolePermissionSeeder::class,
            AdminInfoSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
