<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Comment as ModelsComment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Http\Resources\CommentResource;
use Exception;

class CommentController extends Controller
{
    //  Returns: comment
    //  Accessable: by user and admin role
    public function addUserCommentOnBook(int $bookId, Request $request)
    {

        $userId = Auth::user()->id;
        try {
            Book::findOrFail($bookId);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Book not found'
            ], 200);
        }

        $validated = $request->validate([
            'comment_text' => 'required|string|max:255',
        ]);
        $comment = Comment::create([
            'user_id' => $userId,
            'book_id' => $bookId,
            'comment_text' => $validated['comment_text']
        ]);
        return response()->json([
            'message' => 'comment added successfully',
            'comment' => new CommentResource($comment),
        ], 201);
    }

    //  Returns: comments on specifi book
    //  Accessable: by user and admin role
    public function getCommentOnBook(int $bookId)
    {
        $comments = Book::findOrFail($bookId)->comments()->orderby('created_at', 'desc')->get();
        return response()->json([
            'message' => 'all comments',
            'comment' => CommentResource::collection($comments),
        ], 201);
    }

    public function removeCommentOnBook(int $bookId, Request $request)
    {
        try {
            $userId = Auth::user()->id;
            $userRole = Auth::user()->role;
            $commentId = $request->id;

            //search on book

            $book = Book::findOrFail($bookId);

            //search on speific comment
            $comment = $book->comments()->findOrFail($commentId);

            if ($userRole === 'user') {
                if ($comment->user_id !== $userId)
                    return response()->json([
                        'message' => 'unautherized'
                    ], 403);

                $comment->delete();
                return response()->json([], 204);
            } else {
                $comment->delete();
                return response()->json([], 204);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Comment not found',
                'details' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'something went wrong',
                'details' => $e->getMessage()
            ], 404);
        }
    }


    //  Returns: comment on specifi book
    //  Accessable: by user and admin role
    public function updateCommentOnBook(int $bookId, Request $request)
    {
        try {
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
                return response()->json([
                    'message' => 'unautherized'
                ], 403);
            $comment->update($validated);
            return response()->json([
                'message' => 'your comment on ' . $book->title . ' updated successfully',
                'comment' => $comment
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => ' not found',
                'details' => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'something went wrong',
                'details' => $e->getMessage()
            ], 404);
        }
    }
}
