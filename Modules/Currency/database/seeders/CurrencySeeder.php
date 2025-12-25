<?php
namespace Modules\Currency\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Currency\app\Models\MultiCurrency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MultiCurrency::truncate();
        if (! MultiCurrency::first()) {

            // Bangladeshi Currency
            $currency                    = new MultiCurrency();
            $currency->currency_name     = '৳-Taka';
            $currency->country_code      = 'BD';
            $currency->currency_code     = 'BDT';
            $currency->currency_icon     = 'TK';
            $currency->is_default        = 'yes';
            $currency->currency_rate     = 1;
            $currency->currency_position = 'before_price';
            $currency->status            = 'active';
            $currency->save();
        }
    }
}
