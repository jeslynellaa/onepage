<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\SectionsController;

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
Route::get('/documents/system-procedures/data', [DocumentController::class, 'getDocuments'])->name('documents.system_procedures.data');
Route::get('/documents/system-procedures/create', [DocumentController::class, 'sp_create'])->name('document.system_procedures.create');
Route::post('/documents/system-procedures', [DocumentController::class, 'sp_store'])->name('document.system_procedures.store');

Route::get('/documents/system-procedures/{doc}/view-pdf', [DocumentController::class, 'sp_view'])->name('document.system_procedures.view_pdf');

Route::get('/documents/system-procedures/{doc}/edit', [DocumentController::class, 'sp_edit'])->name('document.system_procedures.edit');
Route::put('/documents/system-procedures/{doc}', [DocumentController::class, 'sp_update'])->name('document.system_procedures.update');
Route::put('/documents/system-procedures/{doc}/sendForReview', [DocumentController::class, 'sp_forReview'])->name('document.system_procedures.forReview');
Route::put('/documents/system-procedures/{doc}/reviewDecision', [DocumentController::class, 'sp_reviewPassOrFail'])->name('document.system_procedures.reviewPassOrFail');
Route::put('/documents/system-procedures/{doc}/approvalDecision', [DocumentController::class, 'sp_approveOrNot'])->name('document.system_procedures.approveOrNot');

Route::delete('/documents/{doc}', [DocumentController::class, 'destroy'])->name('document.system_procedures.destroy');

Route::get('section/documents', [DocumentController::class, 'getSectionDocuments']);

Route::get('/documents/sytem-procedures/section/{code}/revision-history', [DocumentController::class, 'sp_document_history'])->name('document.system_procedures.rev_history');

Route::get('/documents/system-procedures/{doc}/dirf/edit', [DocumentController::class, 'dirf_edit'])->name('document.system_procedures.dirf_edit');
Route::put('/documents/system-procedures/{doc}/dirf/update', [DocumentController::class, 'dirf_update'])->name('document.system_procedures.dirf_update');
Route::get('/documents/system-procedures/{doc}/dirf', [DocumentController::class, 'dirf_generate'])->name('document.system_procedures.dirf_generate');

Route::get('/users/search', [UserController::class, 'search']);

Route::put('/document/system-procedures/{section}', [SectionsController::class, 'update'])->name('document.system_procedures_section.update');
