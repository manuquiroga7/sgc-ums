<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Producto::orderBy('nombre')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $producto = Producto::create($this->validateData($request));

        return response()->json($producto, 201);
    }

    public function show(Producto $producto): JsonResponse
    {
        return response()->json($producto);
    }

    public function update(Request $request, Producto $producto): JsonResponse
    {
        $producto->update($this->validateData($request));

        return response()->json($producto);
    }

    public function destroy(Producto $producto): JsonResponse
    {
        $producto->delete();

        return response()->json(null, 204);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:191'],
            'categoria' => ['nullable', 'string', 'max:191'],
            'subtipo' => ['nullable', 'string', 'max:191'],
            'descripcion' => ['nullable', 'string'],
            'activo' => ['boolean'],
        ]);
    }
}
