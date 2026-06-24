<?php

namespace App\Http\Controllers;

use App\Models\TipoCertificado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TipoCertificadoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(TipoCertificado::orderBy('nombre')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $tipo = TipoCertificado::create($this->validateData($request));

        return response()->json($tipo, 201);
    }

    public function show(TipoCertificado $tipoCertificado): JsonResponse
    {
        return response()->json($tipoCertificado);
    }

    public function update(Request $request, TipoCertificado $tipoCertificado): JsonResponse
    {
        $tipoCertificado->update($this->validateData($request));

        return response()->json($tipoCertificado);
    }

    public function destroy(TipoCertificado $tipoCertificado): JsonResponse
    {
        $tipoCertificado->delete();

        return response()->json(null, 204);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:191'],
            'prefijo' => ['nullable', 'string', 'max:10'],
            'intervalo_meses' => ['nullable', 'integer', 'min:0'],
            'normativa_aplicable' => ['nullable', 'string', 'max:191'],
            'descripcion' => ['nullable', 'string'],
        ]);
    }
}
