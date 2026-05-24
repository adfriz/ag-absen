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

Route::get('/', function () {
    return view('portal');
});



Route::get('/dashboard', function () {
    return redirect(auth()->user()->role === 'admin' ? '/admin' : '/guru');
})->middleware(['auth'])->name('dashboard');


