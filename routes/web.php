<?php

use App\Models\User;
use App\Mail\SendTestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
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

Route::get('/profie/{user}/edit', [UserController::class, 'profile'])->name('profile.edit');
Route::put('/profile/{user}', [UserController::class, 'update'])->name('profile.update');

Route::get('send-mail', function() {
    $message = "This is a test email for OnePage by FCU Solutions.";
    Mail::to('jeslynella@gmail.com')->send(new SendTestEmail($message));
});

// ADMIN ROUTES
Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

Route::get('/admin/clients/onboard', [ClientController::class, 'create'])->name('admin.client.create');
Route::post('/admin/clients/onboard', [ClientController::class, 'store'])->name('admin.client.store');
Route::get('/admin/clients/{client}/view', [ClientController::class, 'view'])->name('admin.client.view');
Route::get('/admin/clients/{client}/edit', [ClientController::class, 'edit'])->name('admin.client.edit');
Route::put('/admin/clients/{client}', [ClientController::class, 'update'])->name('admin.client.update');

Route::post('/admin/clients/{client}/invite', [ClientController::class, 'invite'])->name('admin.client.invite');
Route::post('/invitations/{invitation}/send', [ClientController::class, 'send'])->name('admin.client.send-invite');
