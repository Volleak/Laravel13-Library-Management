<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookCopyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\BorrowingRequestController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\FinePaymentController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LibrarySettingController;
use App\Http\Controllers\ActivityLogController;

Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// GUEST ROUTES (Authentication)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

// ==========================================
// AUTHENTICATED ROUTES
// ==========================================
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Shared: Profile Management
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // ==========================================
    // MEMBER ROUTES
    // ==========================================
    Route::middleware('role:Member')->group(function () {
        Route::post('books/{book}/request', [BorrowingRequestController::class, 'store'])->name('member.request-book');
        Route::get('my-requests', [BorrowingRequestController::class, 'index'])->name('member.requests');
        Route::get('my-loans', [BorrowingController::class, 'myLoans'])->name('member.loans');
        Route::get('my-fines', [FineController::class, 'myFines'])->name('member.fines');
    });

    // ==========================================
    // STAFF ROUTES (Admin + Librarian)
    // ==========================================
    Route::middleware('role:Admin,Librarian')->group(function () {
        // Catalog Management
        Route::resource('books', BookController::class);
        Route::post('books/{book}/copies', [BookCopyController::class, 'store'])->name('copies.store');
        Route::patch('copies/{copy}/status', [BookCopyController::class, 'updateStatus'])->name('copies.status');
        
        Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
        Route::resource('authors', AuthorController::class)->except(['create', 'show', 'edit']);
        
        // Member Management
        Route::resource('members', MemberController::class);
        
        // Borrowing Operations
        Route::get('borrowing/requests', [BorrowingRequestController::class, 'index'])->name('borrowing.requests');
        Route::post('borrowing/requests/{borrowingRequest}/approve', [BorrowingRequestController::class, 'approve'])->name('requests.approve');
        Route::post('borrowing/requests/{borrowingRequest}/reject', [BorrowingRequestController::class, 'reject'])->name('requests.reject');
        
        Route::get('borrowing/transactions', [BorrowingController::class, 'index'])->name('transactions.index');
        Route::post('borrowing/transactions/{transaction}/return', [BorrowingController::class, 'processReturn'])->name('borrowing.return');
        
        // Financials
        Route::get('fines', [FineController::class, 'index'])->name('fines.index');
        Route::post('fines/{fine}/pay', [FinePaymentController::class, 'store'])->name('fines.pay');
        
        // Reports
        Route::get('reports', [ReportController::class, 'dashboard'])->name('reports.index');
    });

    // ==========================================
    // ADMIN ONLY ROUTES
    // ==========================================
    Route::middleware('role:Admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('settings', [LibrarySettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [LibrarySettingController::class, 'update'])->name('settings.update');
        Route::get('audit-logs', [ActivityLogController::class, 'index'])->name('logs.index');
    });


    // ==========================================
    // SHARED AUTHENTICATED ROUTES (Everyone)
    // ==========================================
    Route::get('books', [BookController::class, 'index'])->name('books.index');
    Route::get('books/{book}', [BookController::class, 'show'])->name('books.show');

    // ==========================================
    // STAFF ROUTES (Admin + Librarian)
    // ==========================================
    Route::middleware('role:Admin,Librarian')->group(function () {
        // Change this from resource('books') to except index/show
        Route::resource('books', BookController::class)->except(['index', 'show']);
        
        // ... rest of your existing staff routes
    });
    Route::get('/requests', [BorrowingRequestController::class, 'index'])->name('requests.index');

    Route::get('/settings', [LibrarySettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [LibrarySettingController::class, 'update'])->name('settings.update');
});