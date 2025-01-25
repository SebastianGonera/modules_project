<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuleRequest;
use App\Models\Module;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function store(ModuleRequest $request): JsonResponse
    {
        $newModule = Module::create($request->validated());

        return response()->json(
            [
                'message' => 'Module created successfully!',
                'data' => $newModule->id
            ],
            201);
    }
}
