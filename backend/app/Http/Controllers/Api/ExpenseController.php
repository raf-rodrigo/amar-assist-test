<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search'));
        $searchTerm = mb_strtolower($search);
        $items = $request->user()->expenses()->with('category:id,description')
            ->when($search !== '', fn (Builder $query) => $query->where(fn (Builder $nested) => $nested
                ->whereRaw('LOWER(description) LIKE ?', ["%{$searchTerm}%"])
                ->orWhereHas('category', fn (Builder $category) => $category->whereRaw('LOWER(description) LIKE ?', ["%{$searchTerm}%"]))))
            ->orderByDesc('date')->paginate(20)->withQueryString();
        return response()->json($items);
    }

    public function store(ExpenseRequest $request): JsonResponse
    {
        return response()->json($request->user()->expenses()->create($request->validated())->load('category:id,description'), 201);
    }

    public function show(Request $request, int $expense): JsonResponse
    {
        return response()->json($this->ownedExpense($request, $expense)->load('category:id,description'));
    }

    public function update(ExpenseRequest $request, int $expense): JsonResponse
    {
        $model = $this->ownedExpense($request, $expense);
        $model->update($request->validated());
        return response()->json($model->refresh()->load('category:id,description'));
    }

    public function destroy(Request $request, int $expense): JsonResponse
    {
        $this->ownedExpense($request, $expense)->delete();
        return response()->json(null, 204);
    }

    private function ownedExpense(Request $request, int $id): Expense
    {
        return $request->user()->expenses()->findOrFail($id);
    }
}
