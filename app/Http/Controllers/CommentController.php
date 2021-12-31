<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function postComment(Request $request, $audio_id = null) {
        $message    = $request->message;
        $user       = $request->user();
        $user_id    = $user->id;
        $parent_id  = $request->get('parent_id', null);

        $comment = Comment::create([
            'audio_id'  => $audio_id,
            'user_id'   => $user_id,
            'message'   => $message,
            'parent_id' => $parent_id
        ]);

        $response = [
            'audio_id'      => $audio_id,
            'user_id'       => $user_id,
            'message'       => $message,
            'user'          => $user,
            'created_at'    => $comment->created_at,
            'updated_at'    => $comment->updated_at,
            'id'            => $comment->id,
            'replies'       => []
        ];

        return response($response, 201);
    }

    public function getAllComments($audio_id = null) {
        $comments = Comment::where('audio_id', $audio_id)
            ->whereNull('parent_id')
            ->with('user', 'replies.user')
            ->get();

        return response($comments, 200);
    }

    public function deleteComment($id) {
        Comment::where('id', $id)->delete();
        Comment::where('parent_id', $id)->delete();

        return response(204);
    }
}
