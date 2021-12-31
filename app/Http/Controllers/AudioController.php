<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Audio;
use App\Models\Favorite;
use App\Models\User;
use App\Models\Comment;

class AudioController extends Controller
{
    public function getAudioFile(Request $request, $uuid = null) {
        $file_data  = Audio::loadData()->where('uuid', $uuid)->firstOrFail();
        $user_id    = $request->user()->id ?? null;
        $audio_id   = $file_data->id;

        if(isset($user_id) && !empty($user_id)) {
            $favorite_boolean = Favorite::where('user_id', $user_id)
                ->where('audio_id', $audio_id)
                ->exists();
        }

        return response([
            'file_data' => $file_data,
            'favorite_boolean' => $favorite_boolean ?? false
        ]);
    }

    public function getAllAudioFiles() {
        $file_data_array = Audio::with('user')->get();

        return response($file_data_array);
    }

    public function getAllUserAudioFiles(Request $request, $user_uuid = null) {
        $user            = User::where('uuid', $user_uuid)->first();
        $user_id         = $user->id;
        $request_user_id = $request->user()->id ?? null;
        $fileQuery       = Audio::query();

        $fileQuery->where('user_id', $user_id)
            ->with('user')
            ->orderBy('created_at', 'desc');

        if($user_id != $request_user_id) {
            $fileQuery->where('private', 0);
        }

        $file_data_array = $fileQuery->get();

        if(isset($request_user_id) && !empty($request_user_id)) {
            foreach ($file_data_array as $file_data) {
                $file_data->favorite_boolean = Favorite::where('user_id', $request_user_id)
                    ->where('audio_id', $file_data->id)
                    ->exists();
            }
        }

        return response($file_data_array);
    }

    public function getRandomAudioFile($curr_uuid = null) {
        $file_data  = Audio::inRandomOrder()
            ->whereNotIn('uuid', [$curr_uuid])
            ->where('private', 0)
            ->first();
        $uuid       = $file_data->uuid;

        return response($uuid);
    }

    public function uploadAudioFile(Request $request) {
        $request->validate([
            'file' => 'required|mimes:mp3'
        ]);

        $user_id        = $request->user()->id;
        $title          = $request->title;
        $description    = $request->description ? $request->description : 'No description given.';
        $private        = $request->private;
        $download       = $request->download;
        $file           = $request->file('file');
        $file_name      = $file->hashName();
        $file_path      = $file_name;
        $uuid           = Str::uuid();

        $file->storeAs(null, $file_name, 'audio');

        $audio = Audio::create([
            'user_id'       => $user_id,
            'title'         => $title,
            'description'   => $description,
            'private'       => ($private == 'true') ? 1 : 0,
            'download'      => ($download == 'true') ? 1 : 0,
            'waveform_path' => null,
            'file_path'     => $file_path,
            'uuid'          => $uuid,
        ]);

        User::where('id', $user_id)->increment('upload_amount');

        return response($audio);
    }

    public function uploadAudioImage(Request $request) {
        $user_id    = $request->user()->id;
        $audio_id   = $request->id;
        $file       = $request->file('file');
        $file_name  = $file->hashName();

        $file->storeAs(null, $file_name, 'waveform_images');

        $audio  = Audio::where('user_id', $user_id)->where('id', $audio_id)->firstOrFail();
        $audio->waveform_path = $file_name;
        $audio->save();
    }

    public function incrementStreams($audio_id = null) {
        Audio::where('id', $audio_id)->increment('streams');

        return response(200);
    }

    public function deleteAudioFile(Request $request, $id = null) {
        $user_id    = $request->user()->id;
        $file_name  = Audio::where('id', $id)->value('file_path');

        Audio::where('id', $id)->delete();
        Comment::where('audio_id', $id)->delete();
        Favorite::where('audio_id', $id)->delete();
        User::where('id', $user_id)->decrement('upload_amount');
        Storage::disk('audio')->delete($file_name);

        return response(204);
    }
}
