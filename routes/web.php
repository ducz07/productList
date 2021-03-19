<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;

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
//     return view('welcome');
// })->middleware('auth');

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::group(['middleware' => 'auth'], function () {

    Route::get('product-list', [ProductController::class, 'index'])->name('product.index');
    Route::post('add-update-product', [ProductController::class, 'store'])->name('product.store');
    Route::post('edit-product', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('delete-product', [ProductController::class, 'destroy'])->name('product.destroy');

});





