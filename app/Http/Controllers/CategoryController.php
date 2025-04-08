<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
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
        return ResponseHelper::success('Data returned successfully', CategoryResource::collection($Categories));
    }


    //  Returns: Category
    //  Accessable: by admin role
    public function storeCategory(StoreNameRequest $request)
    {
        $Category = Category::create($request->validated());
        return ResponseHelper::success('Data returned successfully', new CategoryResource($Category));
    }


    //  Returns: Category
    //  Accessable: by admin role
    public function updateCategory(UpdateNameRequest $request, int $CategoryId)
    {
        try {
            $Category = Category::findOrFail($CategoryId);
            $Category->update($request->validated());
            return ResponseHelper::success('Data updated successfully', new CategoryResource($Category));
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }

    //  Returns: nothing
    //  Accessable: by admin role
    public function destroyCategory(int $CategoryId)
    {
        try {
            $Category = Category::findOrFail($CategoryId);
            $Category->delete();
            return ResponseHelper::success('Data deleted successfully', []);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }


    //  Returns: get all book related in certain category
    //  Accessable: by user and admin role
    public function getBooksInCategoriy(int $categoryId)
    {
        try {
            $category = Category::findOrFail($categoryId);
            $books = Category::findOrFail($categoryId)->books;
            return ResponseHelper::success('Data returned successfully', BookResource::collection($books));
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }

    //  Returns: all books with inputing key
    //  Accessable: by user and admin role
    public function search($key){
        $books = Category::with('books')->where('name','LIKE','%'.$key.'%')->get();
        return ResponseHelper::success('Data returned successfully', $books);

    }
}
