<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\XmlImportService;
use Illuminate\Http\JsonResponse;

class ImportController extends Controller
{
    /**
     * Executa a importação de dados a partir de um arquivo XML.
     */
    public function __invoke(XmlImportService $xmlImportService): JsonResponse
    {
        return response()->json([
            'message' => 'XML data imported successfully.', // Mensagem indicando sucesso na importação
            'data' => $xmlImportService->importAll(), // Dados retornados pelo serviço após o processamento do XML
        ]);
    }
}
