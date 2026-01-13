<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;

class GenerateCsv extends Command
{

    protected $signature = 'make:dummy-csv {count=100000}';

    protected $description = 'Generate dummy CSV file for testing import';

    public function handle()
    {
        $count = (int) $this->argument('count');
        $this->info("Generating {$count} dummy rows...");

        $faker = Faker::create('id_ID');
        $path = storage_path('app/dummy_users.csv');
        $handle = fopen($path, 'w');

        fputcsv($handle, ['Name', 'Email', 'Address']);

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            fputcsv($handle, [
                $faker->name,
                $faker->unique()->email,
                str_replace(["\n", "\r"], " ", $faker->address)
            ]);

            if ($i % 1000 === 0) {
                $bar->advance(1000);
            }
        }

        $bar->finish();
        fclose($handle);

        $this->newLine(2);
        $this->info("✅ Berhasil! File tersimpan di:");
        $this->line($path);
    }
}