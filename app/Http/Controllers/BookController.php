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
use Faker\Provider\HtmlLorem;
use Faker\Provider\Lorem;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as PhpWordIOFactory;


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
        try {
            $books = Book::findOrFail($BookId);
            return ResponseHelper::success('Data returned successfully', $books);
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
            return ResponseHelper::success('Data updated successfully', new BookResource($Book));
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
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
            return ResponseHelper::success('Data deleated successfuly', []);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
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
            return ResponseHelper::success('Data returned successfully', $book_details);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
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
            return ResponseHelper::success('Data returned successfully', $auther);
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
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
            return ResponseHelper::success('Data returned successfully', new BookDetailsResource($bookData));
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
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
            return ResponseHelper::success('Data returned successfully', BookDetailsResource::collection($booksData));
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
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
            if (!$book->categories()->where('category_id', $request->category_id)->exists()) {
                $book->categories()->attach($request->category_id);
                return ResponseHelper::success('Attached successfully', $book);
            } else {
                return ResponseHelper::error('The category is already attached to this book.', [], 409);
            }
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
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
            return ResponseHelper::success('Data return success',  CategoryResource::collection($categories));
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
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
            return ResponseHelper::success('Data return success', BookDetailsResource::collection($book));
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error('Not Found', [], 404);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), [], 500);
        }
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
                try {
                    $pdf = $pdfParser->parseFile($filePath);
                    $content = $pdf->getText();
                    return $this->textToSpeech($content);
                } catch (\Exception $e) {
                    return response()->json(['message' => 'Error parsing PDF: ' . $e->getMessage()], 500);
                }
            } else {
                return response()->json(['message' => 'File not found: ' . $filePath], 404);
            }
        } elseif ($book->file_type === 'doc' || $book->file_type === 'docx') {
            //  Analytic DOC and DOCX  
            try {
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
                // return response()->json(['content' => $content], 200);
                return $this->textToSpeech($content);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error parsing DOC/DOCX: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'Unsupported file type: ' . $book->file_type], 400);
    }

    public function textToSpeech($text)
    {
        
        if (empty($text)) {
            return response()->json(['message' => 'No text provided for conversion.'], 400);
        }

        // نصائح لتجنب مشاكل الترميز، خاصة إذا كان هناك أحرف خاصة  
        $text = escapeshellarg($text);
        $audioFilePath = storage_path('app/public/audio/') . time() . '_output.wav';
        return response()->json($audioFilePath, 200);
        
    //     // تأكد من وجود المجلد  
    //     $audioDir = dirname($audioFilePath);
    //     if (!file_exists($audioDir)) {
    //         mkdir($audioDir, 0777, true); // انشئ المجلد إذا لم يكن موجوداً  
    //     }

    //     // استخدام eSpeak مع المسار الصحيح  
    //     $command = '"C:\\Program Files (x86)\\eSpeak\\command_line\\espeak.exe" -w ' . $audioFilePath . ' ' . $text;
    //     exec($command, $output, $returnVar);

    //     // التحقق من نتيجة الأمر  
    //     if ($returnVar !== 0) {
    //         // قم بتسجيل مخرجات الأمر  
    //         Log::error('Audio generation command failed: ' . implode("\n", $output));
    //         return response()->json(['message' => 'Error generating audio: ' . implode("\n", $output)], 500);
    //     }

    //     Log::info('Audio generated successfully: ' . $audioFilePath);
    //     return response()->json(['message' => 'Audio generated successfully.', 'audio_file' => $audioFilePath], 200);
     }
}
