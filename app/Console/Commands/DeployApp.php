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
        Artisan::call('db:seed');
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

        // Buat symbolic link
        $this->info('Creating storage link...');
        Artisan::call('storage:link');
        $this->info(Artisan::output());

        $this->info('Deployment tasks completed successfully!');
    }
}
