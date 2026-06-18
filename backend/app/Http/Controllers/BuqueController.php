<?php

namespace App\Http\Controllers;

use App\Models\Buque;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuqueController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Buque::orderBy('nombre')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateData($request);
        $buque = Buque::create($data);

        return response()->json($buque, 201);
    }

    public function show(Buque $buque): JsonResponse
    {
        return response()->json($buque);
    }

    public function update(Request $request, Buque $buque): JsonResponse
    {
        $buque->update($this->validateData($request));

        return response()->json($buque);
    }

    public function destroy(Buque $buque): JsonResponse
    {
        $buque->delete();

        return response()->json(null, 204);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:191'],
            'bandera' => ['nullable', 'string', 'max:191'],
            'numero_imo' => ['nullable', 'string', 'max:191'],
            'call_sign' => ['nullable', 'string', 'max:191'],
            'propietario' => ['nullable', 'string', 'max:191'],
            'tipo_buque' => ['nullable', 'string', 'max:191'],
            'activo' => ['boolean'],
        ]);
    }
}
