<?php


use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', \App\Livewire\Home::class)->name('home');

    Route::get('/process-report', \App\Livewire\Activities\ActivityIndex::class)->name('activity.index');

    Route::prefix('documents')->group(function () {
        Route::prefix('payables')->group(function () {
            Route::get('/', \App\Livewire\Payables\PayableIndex::class)->name('payable.index');
        });
        Route::prefix('receivables')->group(function () {
            Route::get('/', \App\Livewire\Receivables\ReceivableIndex::class)->name('receivable.index');
        });
    });

    Route::get('/user', \App\Livewire\User\UserIndex::class)->name('user');
});
