<?php

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

// Route::get('/', function () {
//     return view('conteudo');
// });

Route::view("/",'index');

// Route::get('/', 'LoteriasController@index');
// Route::get('{game}', 'LoteriasController@forGame');
// Route::post('busca','LoteriasController@searchGame');
//'LoteriasController@searchGame'
