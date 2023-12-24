<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => 'auth:sanctum'], function () {
    //Posts
    Route::post('books/issue_book/{book_id}', [BookController::class,'issueBook'])->name('books.issue');
    Route::post('books/apply_book_loan/', [BookController::class,'applyBookLoan'])->name('books.apply');
    Route::resource('books', BookController::class);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
});


Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/login', [AuthController::class, 'loginPage'])->name('login');


