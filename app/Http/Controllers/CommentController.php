<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function postComment(Request $request, $audio_id = null) {
        $message = $request->message;
        $user    = $request->user();
        $user_id = $user->id;

        $comment = Comment::create([
            'audio_id'  => $audio_id,
            'user_id'   => $user_id,
            'message'   => $message
        ]);

        $response = [
            'audio_id'      => $audio_id,
            'user_id'       => $user_id,
            'message'       => $message,
            'user'          => $user,
            'created_at'    => $comment->created_at,
            'updated_at'    => $comment->updated_at,
            'id'            => $comment->id
        ];

        return response($response, 201);
    }

    public function getAllComments($audio_id = null) {
        $comments = Comment::where('audio_id', $audio_id)->with('user')->get();

        return response($comments, 200);
    }

    public function deleteComment($id) {
        Comment::where('id', $id)->delete();

        return response(204);
    }
}
