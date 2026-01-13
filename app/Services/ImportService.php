<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\ProcessCsvImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ImportService
{
  public function handleImport(UploadedFile $file): string
  {

    $filename = $file->store('imports');

    $fullPath = storage_path('app/' . $filename);

    $importId = (string) Str::uuid();

    Cache::put("import_status_{$importId}", [
      'status' => 'pending',
      'processed' => 0,
      'total' => 0,
      'message' => 'Job is queued...'
    ], 3600);

    ProcessCsvImport::dispatch($fullPath, $importId);

    return $importId;
  }
}
