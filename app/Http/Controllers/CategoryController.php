<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Categories = Category::all();
        return ResponseHelper::success('Data returned successfully', CategoryResource::collection($Categories));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $categoryExists = Category::where('name', $request->name)->exists();
        if ($categoryExists) {
            return ResponseHelper::error('this category is already exists', [], 301);
        }
        $Category = Category::create($request->validated());
        return ResponseHelper::success('Data returned successfully', new CategoryResource($Category));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, int $CategoryId)
    {
        $Category = Category::findOrFail($CategoryId);
        $Category->update($request->validated());
        return ResponseHelper::success('Data updated successfully', new CategoryResource($Category));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $CategoryId)
    {
        $Category = Category::findOrFail($CategoryId);
        $Category->delete();
        return ResponseHelper::success('Data deleted successfully', []);
    }

        //  Returns: get all book related in certain category
    //  Accessable: by user and admin role
    public function getBooksInCategoriy(int $categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $books = Category::findOrFail($categoryId)->books;
        return ResponseHelper::success('Data returned successfully', BookResource::collection($books));
    }

    //  Returns: all books with inputing key
    //  Accessable: by user and admin role
    public function search($key)
    {
        $books = Category::with('books')->where('name', 'LIKE', '%' . $key . '%')->get();
        return ResponseHelper::success('Data returned successfully', $books);
    }
}
