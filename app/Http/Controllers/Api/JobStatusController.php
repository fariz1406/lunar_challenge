<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class JobStatusController extends Controller
{
    public function show(string $importId): JsonResponse
    {
        $status = Cache::get("import_status_{$importId}");

        if (!$status) {
            return response()->json(['message' => 'Job ID not found or expired'], 404);
        }

        return response()->json($status);
    }
}
