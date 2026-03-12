<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FormsManualController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MsManualController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\SupportDocumentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
    Route::get('/faqs', [HomeController::class, 'faqs'])->name('faqs');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/auth-check', function () {
    return response()->json(['ok' => true]);
})->middleware('auth');

Route::middleware(['auth', 'nocache'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard')->middleware('auth');
    // Route::get('/dashboard', function () {return redirect('/');})->middleware('auth');

    // ===== Document Routes =====
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

    Route::put('/documents/system-procedures/{doc}/assign-code', [DocumentController::class, 'assignCode'])->name('document.system_procedures.assignCode');

    Route::delete('/documents/{doc}', [DocumentController::class, 'destroy'])->name('document.system_procedures.destroy');

    Route::get('section/documents', [DocumentController::class, 'getSectionDocuments']);

    Route::get('/documents/sytem-procedures/section/{code}/revision-history', [DocumentController::class, 'sp_document_history'])->name('document.system_procedures.rev_history');

    Route::get('/documents/system-procedures/{doc}/dirf/edit', [DocumentController::class, 'dirf_edit'])->name('document.system_procedures.dirf_edit');
    Route::put('/documents/system-procedures/{doc}/dirf/update', [DocumentController::class, 'dirf_update'])->name('document.system_procedures.dirf_update');
    Route::get('/documents/system-procedures/{doc}/dirf', [DocumentController::class, 'dirf_generate'])->name('document.system_procedures.dirf_generate');
    Route::get('/documents/system-procedures/{doc}/comment', [DocumentController::class, 'showCommentForm'])->name('document.system_procedures.showComment');
    Route::get('/documents/system-procedures/{doc}/preview', [DocumentController::class, 'preview'])->name('document.system_procedures.sp_preview');
    Route::post('/documents/system-procedures/{doc}/comment', [DocumentController::class, 'storeComment'])->name('document.system_procedures.storeComment');

    Route::get('/users/search', [UserController::class, 'search']);

    Route::put('/document/system-procedures/{section}', [SectionsController::class, 'update'])->name('document.system_procedures_section.update');

    Route::get('/documents/ms-manual', [MsManualController::class, 'index'])->name('document.ms_manual.index');
    Route::get('/documents/ms-manual/create', [MsManualController::class, 'create'])->name('document.ms_manual.create');
    Route::post('/documents/ms-manual', [MsManualController::class, 'store'])->name('document.ms_manual.store');
    Route::get('/documents/ms-manual/{doc}', [MsManualController::class, 'view'])->name('document.ms_manual.view');

    Route::get('/documents/support-documents', [SupportDocumentController::class, 'index'])->name('document.support_document.index');
    Route::get('/documents/support-documents/create', [SupportDocumentController::class, 'create'])->name('document.support_document.create');
    Route::post('/documents/support-documents', [SupportDocumentController::class, 'store'])->name('document.support_document.store');
    Route::get('/documents/support-documents/{doc}', [SupportDocumentController::class, 'view'])->name('document.support_document.view');
    Route::get('section/sp/documents', [SupportDocumentController::class, 'getSpSectionDocuments']);

    Route::get('/documents/forms', [FormsManualController::class, 'index'])->name('document.forms.index');
    Route::get('/documents/forms/create', [FormsManualController::class, 'create'])->name('document.forms.create');
    Route::post('/documents/forms', [FormsManualController::class, 'store'])->name('document.forms.store');
    Route::get('/documents/forms/{doc}', [FormsManualController::class, 'view'])->name('document.forms.view');
    Route::get('section/form/documents', [FormsManualController::class, 'getFormsSectionDocuments']);

    // Profile Routes
    Route::get('/profie/{user}/edit', [UserController::class, 'profile'])->name('profile.edit');
    Route::put('/profile/{user}', [UserController::class, 'update'])->name('profile.update');
    Route::put('/profile/{user}/password', [UserController::class, 'updatePassword'])->name('profile.password.update');

    // ADMIN ROUTES
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    Route::get('/admin/clients/onboard', [ClientController::class, 'create'])->name('admin.client.create');
    Route::post('/admin/clients/onboard', [ClientController::class, 'store'])->name('admin.client.store');
    Route::get('/admin/clients/{client}/view', [ClientController::class, 'view'])->name('admin.client.view');
    Route::get('/admin/clients/{client}/edit', [ClientController::class, 'edit'])->name('admin.client.edit');
    Route::put('/admin/clients/{client}', [ClientController::class, 'update'])->name('admin.client.update');

    Route::post('/admin/clients/{client}/invite', [ClientController::class, 'invite'])->name('admin.client.invite');
    Route::post('/invitations/{invitation}/send', [ClientController::class, 'send'])->name('admin.client.send-invite');

    Route::get('/activity-logs', [HomeController::class, 'showLogs'])->name('activity.index');
});


