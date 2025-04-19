<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutherController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookDetailsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\YourController;
use DeepCopy\Filter\Filter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Authentication
Route::post('sign_up', [AuthController::class, 'signUp']);
Route::post('sign_in', [AuthController::class, 'signIn']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('sign_out', [AuthController::class, 'signOut']);

    //Auther
    Route::get('view_authers', [AutherController::class, 'index']); //admin and user
    Route::get('view_books_for_auther/{id}', [AutherController::class, 'getBooksForAuther']); //admin and user
    Route::get('view_autherInfo_with_related_books/{id}', [AutherController::class, 'autherInfoWithRelatedBooks']); //admin and user
    Route::get('search/{autherName}/auther', [AutherController::class, 'search']); //admin and user

    //Category
    Route::get('view_categories', [CategoryController::class, 'index']); //admin and user
    Route::get('view_books_in_category/{id}', [CategoryController::class, 'getBooksInCategoriy']); //admin and user
    Route::get('search/{categoryName}/category', [CategoryController::class, 'search']); //admin and user

    //Book
    Route::get('view_books', [BookController::class, 'index']); //admin and user
    Route::get('view_book/{id}', [BookController::class, 'show']); //admin and user
    Route::get('view_details_for_certain_book/{id}', [BookController::class, 'getBookDetails']); //admin and user
    Route::get('view_auther_for_book/{id}', [BookController::class, 'getAutherForBook']); //admin and user
    Route::get('view_book_with_details/{id}', [BookController::class, 'getBookwithDetails']); //admin and user
    Route::get('view_books_with_details', [BookController::class, 'getBookswithDetails']); //admin and user
    Route::get('view_categories_for_book/{id}', [BookController::class, 'getCategoriesForBook']); //admin and user
    Route::get('search/{bookKey}/title', [BookController::class, 'search']); //admin and user

    //Book_Details
    Route::get('view_books_details', [BookDetailsController::class, 'index']); //admin and user
    Route::get('view_book_details/{id}', [BookDetailsController::class, 'show']); //admin and user
    Route::get('view_book_for_certain_details/{id}', [BookDetailsController::class, 'getBookForDetails']); //admin and user
    Route::get('search/{startDate}/{endDate}/publiation_date', [BookDetailsController::class, 'search']); //admin and user

    //Comments
    Route::apiResource('comments', CommentController::class); //admin and user
    Route::post('comments/{id}', [CommentController::class, 'store']); //admin and user

    //Ratings
    Route::apiResource('rating', RatingController::class); //admin and user
    Route::post('add_rate_for_book/{bookId}', [RatingController::class, 'store']); //admin and user

    // Filter
    Route::post('filter_books_by_rating', [RatingController::class, 'filterBooksByRating']); //admin and user

    //Admin
    Route::middleware('isAdmin')->group(function () {
        
        //Auther
        Route::apiResource('authers', AutherController::class)->except(['index']);

        //Category
        Route::apiResource('categories', CategoryController::class)->except(['index']);

        //Book
        Route::apiResource('books', BookController::class)->except(['index', 'show']);
        Route::post('book/{bookId}/category', [BookController::class, 'storeBookInCategory']); //admin

        //Book_Details
        Route::apiResource('details', BookDetailsController::class)->except(['index', 'show']);
    });
});
Route::get('/bookContent/{bookId}', [BookController::class, 'getContent']);

