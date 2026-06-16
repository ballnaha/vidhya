<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminDirectorController;

Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/about', 'pages::about')->name('about');
Route::livewire('/services', 'pages::services')->name('services');
Route::livewire('/ai-director', 'pages::ai-director')->name('ai-director');
Route::livewire('/contact', 'pages::contact')->name('contact');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('admin/users/data', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('admin/users/check-email', [AdminUserController::class, 'checkEmail'])->name('admin.users.check-email');
    Route::post('admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::patch('admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::view('admin/users', 'pages.admin.⚡users')->name('admin.users');

    Route::get('admin/directors/data', [AdminDirectorController::class, 'index'])->name('admin.directors.index');
    Route::get('admin/directors/check-slug', [AdminDirectorController::class, 'checkSlug'])->name('admin.directors.check-slug');
    Route::post('admin/directors', [AdminDirectorController::class, 'store'])->name('admin.directors.store');
    Route::patch('admin/directors/{director}', [AdminDirectorController::class, 'update'])->name('admin.directors.update');
    Route::delete('admin/directors/{director}', [AdminDirectorController::class, 'destroy'])->name('admin.directors.destroy');
    Route::view('admin/directors', 'pages.admin.⚡directors')->name('admin.directors');
});

require __DIR__.'/settings.php';
