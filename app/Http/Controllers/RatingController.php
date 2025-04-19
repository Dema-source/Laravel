<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Book;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, int $bookId)
    {
        $userId = Auth::user()->id;
        if (!$userId) {
            return ResponseHelper::error('User is not authenticated', [], 401);
        }
        Book::findOrFail($bookId);
        $request->validate([
            'rating' => 'required|in:1,2,3,4,5'
        ]);
        $existingRating = Rating::where('user_id', $userId)->where('book_id', $bookId)->first();
        if ($existingRating) {
            return ResponseHelper::error('You have already rated this book.', [], 301);
        }
        $rating = Rating::create([
            'user_id' => $userId,
            'book_id' => $bookId,
            'rating' => $request->rating
        ]);
        return ResponseHelper::success('Thank you for participating. I hope you have a good user experience.', $rating);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $bookId)
    {
        $averageRating = Book::findOrFail($bookId)->ratings()->avg('rating');
        $number = $this->RateBook($averageRating);
        return ResponseHelper::success('Data deleted successfully', [$averageRating, $this->getStarRating($number)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $bookId, Request $request)
    {
        $userId = Auth::user()->id;
        $book = Book::findOrFail($bookId);
        $rating = Rating::where('user_id', $userId)->where('book_id', $bookId)->first();
        $validated = $request->validate([
            'rating' => 'required|in:1,2,3,4,5'
        ]);
        if ($rating->user_id !== $userId)
            return ResponseHelper::error('unautherized', [], 403);
        $rating->update($validated);
        return ResponseHelper::success('Data updated successfully', []);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $bookId)
    {
        $userId = Auth::user()->id;
        $book = Book::findOrFail($bookId);
        $rating = Rating::where('user_id', $userId)->where('book_id', $bookId)->first();
        if ($rating->user_id !== $userId)
            return ResponseHelper::error('unautherized', [], 403);
        $rating->delete();
        return ResponseHelper::success('Data deleted successfully', []);
    }

    //  Returns: number of rate
    //  Accessable: by user and admin role
    public function RateBook(float $averageRating)
    {
        if ($averageRating <= 1.9) {
            return 1;
        } elseif ($averageRating >= 2.0 && $averageRating <= 2.9) {
            return 2;
        } elseif ($averageRating >= 3.0 && $averageRating <= 3.9) {
            return 3;
        } elseif ($averageRating >= 4.0 && $averageRating <= 4.9) {
            return 4;
        } else {
            return 5;
        }
    }

    public function getStarRating(int $number)
    {
        switch ($number) {
            case 1:
                return 'One Star';
            case 2:
                return 'Two Stars';
            case 3:
                return 'Three Stars';
            case 4:
                return 'Four Stars';
            case 5:
                return 'Five Stars';
            default:
                return 'No Rating';
        }
    }

    public function filterBooksByRating(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5'
        ]);
        //average book ratings
        $avgResults  = $this->avg();

        //required evaluation values
        $targetRating  = $request->rating;

        //final results matrix
        $filteredBooks = [];

        //filter books based on rating
        foreach ($avgResults as $avgResult) {
            $book_id = $avgResult[0];
            $averageRating = $avgResult[1];
            $book_title = Book::findOrFail($book_id)->title;

            if ($averageRating == $targetRating) {
                $filteredBooks[] = [
                    'book_title' => $book_title
                ];
            }
        }
        return ResponseHelper::success('Books with ' . $targetRating . ' rating', $filteredBooks);
    }


    public function avg()
    {
        //get all book's id
        $booksId = Book::pluck('id')->toArray();

        // remove duplicates
        $uniqueBooksId = array_unique($booksId);

        // sort the matrix in ascending order
        sort($uniqueBooksId);
        $avg_result = [];
        foreach ($uniqueBooksId as $bookId) {
            $averageRating1 = Rating::where('book_id', $bookId)->avg('rating');
            if ($averageRating1 !== null) {
                $averageRating = $this->RateBook($averageRating1);
                $arr = [$bookId, $averageRating];
                array_push($avg_result, $arr);
            }
        }
        return  $avg_result;
    }
}
