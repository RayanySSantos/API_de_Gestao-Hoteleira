<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\XmlImportService;
use Illuminate\Http\JsonResponse;

class ImportController extends Controller
{
    public function __invoke(XmlImportService $xmlImportService): JsonResponse
    {
        return response()->json([
            'message' => 'XML data imported successfully.',
            'data' => $xmlImportService->importAll(),
        ]);
    }
}
