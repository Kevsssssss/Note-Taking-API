<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\NoteController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("/hello", function () {
    return response()->json(["message" => "Hello, World!"]);
});

Route::apiResource('notes', NoteController::class);