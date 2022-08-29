<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

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

Route::get('/helloworld', function () {
    return response()->json(['message' => 'hello world'], 200);
});


// in laravel 8 the routes have been changed the complete namespace is needed
// doesn't work (controller is not invokeable?)
// Route::get('articles', ArticleController::class, 'index');

// works
// Route::get('articles', 'App\Http\Controllers\ArticleController@index');

// Route::get('articles',  [ArticleController::class, 'index']);
// Route::get('posts',  [PostController::class, 'index']);




// articles routes public
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{id}', [ArticleController::class, 'findArticle']);

Route::post('articles', [ArticleController::class, 'store']);
Route::put('articles/{id}', [ArticleController::class, 'update']);
Route::delete('articles/{id}', [ArticleController::class, 'destroy']);


Route::put('articles/{id}/publish', [ArticleController::class, 'publish']);


Route::get('articles/{id}/updateAge', [ArticleController::class, 'updateAge']);


// added to update
// Route::put('articles/{id}/publish-on', [ArticleController::class, 'publishOnDate']);





Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
