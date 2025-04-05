<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDetailsRequest;
use App\Http\Requests\UpdateDetailsRequest;
use App\Http\Requests\UpdateNameRequest;
use App\Http\Resources\Book_DetailsResource;
use Illuminate\Http\Request;
use App\Models\Book_Details;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BookDetailsController extends Controller
{

    //  Returns: books details
    //  Accessable: by user and admin role
    public function viewBooksDetails()
    {
        $details = Book_Details::all();
        return response()->json([
            'message' => 'details for this book are:',
            'details' => Book_DetailsResource::collection($details)
        ], 200);
    }
    //  Returns: book details
    //  Accessable: by user and admin role
    public function viewBookDetails($Book_DetailsId)
    {
        try {
            $details = Book_Details::findOrFail($Book_DetailsId);
            return response()->json([
                'message' => 'details for this book are:',
                'details' => new Book_DetailsResource($details)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Details not found',
                'details' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'something went wrong',
                'details' => $e->getMessage()
            ], 404);
        }
    }


    //  Returns: Book_Details
    //  Accessable: by admin role
    public function storeBookDetails(StoreDetailsRequest $request)
    {
        $Book_Details = Book_Details::create($request->validated());
        return response()->json([
            'message' => 'Book_Details created successfully',
            'Book_Details' => new Book_DetailsResource($Book_Details)
        ], 201);
    }


    //  Returns: Book_Details
    //  Accessable: by admin role
    public function updateBookDetails(UpdateDetailsRequest $request, int $Book_DetailsId)
    {
        try {
            $Book_Details = Book_Details::findOrFail($Book_DetailsId);
            $Book_Details->update($request->validated());
            return response()->json([
                'message' => 'Book_Details updated successfully',
                'details' => new Book_DetailsResource($Book_Details)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Details not found',
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
    public function destroyBookDetails(int $Book_DetailsId)
    {
        try {
            $Book_Details = Book_Details::findOrFail($Book_DetailsId);
            $Book_Details->delete();
            return response()->json([], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Details not found',
                'details' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'something went wrong',
                'details' => $e->getMessage()
            ], 404);
        }
    }

    //  Returns: book
    //  Accessable: by user and admin role
    public function getBookForDetails(int $Book_DetailsId)
    {
        try {
            $book = Book_Details::findOrFail($Book_DetailsId)->book;
            return response()->json($book, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Details not found',
                'details' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'something went wrong',
                'details' => $e->getMessage()
            ], 404);
        }
    }


    //  Returns: books between 2 dates
    //  Accessable: by user and admin role
    public function search($startDate, $endDate)
    {
        $books = Book_Details::with('book')->whereBetween('publication_date', [$startDate, $endDate])
            ->get();
        return response()->json($books, 200);
    }
}
