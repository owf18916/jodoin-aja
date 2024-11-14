<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class DeployApp extends Command
{
/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run deployment tasks: migrate, seed, and create storage folders and symlinks';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Jalankan migrate:fresh
        $this->info('Running migrations...');
        Artisan::call('migrate:fresh');
        $this->info(Artisan::output());

        // Jalankan db:seed
        $this->info('Running database seeder...');
        // Artisan::call('db:seed');
        $this->info(Artisan::output());

        // Buat folder jika belum ada
        $folders = [
            'storage/app/public/documents',
            'storage/app/public/exports',
            'storage/app/chunks/exports',
            'storage/app/public/zip',
            'storage/exports',
            'storage/app/imports',
        ];

        foreach ($folders as $folder) {
            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0755, true);
                $this->info("Created directory: {$folder}");
            }
        }

        $this->generateYearFolders('payables');
        $this->generateYearFolders('receivables');
        $this->generateYearFolders('bl');

        // Buat symbolic link
        $this->info('Creating storage link...');
        Artisan::call('storage:link');
        $this->info(Artisan::output());

        $this->info('Deployment tasks completed successfully!');
    }

    private function generateYearFolders($subDir)
    {
        // Path utama tempat folder akan dibuat, misalnya di storage/app/directories
        $baseDirectory = storage_path('app/public/documents/'.$subDir.'/');

        // Rentang tahun dari 2024 hingga 2034
        $startYear = 2024;
        $endYear = 2034;

        // Loop untuk setiap tahun
        for ($year = $startYear; $year <= $endYear; $year++) {
            // Path untuk folder tahun
            $yearPath = $baseDirectory . DIRECTORY_SEPARATOR . $year;

            // Membuat folder tahun jika belum ada
            if (!File::exists($yearPath)) {
                File::makeDirectory($yearPath, 0755, true);
                $this->info("Created directory: $yearPath");
            }

            // Loop untuk setiap bulan dalam tahun tersebut (1 hingga 12)
            for ($month = 1; $month <= 12; $month++) {
                // Format bulan agar selalu dua digit (01, 02, ... 12)
                $monthName = str_pad($month, 2, '0', STR_PAD_LEFT);
                
                // Path untuk folder bulan
                $monthPath = $yearPath . DIRECTORY_SEPARATOR . $monthName;

                // Membuat folder bulan jika belum ada
                if (!File::exists($monthPath)) {
                    File::makeDirectory($monthPath, 0755, true);
                    $this->info("Created directory: $monthPath");
                }
            }
        }

        $this->info('Yearly'. $subDir .'directories with monthly subdirectories have been generated successfully.');
    }
}
