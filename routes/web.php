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

Route::middleware([
    \Filament\Http\Middleware\DisableBladeIconComponents::class,
    \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
    \Filament\Http\Middleware\SetUpPanel::class . ':app',
])->group(function () {
    Route::get('/', \App\Filament\Pages\Auth\Login::class)->name('login');
});

