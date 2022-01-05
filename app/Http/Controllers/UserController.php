<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class UserController extends Controller
{
    public function getUserByUuid($uuid = null) {
        $user = User::where('uuid', $uuid)
            ->withCount([
                'audio as public_upload_count' => function($query) {
                    $query->where('private', 0);
                },
                'audio as private_upload_count' => function($query) {
                    $query->where('private', 1);
                }
            ])
            ->first();

        return response($user);
    }

    public function uploadUserImage(Request $request) {
        $request->validate([
            'file' => 'required|mimes:jpg,png'
        ]);

        $user_id        = $request->user()->id;
        $file           = $request->file('file');
        $file_name      = $file->hashName();
        $previous_file  = $request->user()->img_path;

        $file->storeAs(null, $file_name, 'images');

        $user = User::where('id', $user_id)->firstOrFail();
        $user->img_path = $file_name;
        $user->save();

        if($previous_file !== 'default.png') {
            Storage::disk('images')->delete($previous_file);
        }

        return response($user);
    }
}
