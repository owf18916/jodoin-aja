<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csv = fopen(base_path('database/data/suppliers.csv'),"r");
        $firstline = true;
        while (($data = fgetcsv($csv, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                Supplier::create([
                    'name' => $data[0],
                ]);
            }

            $firstline = false;
        }

        fclose($csv);
    }
}
