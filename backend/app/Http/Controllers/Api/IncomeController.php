<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IncomeRequest;
use App\Models\Income;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search'));
        $items = $request->user()->incomes()->with('category:id,description')
            ->when($search !== '', fn (Builder $query) => $query->where(fn (Builder $nested) => $nested
                ->where('description', 'ilike', "%{$search}%")
                ->orWhereHas('category', fn (Builder $category) => $category->where('description', 'ilike', "%{$search}%"))))
            ->orderByDesc('date')->paginate(20)->withQueryString();
        return response()->json($items);
    }

    public function store(IncomeRequest $request): JsonResponse
    {
        return response()->json($request->user()->incomes()->create($request->validated())->load('category:id,description'), 201);
    }

    public function show(Request $request, int $income): JsonResponse
    {
        return response()->json($this->ownedIncome($request, $income)->load('category:id,description'));
    }

    public function update(IncomeRequest $request, int $income): JsonResponse
    {
        $model = $this->ownedIncome($request, $income);
        $model->update($request->validated());
        return response()->json($model->refresh()->load('category:id,description'));
    }

    public function destroy(Request $request, int $income): JsonResponse
    {
        $this->ownedIncome($request, $income)->delete();
        return response()->json(null, 204);
    }

    private function ownedIncome(Request $request, int $id): Income
    {
        return $request->user()->incomes()->findOrFail($id);
    }
}

