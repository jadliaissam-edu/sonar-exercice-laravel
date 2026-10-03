<?php

namespace App\Level1\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController
{
	public function index()
	{
		$items = Category::orderBy('name')->get();   

		return response()->json(['data' => $items]);
	}

	public function store(Request $request)
	{
		$data = $request->validate(['name' => 'required|string|max:60']);
		$category = Category::create([
			'name' => $data['name'],
			'slug' => strtolower(str_replace(' ', '-', $data['name'])),
		]);

		return response()->json($category, 201);
	}
}
