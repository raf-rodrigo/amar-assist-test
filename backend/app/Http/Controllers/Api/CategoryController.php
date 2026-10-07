<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search'));
        $searchTerm = mb_strtolower($search);
        $categories = $request->user()->categories()
            ->when($search !== '', fn ($query) => $query->whereRaw('LOWER(description) LIKE ?', ["%{$searchTerm}%"]))
            ->orderBy('description')->paginate(20)->withQueryString();

        return response()->json($categories);
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        return response()->json($request->user()->categories()->create($request->validated()), 201);
    }

    public function show(Request $request, int $category): JsonResponse
    {
        return response()->json($this->ownedCategory($request, $category));
    }

    public function update(CategoryRequest $request, int $category): JsonResponse
    {
        $model = $this->ownedCategory($request, $category);
        $model->update($request->validated());
        return response()->json($model->refresh());
    }

    public function destroy(Request $request, int $category): JsonResponse
    {
        $model = $this->ownedCategory($request, $category);
        if ($model->incomes()->exists() || $model->expenses()->exists()) {
            return response()->json(['message' => 'A categoria está em uso e não pode ser excluída.'], 409);
        }
        $model->delete();
        return response()->json(null, 204);
    }

    private function ownedCategory(Request $request, int $id): Category
    {
        return $request->user()->categories()->findOrFail($id);
    }
}
