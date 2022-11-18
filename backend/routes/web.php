<?php

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

Route::get('/', function () {
    return view('welcome');
});
//Route::middleware('auth')->group(function() {
//Route::group(function(){
    Route::get('/admin/{react?}', function ($react = null) {
        //dd(session('_keycloak_token'));
        return view('admin-frontend.index');
    })->where('react', '.*');
//});

//http://localhost:8000/admin/categories
