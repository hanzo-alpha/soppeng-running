<?php

use App\Filament\App\Pages\Pendaftaran;
use Illuminate\Support\Facades\Route;

Route::get('/', Pendaftaran::class)->name('home');
Route::get('/notifikasi', Pendaftaran::class)->name('tripay.notifikasi');

Route::webhooks('registrasi-webhook', 'registrasi-webhook');
Route::webhooks('resend-notification', 'resend-webhook');
Route::webhooks('tripay-notification', 'tripay-webhook');


Route::get('/mailable', function () {
    $invoice = App\Models\Pembayaran::find(1);

    return new App\Mail\PembayaranBerhasil($invoice);
});

Route::get('/sentmail', function () {
    $invoice = App\Models\Pembayaran::find(1);
    return Mail::to(request()->user())->send(new App\Mail\PembayaranBerhasil($invoice));
});
