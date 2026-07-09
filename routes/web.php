<?php

declare(strict_types=1);

use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

// Startseite leitet auf das App-Panel weiter
Route::redirect('/', '/app');

// Öffentliche Annahme von Einladungen (Token aus der Einladungs-E-Mail)
Route::get('/einladung/{token}', [InvitationController::class, 'show'])->name('invitations.show');
Route::post('/einladung/{token}', [InvitationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('invitations.store');
