<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;
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

// routes/api.php
Route::post('/upload-chunk', [FileUploadController::class, 'uploadChunk']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
