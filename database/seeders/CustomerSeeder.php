<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csv = fopen(base_path('database/data/customer.csv'),"r");
        $firstline = true;
        while (($data = fgetcsv($csv, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                Customer::create([
                    'name' => $data[0],
                ]);
            }

            $firstline = false;
        }

        fclose($csv);
    }
}
