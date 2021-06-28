<?php

use Illuminate\Support\Facades\Route;
use AvtoDev\JsonRpc\RpcRouter;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticlesController;

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

// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/skillwork', \AvtoDev\JsonRpc\Http\Controllers\RpcController::class);

RpcRouter::on('show_full_request', AuthController::class."@showInfo");
RpcRouter::on('register', AuthController::class."@register");
RpcRouter::on('login', AuthController::class."@login");
RpcRouter::on('logout', AuthController::class."@logout");
RpcRouter::on('createArticle', ArticlesController::class."@createArticle");
RpcRouter::on('listArticles', ArticlesController::class."@listArticles");
