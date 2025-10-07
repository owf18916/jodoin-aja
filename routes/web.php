<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayableDocumentController;
use App\Http\Controllers\ReceivableDocumentController;

// Route::get('/_nas-check', function () {
//     $root = env('NAS_FATP_ROOT');
//     return [
//         'root' => $root,
//         'root_exists' => is_dir($root),
//     ];
// });

Route::middleware('auth')->group(function () {
    Route::get('/', \App\Livewire\Home::class)->name('home');

    Route::get('/process-report', \App\Livewire\Activities\ActivityIndex::class)->name('activity.index');

    Route::prefix('documents')->group(function () {
        Route::prefix('payables')->group(function () {
            Route::get('/', \App\Livewire\Payables\PayableIndex::class)->name('payable.index');
        });

        Route::get('/payables/{payable}/pdf', [PayableDocumentController::class, 'showPdf'])
            ->name('payables.pdf');   // VIEW inline
        Route::get('/payables/{payable}/zip', [PayableDocumentController::class, 'downloadZip'])
            ->name('payables.zip');   // DOWNLOAD zip

        Route::prefix('receivables')->group(function () {
            Route::get('/', \App\Livewire\Receivables\ReceivableIndex::class)->name('receivable.index');
        });

        Route::get('/receivables/{receivable}/pdf', [ReceivableDocumentController::class, 'showInvoicePdf'])
            ->name('receivable-invoice.pdf');   // VIEW inline
        Route::get('/receivables/{bl}/pdf', [ReceivableDocumentController::class, 'showBlPdf'])
            ->name('receivable-bl.pdf');   // VIEW inline
    });

    Route::prefix('master')->group(function () {
        Route::get('/suppliers', \App\Livewire\Suppliers\SupplierIndex::class)->name('supplier.index');
        Route::get('/customers', \App\Livewire\Customers\CustomerIndex::class)->name('customer.index');
        Route::get('/banks', \App\Livewire\Banks\BankIndex::class)->name('bank.index');
        Route::get('/currencies', \App\Livewire\Currencies\CurrencyIndex::class)->name('currency.index');
    });

    Route::get('/user', \App\Livewire\User\UserIndex::class)->name('user');
});
