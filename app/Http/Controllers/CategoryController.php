<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreNameRequest;
use App\Http\Requests\UpdateNameRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Resources\BookResource;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller
{
    //  Returns: all Categories
    //  Accessable: by user and admin role
    public function viewCategories()
    {
        $Categories = Category::all();
        return response()->json([
            'message' => 'Categories in our system are:',
            'Categories' =>  CategoryResource::collection($Categories)
        ], 200);
    }


    //  Returns: Category
    //  Accessable: by admin role
    public function storeCategory(StoreNameRequest $request)
    {
        $Category = Category::create($request->validated());
        return response()->json([
            'message' => 'Category created successfully',
            'Category' => new CategoryResource($Category)
        ], 201);
    }


    //  Returns: Category
    //  Accessable: by admin role
    public function updateCategory(UpdateNameRequest $request, int $CategoryId)
    {
        try {
            $Category = Category::findOrFail($CategoryId);
            $Category->update($request->validated());
            return response()->json([
                'message' => 'Category updated successfully',
                'Categories' => new CategoryResource($Category)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Category not found',
                'details' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'something went wrong',
                'details' => $e->getMessage()
            ], 404);
        }
    }

    //  Returns: nothing
    //  Accessable: by admin role
    public function destroyCategory(int $CategoryId)
    {
        try {
            $Category = Category::findOrFail($CategoryId);
            $Category->delete();
            return response()->json([], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Category not found',
                'details' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'something went wrong',
                'details' => $e->getMessage()
            ], 404);
        }
    }


    //  Returns: get all book related in certain category
    //  Accessable: by user and admin role
    public function getBooksInCategoriy(int $categoryId)
    {
        try {
            $category = Category::findOrFail($categoryId);
            $books = Category::findOrFail($categoryId)->books;
            return response()->json([
                'category_name' => $category->name,
                'Categories' => BookResource::collection($books)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Category not found',
                'details' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'something went wrong',
                'details' => $e->getMessage()
            ], 404);
        }
    }

    //  Returns: all books with inputing key
    //  Accessable: by user and admin role
    public function search($key){
        $books = Category::with('books')->where('name','LIKE','%'.$key.'%')->get();
        return response()->json($books, 200);
    }
}
