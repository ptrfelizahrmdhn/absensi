<?php

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

Route::post('/mark_attendance', function (Request $request) {
    dd($request->all());
    $name = $request->input('name');

    if ($name) {
        // Logika untuk menyimpan atau memproses absen
        return response()->json(['status' => 'success', 'message' => "$name berhasil absen"], 200);
    }

    return response()->json(['status' => 'error', 'message' => 'Wajah tidak dikenali'], 400);
});
