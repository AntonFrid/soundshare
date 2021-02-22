<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AudioController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);

    Route::get('user/{uuid}', [UserController::class, 'getUserByUuid']);
    Route::post('user/image', [UserController::class, 'uploadUserImage']);

    Route::get('file/audio/{uuid}', [AudioController::class, 'getAudioFile']);
    Route::delete('file/audio/{id}', [AudioController::class, 'deleteAudioFile']);
    Route::get('file/audio', [AudioController::class, 'getAllAudioFiles']);
    Route::get('user/{user_uuid}/file/audio', [AudioController::class, 'getAllUserAudioFiles']);
    Route::post('file/audio', [AudioController::class, 'uploadAudioFile']);
    Route::get('file/random/audio/{curr_uuid}', [AudioController::class, 'getRandomAudioFile']);
    Route::put('audio/{audio_id}/stream', [AudioController::class, 'incrementStreams']);

    Route::post('audio/{audio_id}/comment', [CommentController::class, 'postComment']);
    Route::get('audio/{audio_id}/comments', [CommentController::class, 'getAllComments']);
    Route::delete('audio/comments/{id}', [CommentController::class, 'deleteComment']);

    Route::put('audio/{audio_id}/favorite', [FavoriteController::class, 'putFavorite']);
    Route::delete('audio/{audio_id}/favorite', [FavoriteController::class, 'deleteFavorite']);
    Route::get('/favorites', [FavoriteController::class, 'getUserFavorites']);
});
