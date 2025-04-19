<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\AutherRequest;
use App\Http\Resources\AutherBookResource;
use App\Http\Resources\AutherResource;
use App\Models\Auther;
use Illuminate\Http\Request;

class AutherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authers = Auther::all();
        return ResponseHelper::success('Data returned successfully', $authers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AutherRequest $request)
    {
        $auther = Auther::create($request->validated());
        return ResponseHelper::success('Data returned successfully', new AutherResource($auther));
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
    public function update(AutherRequest $request, int $autherId)
    {
        $auther = Auther::findOrFail($autherId);
        $auther->update($request->validated());
        return ResponseHelper::success('Data returned successfully', new AutherResource($auther));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $autherId)
    {
        $auther = Auther::findOrFail($autherId);
        $auther->delete();
        return ResponseHelper::success('Data deleted successfuly', []);
    }

        //  Returns: all books with inputing key
    //  Accessable: by user and admin role
    public function search(string $key)
    {
        $data = Auther::with('books')->where('name', 'LIKE', '%' . $key . '%')->get();
        return ResponseHelper::success('Data returned successfully', $data);
    }

    //  Returns: Auther's books
    //  Accessable: by user and admin role
    public function getBooksForAuther(int $autherId)
    {
        $books = Auther::findOrFail($autherId)->books;
        return ResponseHelper::success('Data returned successfully', $books);
    }

    //  Returns: auther with related books
    //  Accessable: by user and admin role
    public function autherInfoWithRelatedBooks(int $autherId)
    {
        $autherData = Auther::with('books')->findOrFail($autherId);
        return ResponseHelper::success('Data returned successfully', new AutherBookResource($autherData));
    }

}
