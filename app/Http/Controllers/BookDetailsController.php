<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
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
        return ResponseHelper::success('Data returned successfully', Book_DetailsResource::collection($details));
    }

    //  Returns: book details
    //  Accessable: by user and admin role
    public function viewBookDetails($Book_DetailsId)
    {
        try {
            $details = Book_Details::findOrFail($Book_DetailsId);
            return ResponseHelper::success('Data returned successfully', new Book_DetailsResource($details));
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }

    //  Returns: Book_Details
    //  Accessable: by admin role
    public function storeBookDetails(StoreDetailsRequest $request)
    {
        $Book_Details = Book_Details::create($request->validated());
        return ResponseHelper::success('Data returned successfully', new Book_DetailsResource($Book_Details));
    }

    //  Returns: Book_Details
    //  Accessable: by admin role
    public function updateBookDetails(UpdateDetailsRequest $request, int $Book_DetailsId)
    {
        try {
            $Book_Details = Book_Details::findOrFail($Book_DetailsId);
            $Book_Details->update($request->validated());
            return ResponseHelper::success('Data returned successfully', new Book_DetailsResource($Book_Details));
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }

    //  Returns: nothing
    //  Accessable: by admin role
    public function destroyBookDetails(int $Book_DetailsId)
    {
        try {
            $Book_Details = Book_Details::findOrFail($Book_DetailsId);
            $Book_Details->delete();
            return ResponseHelper::success('Data returned successfully', []);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }

    //  Returns: book
    //  Accessable: by user and admin role
    public function getBookForDetails(int $Book_DetailsId)
    {
        try {
            $book = Book_Details::findOrFail($Book_DetailsId)->book;
            return ResponseHelper::success('Data returned successfully', $book);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
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
