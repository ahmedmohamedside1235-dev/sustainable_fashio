<?php

use App\Http\Controllers\AddItemController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\Forget_PasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CollectionsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('nocache')->group(function () {
/* ======== Auth ======== */
// Login
    Route::get('/auth/login', [LoginController::class, 'index'])->name('login');
    Route::post('/auth/login', [LoginController::class, 'login'])->name('login.submit');

// Logout
    Route::get('/auth/logout', [LoginController::class, 'logout'])->name('logout');

// Register
    Route::get('/auth/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/auth/register', [RegisterController::class, 'register'])->name('register.submit');

// Forget password
    Route::get('/auth/forget', [Forget_PasswordController::class, 'index'])->name('forget');
    Route::post('/auth/forget', [Forget_PasswordController::class, 'forget'])->name('forget.submit');

    /* ======== Home Page ======== */
    Route::get('/', function () {
        return view('index');
    })->name('home');

    Route::middleware('auth:user')->group(function () {
        /* ======== Add Item ======== */
        Route::get('/auth/add_item', [AddItemController::class, 'add_item'])->name('add_item');
        Route::post('/auth/add_item_submit', [AddItemController::class, 'add_item_store'])->name('add_item_submit');

        /* ======== Collections ======== */
        Route::get('/user/collections', [UserController::class, 'indexCollection'])->name('collections');

        /* ======== Cart ======== */
        Route::get('/user/cart', [UserController::class, 'indexCart'])->name('cart');

        /* ======== Request ======== */
        Route::get('/user/request', [UserController::class, 'indexRequest'])->name('request');

        /* ======== Swap ======== */
        Route::get('/user/swap', [UserController::class, 'indexSwap'])->name('swap');

        // Collections
        Route::get('/items/all', [CollectionsController::class, 'getItems']);
        Route::get('/items/my-actions', [CollectionsController::class, 'getMyActions']);
        Route::post('/swap/store', [CollectionsController::class, 'storeSwap']);
        Route::post('/request/store', [CollectionsController::class, 'storeRequest']);

        // Requests page
        Route::get('/requests/all', [CollectionsController::class, 'getRequests']);
        Route::patch('/requests/{id}/update', [CollectionsController::class, 'updateRequest']);
        Route::patch('/requests/{id}/cancel', [CollectionsController::class, 'cancelRequest']);
        Route::patch('/swap/{id}/update', [CollectionsController::class, 'updateSwap']);
        Route::get('/swaps/all', [CollectionsController::class, 'getSwaps']);
        Route::post('/swap/{id}/cancel', [CollectionsController::class, 'cancelSwap']);
        
        // Admin
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/admin/items/{id}/delete', [AdminController::class, 'deleteItem'])->name('admin.items.delete');
        Route::post('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::post('/admin/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    });
});
