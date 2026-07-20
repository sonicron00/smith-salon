<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\Public\BookingController;
use App\Http\Controllers\Public\ManageAppointmentController;
use App\Http\Controllers\Public\AboutController;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/about', [AboutController::class, 'index'])->name('about');

Route::prefix('book')->group(function () {
    Route::get('/', [BookingController::class, 'start'])->name('booking.start');
    Route::get('/staff/{service}', [BookingController::class, 'pickStaff'])->name('booking.staff');
    Route::get('/slots/{service}/{staff}', [BookingController::class, 'pickSlot'])->name('booking.slots');
    Route::post('/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
    Route::get('/done/{appointment}', [BookingController::class, 'done'])->name('booking.done');
});

use App\Http\Controllers\Public\ConsultationFormController;

Route::get('/a/{token}', [ManageAppointmentController::class, 'show'])->name('appointment.manage');
Route::post('/a/{token}/cancel', [ManageAppointmentController::class, 'cancel'])->name('appointment.cancel');
Route::post('/a/{token}/reschedule', [ManageAppointmentController::class, 'reschedule'])->name('appointment.reschedule');

Route::get('/a/{token}/consultation', [ConsultationFormController::class, 'show'])->name('appointment.consultation');
Route::post('/a/{token}/consultation', [ConsultationFormController::class, 'submit'])->name('appointment.consultation.submit');
