<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminHomeController;
use App\Http\Controllers\AdminDirectorController;
use App\Http\Controllers\AdminFaqController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminPortfolioController;
use App\Http\Controllers\ContactController;

Route::livewire('/', 'pages::home')->name('home');
Route::livewire('/about', 'pages::about')->name('about');
Route::livewire('/services', 'pages::services')->name('services');
Route::livewire('/ai-director', 'pages::ai-director')->name('ai-director');
Route::livewire('/portfolio', 'pages::portfolio')->name('portfolio');
Route::livewire('/contact', 'pages::contact')->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');
Route::livewire('/faq', 'pages::faq')->name('faq');

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('admin/home', [AdminHomeController::class, 'edit'])->name('admin.home');
    Route::patch('admin/home', [AdminHomeController::class, 'update'])->name('admin.home.update');
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

    Route::get('admin/faqs/data', [AdminFaqController::class, 'index'])->name('admin.faqs.index');
    Route::post('admin/faqs', [AdminFaqController::class, 'store'])->name('admin.faqs.store');
    Route::patch('admin/faqs/reorder', [AdminFaqController::class, 'reorder'])->name('admin.faqs.reorder');
    Route::patch('admin/faqs/{faq}', [AdminFaqController::class, 'update'])->name('admin.faqs.update');
    Route::delete('admin/faqs/{faq}', [AdminFaqController::class, 'destroy'])->name('admin.faqs.destroy');
    Route::view('admin/faqs', 'pages.admin.⚡faqs')->name('admin.faqs');

    Route::get('admin/services/data', [AdminServiceController::class, 'index'])->name('admin.services.index');
    Route::post('admin/services', [AdminServiceController::class, 'store'])->name('admin.services.store');
    Route::patch('admin/services/reorder', [AdminServiceController::class, 'reorder'])->name('admin.services.reorder');
    Route::patch('admin/services/{service}', [AdminServiceController::class, 'update'])->name('admin.services.update');
    Route::delete('admin/services/{service}', [AdminServiceController::class, 'destroy'])->name('admin.services.destroy');
    Route::view('admin/services', 'pages.admin.⚡services')->name('admin.services');

    Route::get('admin/portfolios/data', [AdminPortfolioController::class, 'index'])->name('admin.portfolios.index');
    Route::post('admin/portfolios', [AdminPortfolioController::class, 'store'])->name('admin.portfolios.store');
    Route::patch('admin/portfolios/reorder', [AdminPortfolioController::class, 'reorder'])->name('admin.portfolios.reorder');
    Route::patch('admin/portfolios/{portfolio}', [AdminPortfolioController::class, 'update'])->name('admin.portfolios.update');
    Route::delete('admin/portfolios/{portfolio}', [AdminPortfolioController::class, 'destroy'])->name('admin.portfolios.destroy');
    Route::view('admin/portfolios', 'pages.admin.⚡portfolios')->name('admin.portfolios');
});

Route::get('sitemap.xml', function () {
    $urls = [
        ['loc' => route('home'), 'lastmod' => now()->startOfDay()->toAtomString(), 'changefreq' => 'weekly', 'priority' => '1.0'],
        ['loc' => route('about'), 'lastmod' => now()->startOfDay()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.8'],
        ['loc' => route('services'), 'lastmod' => now()->startOfDay()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.8'],
        ['loc' => route('ai-director'), 'lastmod' => now()->startOfDay()->toAtomString(), 'changefreq' => 'weekly', 'priority' => '0.9'],
        ['loc' => route('portfolio'), 'lastmod' => now()->startOfDay()->toAtomString(), 'changefreq' => 'weekly', 'priority' => '0.9'],
        ['loc' => route('faq'), 'lastmod' => now()->startOfDay()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.5'],
        ['loc' => route('contact'), 'lastmod' => now()->startOfDay()->toAtomString(), 'changefreq' => 'monthly', 'priority' => '0.7'],
    ];

    $content = view('sitemap', compact('urls'))->render();

    return response($content, 200)
        ->header('Content-Type', 'text/xml');
})->name('sitemap');

Route::get('robots.txt', function () {
    $sitemapUrl = route('sitemap');
    $content = "User-agent: *\n";
    $content .= "Disallow: /admin\n";
    $content .= "Disallow: /dashboard\n";
    $content .= "Disallow: /login\n";
    $content .= "Disallow: /register\n\n";
    $content .= "Sitemap: {$sitemapUrl}\n";

    return response($content, 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');

require __DIR__.'/settings.php';
