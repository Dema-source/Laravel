<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\BookRequest;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as PhpWordIOFactory;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::all();
        return ResponseHelper::success('Data returned successfully', $books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request)
    {
        $validated = $request->validated();
        //dealing with images
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('my book photo', 'public');
            $validated['image'] = $path;
        }
        //dealing with files(PDF,DOC)
        if ($request->hasFile('file_path')) {
            // get file extension
            $file = $request->file('file_path');
            $extension = $file->getClientOriginalExtension();
            //check the extension
            if ($extension === 'pdf') {
                $path = $file->store('pdfs', 'public');
            } elseif ($extension === 'doc' || $extension === 'docx') {
                $path = $file->store('docs', 'public');
            } else {
                return ResponseHelper::error('Unsupported file type. Only PDF and DOC files are allowed', [], 400);
            }
            $bookExists  = Book::where('title', $request->title)
                ->where('auther_id', $request->auther_id)
                ->exists();
            if ($bookExists) {
                return ResponseHelper::error('this book is already exists', [], 301);
            }
            $validated['file_path'] = $path;
            $validated['file_type'] = $extension;
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

    /**
     * Display the specified resource.
     */
    public function show($BookId)
    {
        $books = Book::findOrFail($BookId);
        return ResponseHelper::success('Data returned successfully', $books);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookRequest $request, int $BookId)
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $BookId)
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

    public function getContent($bookId)
    {
        $book = Book::findOrFail($bookId);
        $filePath = storage_path('app/public/' . $book->file_path);
        error_log("File path for PDF: " . $filePath);

        if ($book->file_type === 'pdf') {
            //reading pdf's content
            $pdfParser = new pdfParser();
            if ($filePath) {

                $pdf = $pdfParser->parseFile($filePath);
                $content = $pdf->getText();
                // $words = explode(' ', $content);
                $sentences = preg_split('/(?<=[.!?])\s+/', $content);
                return view('display', ['sentences' => $sentences]);
            } elseif ($book->file_type === 'doc' || $book->file_type === 'docx') {
                //  Analytic DOC and DOCX  
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
                $content = '';
                // Extract text from each section 
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                            foreach ($element->getElements() as $text) {
                                if ($text instanceof \PhpOffice\PhpWord\Element\Text) {
                                    $content .= $text->getText() . "\n";
                                }
                            }
                        }
                    }
                }
                $sentences = preg_split('/(?<=[.!?])\s+/', $content);
                return view('display', ['sentences' => $sentences]);
            }
        }
        return response()->json(['message' => 'Unsupported file type: ' . $book->file_type], 400);
    }
}
