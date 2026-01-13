<?php

declare(strict_types=1);

namespace App\Services;

use App\Jobs\ProcessCsvImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImportService
{
    public function handleImport(UploadedFile $file): string
    {

        $filename = $file->store('imports');

        $fullPath = storage_path('app/private/' . $filename);

        ProcessCsvImport::dispatch($fullPath);

        return $filename;
    }
}