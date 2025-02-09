<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Doctor\ExaminationController;
use App\Http\Controllers\Doctor\PrescriptionController;
use App\Http\Controllers\Pharmacist\PharmacyController;
use App\Http\Middleware\CheckRole;

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.post');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Auth::routes();
// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Doctor Routes
    Route::middleware([CheckRole::class.':doctor'])->group(function () {
        Route::get('/dashboard', [ExaminationController::class, 'index'])->name('doctor.dashboard');

        // Examination Routes
        Route::resource('examinations', ExaminationController::class);

        // Prescription Routes
        Route::resource('prescriptions', PrescriptionController::class);
        Route::get('/get-medicine-prices/{id}', [PrescriptionController::class, 'getMedicinePrices']);
    });

    // Pharmacist Routes
    Route::middleware([CheckRole::class.':pharmacist'])->group(function () {
        Route::get('/pharmacy', [PharmacyController::class, 'index'])->name('pharmacy.index');
        Route::get('/pharmacy/{prescription}', [PharmacyController::class, 'show'])->name('pharmacy.show');
        Route::post('/pharmacy/{prescription}/process', [PharmacyController::class, 'process'])->name('pharmacy.process');
        Route::get('/pharmacy/{prescription}/print', [PharmacyController::class, 'printReceipt'])->name('pharmacy.print-receipt');
    });
});


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
