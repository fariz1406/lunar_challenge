<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ImportedUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;

class ProcessCsvImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $filePath,
        public string $importId
    ) {}

    public function handle(): void
    {

        $this->updateProgress(0, 0, 'calculating_total');

        $totalRows = 0;
        $handle = fopen($this->filePath, 'r');
        if ($handle) {

            fgetcsv($handle);
            while (fgets($handle) !== false) {
                $totalRows++;
            }
            fclose($handle);
        }

        $processedCount = 0;

        LazyCollection::make(function () {
            $handle = fopen($this->filePath, 'r');
            fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                yield $row;
            }
            fclose($handle);
        })
            ->chunk(1000)
            ->each(function (LazyCollection $chunk) use (&$processedCount, $totalRows) {
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

                $processedCount += count($chunk);
                $this->updateProgress($processedCount, $totalRows, 'processing');
            });

        $this->updateProgress($processedCount, $totalRows, 'completed');
    }

    private function updateProgress(int $processed, int $total, string $status): void
    {
        Cache::put("import_status_{$this->importId}", [
            'status' => $status,
            'processed' => $processed,
            'total' => $total,
            'message' => "Processed {$processed} of {$total} rows"
        ], 3600);
    }
}
