<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CountrySwitcherController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// الصفحة الرئيسية
Route::get('/', [HomeController::class, 'index'])->name('home');

// Admin redirect
Route::get('/admin', function() {
    return redirect()->route('admin.dashboard');
})->name('admin');

// Ruta para cambiar el idioma
Route::get('language/{locale}', [App\Http\Controllers\LanguageController::class, 'switchLang'])
    ->name('language.switch');

// Ruta para cambiar el país
Route::get('country/{id}', [App\Http\Controllers\CountrySwitcherController::class, 'switchCountry'])
    ->name('country.switch');

// Rutas de autenticación
// require __DIR__.'/auth.php'; // Este archivo no existe, por lo que lo comentamos
// Route::get('/affiliate/apply', [UserAffiliateController::class, 'apply'])->name('user.affiliate.apply');

// Rutas de autenticación directamente en este archivo
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas del usuario
require __DIR__.'/user.php';

// Rutas del administrador
require __DIR__.'/admin.php';

// مسارات تتبع الروابط التسويقية
Route::get('/go/{slug}', [App\Http\Controllers\TrackingController::class, 'trackLink'])->name('affiliate.track');
Route::get('/ref/{code}', [App\Http\Controllers\TrackingController::class, 'trackReferral'])->name('affiliate.referral');
