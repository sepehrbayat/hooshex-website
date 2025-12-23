<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Payments\PaymentController;
use App\Http\Livewire\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', Home::class)->name('home');

Route::view('/ai-tools', 'ai-tools.index')->name('ai-tools.index');
Route::get('/ai-tools/{slug}', [App\Http\Controllers\AiTools\AiToolController::class, 'show'])->name('ai-tools.show');
Route::get('/posts/{slug}', [App\Http\Controllers\Blog\PostController::class, 'show'])->name('posts.show');
Route::get('/courses/{slug}', [App\Http\Controllers\Courses\CourseController::class, 'show'])->name('courses.show');
Route::get('/careers/{slug}', [App\Http\Controllers\Careers\CareerController::class, 'show'])->name('careers.show');

Route::prefix('auth/otp')->middleware('throttle:10,1')->group(function () {
    Route::post('request', [OtpController::class, 'requestOtp'])->name('otp.request');
    Route::post('verify', [OtpController::class, 'verifyOtp'])->name('otp.verify');
});

Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// Click Tracking
Route::get('/go/{slug}', [App\Http\Controllers\Core\ClickController::class, 'go'])->name('click.track');

// App Panel Routes (Invoice Download)
Route::middleware(['auth'])->group(function () {
    Route::get('/app/invoice/{order}', [App\Http\Controllers\App\InvoiceController::class, 'download'])
        ->name('app.invoice.download');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->name('logout');

// Sitemaps (Legacy WordPress URL structure)
Route::get('/sitemap_index.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/post-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'posts'])->name('sitemap.posts');
Route::get('/ai_tool-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'aiTools'])->name('sitemap.ai-tools');
Route::get('/course-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'courses'])->name('sitemap.courses');
Route::get('/teacher-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'teachers'])->name('sitemap.teachers');
Route::get('/product-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'products'])->name('sitemap.products');
Route::get('/news-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'news'])->name('sitemap.news');
Route::get('/page-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/career-sitemap.xml', [App\Http\Controllers\SitemapController::class, 'careers'])->name('sitemap.careers');

// Dynamic Pages (must be last to avoid route conflicts)
Route::get('/{slug}', [App\Http\Controllers\PageController::class, 'show'])
    ->where('slug', '^(?!admin|api|auth|payment|ai-tools|posts|courses|careers|sitemap|go|app).*$')
    ->name('page.show');
