<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\CommentResource;
use App\Models\Book;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
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
        Book::findOrFail($bookId);
        $validated = $request->validate([
            'comment_text' => 'required|string|max:255',
        ]);
        $comment = Comment::create([
            'user_id' => $userId,
            'book_id' => $bookId,
            'comment_text' => $validated['comment_text']
        ]);
        return ResponseHelper::success('Data return success', new CommentResource($comment));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $bookId)
    {
        $comments = Book::findOrFail($bookId)->comments()->orderby('created_at', 'desc')->get();
        return ResponseHelper::success('Data returned successfully', CommentResource::collection($comments));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $bookId)
    {
        $userId = Auth::user()->id;
        $commentId = $request->id;
        $validated = $request->validate([
            'comment_text' => 'required|string|max:255'
        ]);
        //search on book
        $book = Book::findOrFail($bookId);
        //search on speific comment
        $comment = $book->comments()->findOrFail($commentId);
        if ($comment->user_id !== $userId)
            return ResponseHelper::error('unautherized', [], 403);
        $comment->update($validated);
        return ResponseHelper::success('Data returned successfully', $comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $bookId)
    {
        $userId = Auth::user()->id;
        $userRole = Auth::user()->role;
        $commentId = $request->id;

        //search on book
        $book = Book::findOrFail($bookId);

        //search on speific comment
        $comment = $book->comments()->findOrFail($commentId);

        if ($userRole === 'user') {
            if ($comment->user_id !== $userId)
                return ResponseHelper::error('unautherized', [], 403);
            $comment->delete();
            return ResponseHelper::success('Data deleted successfully', []);
        } else {
            $comment->delete();
            return ResponseHelper::success('Data deleted successfully', []);
        }
    }
}

