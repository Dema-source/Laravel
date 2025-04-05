<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutherController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookDetailsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RatingController;
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
    Route::get('view_authers', [AutherController::class, 'viewAuthers']); //admin and user
    Route::get('view_books_for_auther/{id}', [AutherController::class, 'getBooksForAuther']); //admin and user
    Route::get('view_autherInfo_with_related_books/{id}', [AutherController::class, 'autherInfoWithRelatedBooks']); //admin and user

    //Category
    Route::get('view_categories', [CategoryController::class, 'viewCategories']); //admin and user

    //Book
    Route::get('view_books', [BookController::class, 'viewBooks']); //admin and user
    Route::get('view_book/{id}', [BookController::class, 'viewBook']); //admin and user
    Route::get('view_details_for_certain_book/{id}', [BookController::class, 'getBookDetails']); //admin and user
    Route::get('view_auther_for_book/{id}', [BookController::class, 'getAutherForBook']); //admin and user
    Route::get('view_book_with_details/{id}', [BookController::class, 'getBookwithDetails']); //admin and user
    Route::get('view_books_with_details', [BookController::class, 'getBookswithDetails']); //admin and user

    //Book_Details
    Route::get('view_books_details', [BookDetailsController::class, 'viewBooksDetails']); //admin and user
    Route::get('view_book_details/{id}', [BookDetailsController::class, 'viewBookDetails']); //admin and user
    Route::get('view_book_for_certain_details/{id}', [BookDetailsController::class, 'getBookForDetails']); //admin and user

    //Admin
    Route::middleware('isAdmin')->group(function () {

        //Auther
        Route::post('store_auther', [AutherController::class, 'storeAuther']); //admin
        Route::put('update_auther/{id}', [AutherController::class, 'updateAuther']); //admin
        Route::delete('destroy_auther/{id}', [AutherController::class, 'destroyAuther']); //admin

        //Category
        Route::post('store_category', [CategoryController::class, 'storeCategory']); //admin
        Route::put('update_category/{id}', [CategoryController::class, 'updateCategory']); //admin
        Route::delete('destroy_category/{id}', [CategoryController::class, 'destroyCategory']); //admin
        Route::get('view_books_in_category/{id}', [CategoryController::class, 'getBooksInCategoriy']); //admin and user

        //Book
        Route::post('store_book', [BookController::class, 'storeBook']); //admin
        Route::put('update_book/{id}', [BookController::class, 'updateBook']); //admin
        Route::delete('destroy_book/{id}', [BookController::class, 'destroyBook']); //admin
        Route::post('add_book_to_category/{id}', [BookController::class, 'storeBookInCategory']); //admin
        Route::get('view_categories_for_book/{id}', [BookController::class, 'getCategoriesForBook']); //admin and user

        //Book_Details
        Route::post('store_book_details', [BookDetailsController::class, 'storeBookDetails']); //admin
        Route::put('update_book_details/{id}', [BookDetailsController::class, 'updateBookDetails']); //admin
        Route::delete('destroy_book_details/{id}', [BookDetailsController::class, 'destroyBookDetails']); //admin

    });


    //Search
    Route::get('search_accordingTo_bookTitle/{key}', [BookController::class, 'search']); //admin and user
    Route::get('search_accordingTo_autherName/{key}', [AutherController::class, 'search']); //admin and user
    Route::get('search_accordingTo_category/{key}', [CategoryController::class, 'search']); //admin and user
    Route::get('search_accordingTo_publiation_date/{startDate}/{endDate}', [BookDetailsController::class, 'search']); //admin and user


    //Comments
    Route::post('store_comment/{bookId}', [CommentController::class, 'addUserCommentOnBook']); //admin and user
    Route::get('get_all_comments_on_specific_book/{bookId}', [CommentController::class, 'getCommentOnBook']); //admin and user
    Route::put('update_comment_on_book/{bookId}', [CommentController::class, 'updateCommentOnBook']); //admin and user
    Route::delete('remove_comment/{bookId}', [CommentController::class, 'removeCommentOnBook']); //admin and user


    //Ratings
    Route::post('add_rate_for_book/{bookId}', [RatingController::class, 'addRateForBook']); //admin and user
    Route::put('update_rate_for_book/{bookId}', [RatingController::class, 'updateRateForBook']); //admin and user
    Route::get('get_book_rating/{bookId}', [RatingController::class, 'getBookRating']); //admin and user
    Route::delete('delete_book_rating/{bookId}', [RatingController::class, 'removeBookRating']); //admin and user


    // Filter
    Route::post('filter_books_by_rating', [RatingController::class, 'filterBooksByRating']); //admin and user

});

//Google Drive API (OAuth)

// Route لتوجيه المستخدم إلى صفحة تسجيل الدخول إلى Google
Route::get('/login', [YourController::class, 'login']);

// Route بعد المصادقة
Route::get('/callback', [YourController::class, 'callback']);

// Route لتحميل الملفات
Route::post('/upload', [YourController::class, 'upload']);
