<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ImportedUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class ProcessCsvImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $filePath
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        LazyCollection::make(function () {
            $handle = fopen($this->filePath, 'r');

            $header = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {

                yield $row;
            }

            fclose($handle);
        })

        ->chunk(1000)
        ->each(function (LazyCollection $chunk) {
            $dataToInsert = [];

            foreach ($chunk as $row) {

                if (count($row) < 3) continue;

                $dataToInsert[] = [
                    'name'       => $row[0],
                    'email'      => $row[1],
                    'address'    => $row[2],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($dataToInsert)) {
                ImportedUser::insertOrIgnore($dataToInsert); 

            }
        });

        Log::info("Import selesai untuk file: {$this->filePath}");
    }
}