<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Audio;
use App\Models\User;

class FavoriteController extends Controller
{
    public function getUserFavorites(Request $request) {
        $user_id    = $request->user()->id;
        $favorites  = Favorite::where('user_id', $user_id)->with('audio')->get();

        foreach ($favorites as $favorite) {
            $favorite->audio->user              = User::where('id', $favorite->audio->user_id)->firstOrFail();
            $favorite->audio->favorite_boolean = true;
        }

        return response($favorites);
    }

    public function putFavorite(Request $request, $audio_id = null) {
        $user_id = $request->user()->id;

        $favorite = Favorite::create([
            'audio_id'  => $audio_id,
            'user_id'   => $user_id
        ]);

        Audio::find($audio_id)->increment('likes');

        return response($favorite);
    }

    public function deleteFavorite(Request $request, $audio_id = null) {
        $user_id = $request->user()->id;

        Favorite::where('audio_id', $audio_id)->where('user_id', $user_id)->delete();
        Audio::find($audio_id)->decrement('likes');

        return response(204);
    }
}
