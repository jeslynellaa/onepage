<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\AuthController;

Route::get('/', [HomeController::class, 'index'])->name('dashboard')->middleware('auth');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Example protected route
Route::get('/dashboard', function () {
    return redirect('/');
})->middleware('auth');


Route::get('/documents', [DocumentController::class, 'index'])->name('document.index');
Route::get('/documents/system-procedures', [DocumentController::class, 'system_procedures'])->name('document.system_procedures');
Route::get('/documents/system-procedures/create', [DocumentController::class, 'sp_create'])->name('document.system_procedures.create');
Route::post('/documents/system-procedures', [DocumentController::class, 'sp_store'])->name('document.system_procedures.store');

Route::get('/documents/system-procedures/{doc}/view-pdf', [DocumentController::class, 'sp_view'])->name('document.system_procedures.view_pdf');

Route::get('/documents/system-procedures/{doc}/edit', [DocumentController::class, 'sp_edit'])->name('document.system_procedures.edit');
Route::get('/documents/system-procedures/{doc}', [DocumentController::class, 'sp_update'])->name('document.system_procedures.update');

Route::delete('/documents/{doc}', [DocumentController::class, 'destroy'])->name('document.system_procedures.destroy');