<?php

namespace App\Http\Controllers;

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
        return response()->json([
            'message' => 'Authers in our system are:',
            'authers' =>  AutherResource::collection($authers)
        ], 200);
    }


    //  Returns: auther
    //  Accessable: by admin role
    public function storeAuther(StoreNameRequest $request)
    {
        $auther = Auther::create($request->validated());
        return response()->json([
            'message' => 'auther created successfully',
            'auther' => new AutherResource($auther)
        ], 201);
    }


    //  Returns: auther
    //  Accessable: by admin role
    public function updateAuther(UpdateNameRequest $request, int $autherId)
    {
        try {
            $auther = Auther::findOrFail($autherId);
            $auther->update($request->validated());
            return response()->json([
                'message' => 'auther updated successfully',
                'authers' => new AutherResource($auther)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Auther not found',
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
    public function destroyAuther(int $autherId)
    {
        try {
            $auther = Auther::findOrFail($autherId);
            $auther->delete();
            return response()->json([
                'message' => 'auther deleted successfully'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Auther not found',
                'details' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'something went wrong',
                'details' => $e->getMessage()
            ], 404);
        }
    }


    //  Returns: Auther's books
    //  Accessable: by user and admin role
    public function getBooksForAuther(int $autherId)
    {
        try {
            $books = Auther::findOrFail($autherId)->books;
            return response()->json($books, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Auther not found',
                'details' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'something went wrong',
                'details' => $e->getMessage()
            ], 404);
        }
    }


    //  Returns: auther with related books
    //  Accessable: by user and admin role
    public function autherInfoWithRelatedBooks(int $autherId)
    {
        try {
            $autherData = Auther::with('books')->findOrFail($autherId);
            return response()->json(new AutherBookResource($autherData), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Auther not found',
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
    public function search(string $key)
    {
        try {
            $data = Auther::with('books')->where('name', 'LIKE', '%' . $key . '%')->get();
            return response()->json($data, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' =>  'not found',
                'details' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'something went wrong',
                'details' => $e->getMessage()
            ], 404);
        }
    }
}
