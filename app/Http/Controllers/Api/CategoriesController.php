<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoriesController extends Controller
{
    public function index()
    {
        $category = Category::all();

        return response()->json([
            'message' => 'list of categories',
            'data' => $category
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $category = Category::create([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'category created successfully',
            'data' => $category
        ], 201);
    }


   
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100'
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'name' => $request->name
        ]);

        return response()->json([
            'message' => 'Category updated',
            'data' => $category
        ]);
    }
}