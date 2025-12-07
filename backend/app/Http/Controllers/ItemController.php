<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Category $category)
    {
        return response()->json($category->items()->get());
    }

    public function store(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
        ]);

        $data['category_id'] = $category->id;

        $item = Item::create($data);

        return response()->json($item, 201);
    }

    public function show(Category $category, Item $item)
    {
        if ($item->category_id !== $category->id) {
            abort(404);
        }

        return response()->json($item);
    }

    public function update(Request $request, Category $category, Item $item)
    {
        if ($item->category_id !== $category->id) {
            abort(404);
        }

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
        ]);

        $item->update($data);

        return response()->json($item);
    }

    public function destroy(Category $category, Item $item)
    {
        if ($item->category_id !== $category->id) {
            abort(404);
        }

        $item->delete();

        return response()->json(null, 204);
    }
}
