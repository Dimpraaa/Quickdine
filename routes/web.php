<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

// Redirect halaman utama ke Meja 1 secara default
Route::get('/', function () {
    return redirect()->route('menu.index', ['table_number' => 1]);
});

// ==========================================
// RUTE AUTHENTIKASI (Login, Register, Logout)
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');

// --- ROUTES LUPA PASSWORD ---
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'simulateForgotPassword'])->name('password.simulate');
Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])->name('password.verify_otp_form');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.verify_otp');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('password.resend_otp');
Route::get('/reset-password', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// RUTE PELANGGAN (Customer Side)
// ==========================================
Route::get('/menu/{table_number}', [MenuController::class, 'index'])->name('menu.index');
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
Route::get('/order-success/{transaction_id}', [OrderController::class, 'success'])->name('order.success');
Route::get('/history/{table_number}', [OrderController::class, 'history'])->name('order.history');
Route::get('/order/{transaction_id}/receipt', [OrderController::class, 'receipt'])->name('order.receipt');
Route::get('/order/{transaction_id}/receipt/pdf', [OrderController::class, 'downloadReceiptPdf'])->name('order.receipt.pdf');
Route::post('/order/{transaction_id}/review', [OrderController::class, 'submitReview'])->name('order.review');
Route::post('/order/auto-cancel', [OrderController::class, 'autoCancel'])->name('order.autoCancel');
Route::post('/order/{transaction_id}/cancel-and-reorder', [OrderController::class, 'cancelAndReorder'])->name('order.cancelAndReorder');
Route::post('/order/{transaction_id}/reorder', [OrderController::class, 'reorder'])->name('order.reorder');
Route::post('/payment/verify', [OrderController::class, 'verifyPayment'])->name('payment.verify');
Route::post('/call-waiter', [MenuController::class, 'callWaiter'])->name('call.waiter');

// Webhook Midtrans (Jika menggunakan integrasi asli, tambahkan pengecualian CSRF)
Route::post('/midtrans/webhook', [OrderController::class, 'webhook'])
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('midtrans.webhook');

// ==========================================
// RUTE TERLINDUNGI: ADMIN ONLY
// ==========================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard Utama Admin (Statistik Pendapatan & Pesanan Terbaru)
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::patch('/admin/menu/{id}/toggle', [AdminController::class, 'menuToggleStock'])->name('admin.menu.toggle');
    Route::post('/admin/order/{id}/cancel', [AdminController::class, 'cancelOrder'])->name('admin.order.cancel');

    // Manajemen Akun Pengguna
    Route::get('/admin/users', [AdminController::class, 'userIndex'])->name('admin.users.index');
    Route::post('/admin/users', [AdminController::class, 'userStore'])->name('admin.users.store');
    Route::put('/admin/users/{id}', [AdminController::class, 'userUpdate'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [AdminController::class, 'userDestroy'])->name('admin.users.destroy');

    // Manajemen Menu (CRUD)
    Route::get('/admin/menu', [AdminController::class, 'menuIndex'])->name('admin.menu.index');
    Route::post('/admin/menu', [AdminController::class, 'menuStore'])->name('admin.menu.store');
    Route::put('/admin/menu/{id}', [AdminController::class, 'menuUpdate'])->name('admin.menu.update');
    Route::delete('/admin/menu/{id}', [AdminController::class, 'menuDestroy'])->name('admin.menu.destroy');

    // Manajemen Meja & QR Code
    Route::get('/admin/tables', [AdminController::class, 'tableIndex'])->name('admin.tables.index');
    Route::post('/admin/tables', [AdminController::class, 'tableStore'])->name('admin.tables.store');
    Route::delete('/admin/tables/{id}', [AdminController::class, 'tableDestroy'])->name('admin.tables.destroy');

    // Laporan Penjualan
    Route::get('/admin/reports', [AdminController::class, 'reportIndex'])->name('admin.reports.index');
    Route::get('/admin/reports/export', [AdminController::class, 'reportExport'])->name('admin.reports.export');
    Route::get('/admin/reports/pdf', [AdminController::class, 'reportPdf'])->name('admin.reports.pdf');

    // Ulasan Pelanggan
    Route::get('/admin/reviews', [AdminController::class, 'reviewIndex'])->name('admin.reviews.index');
});

// ==========================================
// RUTE TERLINDUNGI: STAFF & ADMIN (Kitchen)
// ==========================================
Route::middleware(['auth', 'role:admin,staff'])->group(function () {

    // Kitchen Monitor (Monitor Antrean Dapur)
    Route::get('/kitchen', [DashboardController::class, 'kitchen'])->name('kitchen.index');
    Route::get('/kitchen/counts', [DashboardController::class, 'kitchenCounts'])->name('kitchen.counts');
    Route::post('/order/{id}/status', [DashboardController::class, 'updateStatus'])->name('order.status');
    Route::post('/kitchen/resolve-waiter', [DashboardController::class, 'resolveWaiter'])->name('kitchen.resolveWaiter');
    Route::post('/admin/order/{id}/confirm-payment', [DashboardController::class, 'confirmPayment'])
        ->name('admin.order.confirmPayment');
});
