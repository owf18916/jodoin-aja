<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = collect([
            [
                'name' => 'USD',
                'description' => 'United States Dollar',
                'slug' => 'Dollar'
            ],
            [
                'name' => 'IDR',
                'description' => 'Indonesian Rupiah',
                'slug' => 'Rupiah'
            ],
            [
                'name' => 'JPY',
                'description' => 'Japanese Yen',
                'slug' => 'Yen'
            ],
            [
                'name' => 'EUR',
                'description' => 'Euro',
                'slug' => 'Euro'
            ],
            [
                'name' => 'GBP',
                'description' => 'British Pound Sterling',
                'slug' => 'Pounds'
            ],
            [
                'name' => 'THB',
                'description' => 'Thai Bath',
                'slug' => 'Bath'
            ],
            [
                'name' => 'CNY',
                'description' => 'Chinese Yuan Renminbi',
                'slug' => 'Yuan'
            ],
            [
                'name' => 'SGD',
                'description' => 'Singapore Dollar',
                'slug' => 'Dollar'
            ],
            [
                'name' => 'CHF',
                'description' => 'Swiss Franc',
                'slug' => 'Franc'
            ],
            [
                'name' => 'PHP',
                'description' => 'Phillipine Peso',
                'slug' => 'Peso'
            ],
            [
                'name' => 'AUD',
                'description' => 'Australian Dollar',
                'slug' => 'Dollar'
            ],
        ]);

        $currencies->each(fn ($currency) => Currency::create($currency));
    }
}
