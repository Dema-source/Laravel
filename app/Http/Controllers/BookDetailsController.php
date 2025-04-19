<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\BookDetailsRequest;
use App\Http\Resources\Book_DetailsResource;
use App\Models\Book_Details;
use Illuminate\Http\Request;

class BookDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $details = Book_Details::all();
        return ResponseHelper::success('Data returned successfully', Book_DetailsResource::collection($details));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookDetailsRequest $request)
    {
        $existingBookDetails = Book_Details::where('book_id', $request->book_id)
            ->orWhere('isbn', $request->isbn)
            ->first();
        if ($existingBookDetails) {
            return ResponseHelper::error('This book details already exist.', [], 409);
        }
        $Book_Details = Book_Details::create($request->validated());
        return ResponseHelper::success('Data returned successfully', new Book_DetailsResource($Book_Details));
    }

    /**
     * Display the specified resource.
     */
    public function show($Book_DetailsId)
    {
        $details = Book_Details::findOrFail($Book_DetailsId);
        return ResponseHelper::success('Data returned successfully', new Book_DetailsResource($details));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookDetailsRequest $request, int $Book_DetailsId)
    {
        $Book_Details = Book_Details::findOrFail($Book_DetailsId);
        $Book_Details->update($request->validated());
        return ResponseHelper::success('Data returned successfully', new Book_DetailsResource($Book_Details));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $Book_DetailsId)
    {
        $Book_Details = Book_Details::findOrFail($Book_DetailsId);
        $Book_Details->delete();
        return ResponseHelper::success('Data returned successfully', []);
    }

    //  Returns: book
    //  Accessable: by user and admin role
    public function getBookForDetails(int $Book_DetailsId)
    {
        $book = Book_Details::findOrFail($Book_DetailsId)->book;
        return ResponseHelper::success('Data returned successfully', $book);
    }

    //  Returns: books between 2 dates
    //  Accessable: by user and admin role
    public function search($startDate, $endDate)
    {
        $books = Book_Details::with('book')->whereBetween('publication_date', [$startDate, $endDate])
            ->get();
        return ResponseHelper::success('Data returned successfully', $books);
    }
}
