<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreAutherRequest;
use App\Http\Requests\StoreNameRequest;
use App\Http\Requests\UpdateAutherRequest;
use App\Http\Requests\UpdateNameRequest;
use App\Http\Resources\AutherBookResource;
use App\Http\Resources\AutherResource;
use App\Models\Auther;
use App\Models\Book;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AutherController extends Controller
{
    //  Returns: all authers
    //  Accessable: by user and admin role
    public function viewAuthers()
    {
        $authers = Auther::all();
        return ResponseHelper::success('Data returned successfully', $authers);
    }

    //  Returns: auther
    //  Accessable: by admin role
    public function storeAuther(StoreNameRequest $request)
    {
        $auther = Auther::create($request->validated());
        return ResponseHelper::success('Data returned successfully', new AutherResource($auther));
    }

    //  Returns: auther
    //  Accessable: by admin role
    public function updateAuther(UpdateNameRequest $request, int $autherId)
    {
        try {
            $auther = Auther::findOrFail($autherId);
            $auther->update($request->validated());
            return ResponseHelper::success('Data returned successfully', new AutherResource($auther));
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }

    //  Returns: nothing
    //  Accessable: by admin role
    public function destroyAuther(int $autherId)
    {
        try {
            $auther = Auther::findOrFail($autherId);
            $auther->delete();
            return ResponseHelper::success('Data deleted successfuly', []);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }

    //  Returns: Auther's books
    //  Accessable: by user and admin role
    public function getBooksForAuther(int $autherId)
    {
        try {
            $books = Auther::findOrFail($autherId)->books;
            return ResponseHelper::success('Data returned successfully', $books);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }

    //  Returns: auther with related books
    //  Accessable: by user and admin role
    public function autherInfoWithRelatedBooks(int $autherId)
    {
        try {
            $autherData = Auther::with('books')->findOrFail($autherId);
            return ResponseHelper::success('Data returned successfully', new AutherBookResource($autherData));
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }

    //  Returns: all books with inputing key
    //  Accessable: by user and admin role
    public function search(string $key)
    {
        try {
            $data = Auther::with('books')->where('name', 'LIKE', '%' . $key . '%')->get();
            return ResponseHelper::success('Data returned successfully', $data);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }
}
