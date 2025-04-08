<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Book;
use App\Models\Rating;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    //  Returns: rating
    //  Accessable: by user and admin role
    public function addRateForBook(int $bookId, Request $request)
    {
        try {
            $userId = Auth::user()->id;
            try {
                Book::findOrFail($bookId);
            } catch (ModelNotFoundException $e) {
                return ResponseHelper::error('Not Found', [], 404);
            }
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
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 404);
        }
    }

    //  Returns: nothing
    //  Accessable: by user and admin role
    public function updateRateForBook(int $bookId, Request $request)
    {
        try {
            $userId = Auth::user()->id;
            try {
                $book = Book::findOrFail($bookId);
            } catch (ModelNotFoundException $e) {
                return ResponseHelper::error('Not Found', [], 404);
            }

            $rating = Rating::where('user_id', $userId)->where('book_id', $bookId)->first();
            $validated = $request->validate([
                'rating' => 'required|in:1,2,3,4,5'
            ]);
            if ($rating->user_id !== $userId)
                return ResponseHelper::error('unautherized', [], 403);

            $rating->update($validated);
            return ResponseHelper::success('Data updated successfully', []);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 404);
        }
    }

    //  Returns: nothing
    //  Accessable: by user and admin role
    public function removeBookRating(int $bookId)
    {
        try {
            $userId = Auth::user()->id;
            try {
                $book = Book::findOrFail($bookId);
            } catch (ModelNotFoundException $e) {
                return ResponseHelper::error('Not Found', [], 404);
            }
            $rating = Rating::where('user_id', $userId)->where('book_id', $bookId)->first();
            if ($rating->user_id !== $userId)
                return ResponseHelper::error('unautherized', [], 403);

            $rating->delete();
            return ResponseHelper::success('Data deleted successfully', []);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }

    //  Returns: book rating
    //  Accessable: by user and admin role
    public function getBookRating(int $bookId)
    {
        try {
            $averageRating = Book::findOrFail($bookId)->ratings()->avg('rating');

            $number = $this->RateBook($averageRating);
            return ResponseHelper::success('Data deleted successfully', [$averageRating, $this->getStarRating($number)]);
            // return response()->json([
            //     'average' => $averageRating,
            //     'rating' => $this->getStarRating($number)
            // ], 200);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
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
        // return response()->json([
        //     'message' => 'Books with ' . $targetRating . ' rating',
        //     'result' => $filteredBooks
        // ], 200);
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
