<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\response;
use App\Http\Resources\Book_DetailsResource;
use App\Http\Resources\BookDetailsResource;
use App\Http\Resources\CategoryResource;
use App\Mail\NewBookNotification;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookController extends Controller
{
    //  Returns: all books
    //  Accessable: by user and admin role
    public function viewBooks()
    {
        $books = Book::all();
        // return response()->json([
        //     'message' => 'books in our system are:',
        //     'books' => BookResource::collection($books)
        // ], 200);

        return ResponseHelper::success('Data returen Suceess', $books);
    }


    //  Returns:  book
    //  Accessable: by user and admin role
    public function viewbook($BookId)
    {
        try {
            $books = Book::findOrFail($BookId);
            return ResponseHelper::success('Data returen Suceess', $books);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
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
            // $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $extension = $file->getClientOriginalExtension();
            //dealing with files(PDF,DOC)
            if ($request->hasFile('file')) {
                // get file extension
                $file = $request->file('file');
                $extension = $file->getClientOriginalExtension();
                //check the extension
                if ($extension === 'pdf') {
                    $fpath = $file->store('pdfs', 'public');
                    $validated['pdf_link'] = $fpath;
                    $validated['doc_link'] = null;
                } elseif ($extension === 'doc' || $extension === 'docx') {
                    $fpath = $file->store('docs', 'public');
                    $validated['doc_link'] = $fpath;
                    $validated['pdf_link'] = null;
                } else {
                    return ResponseHelper::error('Unsupported file type. Only PDF and DOC files are allowed', [], 400);
                }
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
            return ResponseHelper::success('Data returen Suceess', new BookResource($Book));
        }
    }
    // public function storeBook(StoreBookRequest $request)
    // {
    //     $validated = $request->validated();

    //     //dealing with images
    //     if ($request->hasFile('image')) {
    //         $path = $request->file('image')->store('my book photo', 'public');
    //         $validated['image'] = $path;
    //     }

    //     //dealing with files(PDF,DOC)
    //     if ($request->hasFile('file')) {
    //         // get file extension
    //         $file = $request->file('file');
    //         $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
    //         $extension = $file->getClientOriginalExtension();

    //         //check the extension
    //         if ($extension === 'pdf') {
    //             $fpath = 'storage/app/public/files/pdfs/' . $fileName;
    //             // $file->move(storage_path('app/public/uploads/pdfs'), $fileName);
    //             // $validated['pdf_link'] = $path;
    //             // $validated['doc_link'] = null;
    //         } elseif (in_array($extension, ['doc', 'docx'])) {
    //             $fpath = 'storage/app/public/files/docs/' . $fileName;
    //             // $file->move(storage_path('app/public/files/docs'), $fileName);
    //             // $validated['doc_link'] = $path;
    //             // $validated['pdf_link'] = null;
    //         } else {
    //             return response()->json([
    //                 'message' => 'Unsupported file type. Only PDF and DOC files are allowed.'
    //             ], 400);
    //         }
    //     }

    //     $bookExists  = Book::where('title', $request->title)
    //         ->where('auther_id', $request->auther_id)
    //         ->exists();
    //     if ($bookExists) {
    //         return response()->json([
    //             'message' => 'this book is already exists'
    //         ], 200);
    //     }
    //     $Book = Book::create($validated);
    //     $users = User::where('notify_new_books', 1)->get();
    //     foreach ($users as $user) {
    //         if (!empty($user->email)) {
    //             Mail::to($user->email)->send(new NewBookNotification($Book));
    //         } else {
    //             return response()->json('empty', 200);
    //         }
    //     }
    //     return response()->json([
    //         'message' => 'Book created successfully',
    //         'path'=>$fpath
    //         // 'Book' => new BookResource($Book)
    //     ], 201);
    // }


    //  Returns: Book
    //  Accessable: by admin role
    public function updateBook(UpdateBookRequest $request, int $BookId)
    {
        try {
            $Book = Book::findOrFail($BookId);
            $validated = $request->validated();
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('my book photo', 'public');
                $validated['image'] = $path;
            }
            $Book->update($validated);
            return ResponseHelper::success('Data returened Suceess', new BookResource($Book));
        } catch (ModelNotFoundException $e) {

            return ResponseHelper::error($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }


    //  Returns: nothing
    //  Accessable: by admin role
    public function destroyBook(int $BookId)
    {
        try {
            $Book = Book::findOrFail($BookId);
            $Book->delete();
            return ResponseHelper::success('Data deleated  Suceessfuly', []);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }


    //  Returns: book details
    //  Accessable: by user and admin role
    public function getBookDetails($bookId)
    {
        try {
            $book_details = Book::findOrFail($bookId)->details;
            return ResponseHelper::success('Data returen Suceess', $book_details);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }


    //  Returns: auther for certain book
    //  Accessable: by user and admin role
    public function getAutherForBook(int $bookId)
    {
        try {
            $auther = Book::findOrFail($bookId)->auther;
            return ResponseHelper::success('Data returen Suceess', $auther);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }


    //  Returns:  book with details
    //  Accessable: by user and admin role
    public function getBookwithDetails($bookId)
    {
        try {
            $bookData = Book::with('details')->findOrFail($bookId);
            return response()->json(new BookDetailsResource($bookData), 200);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }

    //  Returns: all books with details for each book
    //  Accessable: by user and admin role
    public function getBookswithDetails()
    {
        try {
            $booksData = Book::with('details')->get();
            return response()->json(BookDetailsResource::collection($booksData), 200);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }


    //  Returns: attached successfully
    //  Accessable: by admin role
    public function storeBookInCategory(Request $request, int $bookId)
    {
        try {
            $book = Book::findOrFail($bookId);
            $book->categories()->attach($request->category_id);
            return response()->json('attached successfully', 201);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }


    //  Returns: get all cartegories related for certain book
    //  Accessable: by user and admin role
    public function getCategoriesForBook(int $bookId)
    {
        try {
            $book = Book::findOrFail($bookId);
            $categories = Book::findOrFail($bookId)->categories;
            return response()->json([
                'book_name' => $book->title,
                'Categories' => CategoryResource::collection($categories)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }


    //  Returns: all books with inputing key
    //  Accessable: by user and admin role
    public function search(string $key)
    {
        try {
            $book = Book::with('details')->where('title', 'LIKE', '%' . $key . '%')->get();
            return response()->json([
                'message' => 'the book is:',
                'book' => BookDetailsResource::collection($book)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error($e->getMessage(), [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
    }
}
