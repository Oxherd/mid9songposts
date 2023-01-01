<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ThreadController::class, 'index']);
Route::get('/month/{month?}', [ThreadController::class, 'index'])
    ->name('threads.month')
    ->where('month', '20[0-9]{2}-[01][0-9]');
Route::get('/threads/{thread:date}', [ThreadController::class, 'show'])->name('threads.show');
Route::get('/links', [LinkController::class, 'index'])->name('links.index');
