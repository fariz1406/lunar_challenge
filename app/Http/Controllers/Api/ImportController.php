<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadCsvRequest;
use App\Services\ImportService;
use Illuminate\Http\JsonResponse;

class ImportController extends Controller
{

    public function __construct(
        protected ImportService $importService
    ) {}

    public function store(UploadCsvRequest $request): JsonResponse
    {

        $filePath = $this->importService->handleImport($request->file('file'));

        return response()->json([
            'message' => 'Processing',
            'file_path' => $filePath,
            'note' => 'Import sedang berjalan di background.',
        ], 202); // 202 Accepted
    }
}
