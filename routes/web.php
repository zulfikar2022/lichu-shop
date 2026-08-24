<?php

use App\Http\Livewire\Admin\ProductManager;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'active', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('customer/products', 'pages::customer.products')->name('customer.products');
    Route::livewire('customer/cart', 'pages::customer.cart')->name('customer.cart');
    Route::livewire('customer/orders', 'pages::customer.orders')->name('customer.orders');
});

Route::middleware(['auth', 'active', 'verified', 'admin'])->group(function () {
    Route::get('admin/products', ProductManager::class)->name('admin.products');
});

// Super-admin routes: toggle admin role and toggle active status
Route::middleware(['auth', 'active', 'verified', 'superadmin'])->group(function () {
    Route::livewire('superadmin/users', 'pages::superadmin.users')->name('superadmin.users');

    Route::post('superadmin/users/{user}/toggle-admin', function (User $user) {
        if ($user->isSuperAdmin()) {
            return back();
        }

        $user->assignRole($user->isAdmin() ? User::ROLE_CUSTOMER : User::ROLE_ADMIN);

        return back();
    })->name('superadmin.toggle-admin');

    Route::post('superadmin/users/{user}/toggle-active', function (User $user) {
        $user->is_active = ! (bool) $user->is_active;
        $user->save();

        return back();
    })->name('superadmin.toggle-active');
});

// Admin users: allow admins to view users and toggle active status (but not toggle admin role)
Route::middleware(['auth', 'active', 'verified', 'admin'])->group(function () {
    Route::livewire('admin/users', 'pages::superadmin.users')->name('admin.users');

    Route::post('admin/users/{user}/toggle-active', function (User $user) {
        $user->is_active = ! (bool) $user->is_active;
        $user->save();

        return back();
    })->name('admin.toggle-active');
});

require __DIR__.'/settings.php';
