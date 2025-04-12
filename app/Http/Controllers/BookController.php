<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\BookDetailsResource;
use App\Http\Resources\CategoryResource;
use App\Mail\NewBookNotification;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookController extends Controller
{
    //  Returns: all books
    //  Accessable: by user and admin role
    public function viewBooks()
    {
        $books = Book::all();
        return ResponseHelper::success('Data returned successfully', $books);
    }

    //  Returns:  book
    //  Accessable: by user and admin role
    public function viewbook($BookId)
    {

        $books = Book::findOrFail($BookId);
        return ResponseHelper::success('Data returned successfully', $books);
    }

    //  Returns: Book
    //  Accessable: by admin role
    public function storeBook(StoreBookRequest $request)
    {
        $validated = $request->validated();
        //dealing with images
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('my book photo', 'public');
            $validated['image'] = $path;
        }
        //dealing with files(PDF,DOC)
        if ($request->hasFile('file')) {
            // get file extension
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            //check the extension
            if ($extension === 'pdf') {
                $path = $file->store('pdfs', 'public');
                $validated['file'] = $path;
            } elseif ($extension === 'doc' || $extension === 'docx') {
                $path = $file->store('docs', 'public');
                $validated['file'] = $path;
            } else {
                return ResponseHelper::error('Unsupported file type. Only PDF and DOC files are allowed', [], 400);
            }
            $bookExists  = Book::where('title', $request->title)
                ->where('auther_id', $request->auther_id)
                ->exists();
            if ($bookExists) {
                return ResponseHelper::error('this book is already exists', [], 301);
            }
            $Book = Book::create($validated);
            $users = User::where('notify_new_books', 1)->get();
            foreach ($users as $user) {
                if (!empty($user->email)) {
                    Mail::to($user->email)->send(new NewBookNotification($Book));
                } else {
                    return ResponseHelper::error('empty', [], 301);
                }
            }
            return ResponseHelper::success('Data returned successfully', new BookResource($Book));
        }
    }

    //  Returns: Book
    //  Accessable: by admin role
    public function updateBook(UpdateBookRequest $request, int $BookId)
    {
        $Book = Book::findOrFail($BookId);
        $validated = $request->validated();
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('my book photo', 'public');
            $validated['image'] = $path;
        }
        $Book->update($validated);
        return ResponseHelper::success('Data updated successfully', new BookResource($Book));
    }

    //  Returns: nothing
    //  Accessable: by admin role
    public function destroyBook(int $BookId)
    {
        $Book = Book::findOrFail($BookId);
        $Book->delete();
        return ResponseHelper::success('Data deleated successfuly', []);
    }

    //  Returns: book details
    //  Accessable: by user and admin role
    public function getBookDetails($bookId)
    {
        $book_details = Book::findOrFail($bookId)->details;
        return ResponseHelper::success('Data returned successfully', $book_details);
    }

    //  Returns: auther for certain book
    //  Accessable: by user and admin role
    public function getAutherForBook(int $bookId)
    {

        $auther = Book::findOrFail($bookId)->auther;
        return ResponseHelper::success('Data returned successfully', $auther);
    }

    //  Returns:  book with details
    //  Accessable: by user and admin role
    public function getBookwithDetails($bookId)
    {
        $bookData = Book::with('details')->findOrFail($bookId);
        return ResponseHelper::success('Data returned successfully', new BookDetailsResource($bookData));
    }

    //  Returns: all books with details for each book
    //  Accessable: by user and admin role
    public function getBookswithDetails()
    {
        $booksData = Book::with('details')->get();
        return ResponseHelper::success('Data returned successfully', BookDetailsResource::collection($booksData));
    }

    //  Returns: attached successfully
    //  Accessable: by admin role
    public function storeBookInCategory(Request $request, int $bookId)
    {
        $book = Book::findOrFail($bookId);
        if (!$book->categories()->where('category_id', $request->category_id)->exists()) {
            $book->categories()->attach($request->category_id);
            return ResponseHelper::success('Attached successfully', $book);
        } else {
            return ResponseHelper::error('The category is already attached to this book.', [], 409);
        }
    }

    //  Returns: get all cartegories related for certain book
    //  Accessable: by user and admin role
    public function getCategoriesForBook(int $bookId)
    {
        $book = Book::findOrFail($bookId);
        $categories = Book::findOrFail($bookId)->categories;
        return ResponseHelper::success('Data return success',  CategoryResource::collection($categories));
    }

    //  Returns: all books with inputing key
    //  Accessable: by user and admin role
    public function search(string $key)
    {

        $book = Book::with('details')->where('title', 'LIKE', '%' . $key . '%')->get();
        return ResponseHelper::success('Data return success', BookDetailsResource::collection($book));
    }
}
