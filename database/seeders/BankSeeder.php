<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = collect([
            [
                'name' => 'Mitsubishi UFJ Financial Group-IDR',
                'initial' => 'MUFG-IDR',
            ],
            [
                'name' => 'Mitsubishi UFJ Financial Group-USD',
                'initial' => 'MUFG-USD',
            ],
            [
                'name' => 'Bank Central Asia',
                'initial' => 'BCA',
            ],
            [
                'name' => 'Bank Mandiri',
                'initial' => 'Mandiri',
            ],
        ]);

        $banks->each(fn ($bank) => Bank::create($bank));
    }
}
