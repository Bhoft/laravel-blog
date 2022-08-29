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

// articles public routes
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{id}', [ArticleController::class, 'findArticle']);
Route::get('articles/slug/{slug}', [ArticleController::class, 'findBySlug']);
Route::get('search/articles', [ArticleController::class, 'search']);

// protected routes which would be added behind auth

// create an article
Route::post('articles', [ArticleController::class, 'store']);

// update an article
Route::put('articles/{id}', [ArticleController::class, 'update']);

// delete an article
Route::delete('articles/{id}', [ArticleController::class, 'destroy']);

//set publish date to now for an  article (e.g. quick publish via some button)
Route::put('articles/{id}/publish', [ArticleController::class, 'publish']);

// only test
Route::get('articles/{id}/updateAge', [ArticleController::class, 'updateAge']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
