<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//Route::post('/upload', 'App\Http\Controllers\VideoController@upload')->name('upload');
Route::get('/', function () {
    return view('welcome');
});

Route::post('/upload', 'App\Http\Controllers\ExampleController@submit')->name('example.upload');
Route::post('/ajax_remove_file', 'App\Http\Controllers\ExampleController@removeFile');
