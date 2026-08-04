<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookDemoController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FormsManualController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MsManualController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupportDocumentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [HomeController::class, 'welcome'])->name('welcome');
    Route::get('/faqs', [HomeController::class, 'faqs'])->name('faqs');
    Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
    Route::get('/book-demo', [BookDemoController::class, 'index'])->name('demo.index');
    Route::post('/book-demo', [BookDemoController::class, 'store'])->name('demo.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])
        ->name('password.request');

    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'store'])
        ->name('password.update');
});

Route::get('/auth-check', function () {
    return response()->json(['ok' => true]);
})->middleware('auth');

Route::middleware(['auth', 'nocache', 'client-context'])->group(function () {
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

    Route::post('/documents/system-procedures/{doc}/acknowledge-receipt', [DocumentController::class, 'acknowledgeReceipt'])->name('document.system_procedures.acknowledge_receipt');
    Route::post('/documents/system-procedures/{doc}/acknowledge-orientation', [DocumentController::class, 'acknowledgeOrientation'])->name('document.system_procedures.acknowledge_orientation');
    Route::post('/documents/{doc}/distribution/sync', [DocumentController::class, 'syncDistributionUsers'])
    ->name('document.distribution.sync');

    Route::get('/users/search', [UserController::class, 'search']);

    Route::put('/document/system-procedures/{section}', [SectionsController::class, 'update'])->name('document.system_procedures_section.update');

    Route::get('/documents/ms-manual', [MsManualController::class, 'index'])->name('document.ms_manual.index');
    Route::get('/documents/ms-manual/create', [MsManualController::class, 'create'])->name('document.ms_manual.create');
    Route::post('/documents/ms-manual', [MsManualController::class, 'store'])->name('document.ms_manual.store');
    Route::get('/documents/ms-manual/{doc}', [MsManualController::class, 'view'])->name('document.ms_manual.view');
    Route::get('/documents/ms-manual/{doc}/edit', [MsManualController::class, 'edit'])->name('document.ms_manual.edit');
    Route::put('/documents/ms-manual/{doc}', [MsManualController::class, 'update'])->name('document.ms_manual.update');
    Route::put('/documents/ms-manual/{doc}/sendForReview', [MsManualController::class, 'sp_forReview'])->name('document.ms_manual.forReview');
    Route::put('/documents/ms-manual/{doc}/reviewDecision', [MsManualController::class, 'sp_reviewPassOrFail'])->name('document.ms_manual.reviewPassOrFail');
    Route::put('/documents/ms-manual/{doc}/approvalDecision', [MsManualController::class, 'sp_approveOrNot'])->name('document.ms_manual.approveOrNot');

    Route::get('/documents/ms-manual/{doc}/revision-history', [MsManualController::class, 'ms_document_history'])->name('document.ms_manual.rev_history');
    Route::delete('/documents/ms-manual/{doc}/destroy', [MsManualController::class, 'destroy'])->name('document.ms_manual.destroy');

    Route::get('/documents/support-documents', [SupportDocumentController::class, 'index'])->name('document.support_document.index');
    Route::get('/documents/support-documents/create', [SupportDocumentController::class, 'create'])->name('document.support_document.create');
    Route::post('/documents/support-documents', [SupportDocumentController::class, 'store'])->name('document.support_document.store');
    Route::get('/documents/support-documents/{doc}', [SupportDocumentController::class, 'view'])->name('document.support_document.view');
    Route::get('/documents/support-documents/{doc}/edit', [SupportDocumentController::class, 'edit'])->name('document.support_document.edit');
    Route::put('/documents/support-documents/{doc}', [SupportDocumentController::class, 'update'])->name('document.support_document.update');
    Route::put('/documents/support-documents/{doc}/sendForReview', [SupportDocumentController::class, 'sp_forReview'])->name('document.support_document.forReview');
    Route::put('/documents/support-documents/{doc}/reviewDecision', [SupportDocumentController::class, 'sp_reviewPassOrFail'])->name('document.support_document.reviewPassOrFail');
    Route::put('/documents/support-documents/{doc}/approvalDecision', [SupportDocumentController::class, 'sp_approveOrNot'])->name('document.support_document.approveOrNot');


    Route::get('/documents/support-documents/{doc}/revision-history', [SupportDocumentController::class, 'ms_document_history'])->name('document.support_document.rev_history');
    Route::delete('/documents/support-documents/{doc}/destroy', [SupportDocumentController::class, 'destroy'])->name('document.support_document.destroy');
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
    Route::post('/admin/clients/{client}/sections', [ClientController::class, 'storeSection'])->name('admin.client.sections.store');
    Route::put('/admin/clients/{client}/sections/{section}', [ClientController::class, 'updateSection'])->name('admin.client.sections.update');

    Route::post('/admin/clients/{client}/invite', [ClientController::class, 'invite'])->name('admin.client.invite');
    Route::post('/invitations/{invitation}/send', [ClientController::class, 'send'])->name('admin.client.send-invite');

    Route::post('/admin/clients/{client}/consultants', [ClientController::class, 'assignConsultant'])->name('admin.client.consultants.assign');
    Route::put('/admin/clients/consultants/{clientUser}/revoke', [ClientController::class, 'revokeConsultant'])->name('admin.client.consultants.revoke');

    // CONSULTANT ROUTES
    Route::get('/consultant/clients', [ConsultantController::class, 'index'])->name('consultant.clients');
    Route::post('/consultant/clients/{client}/enter', [ConsultantController::class, 'enter'])->name('consultant.clients.enter');
    Route::post('/consultant/exit', [ConsultantController::class, 'exit'])->name('consultant.exit');

    Route::get('/activity-logs', [HomeController::class, 'showLogs'])->name('activity.index');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});


