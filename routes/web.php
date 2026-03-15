<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\AuthController;
use App\Http\Controllers\client\ProfileController;
use App\Http\Controllers\client\PetController;
use App\Http\Controllers\client\CartController;
use App\Http\Controllers\client\OrderController;
use App\Http\Controllers\client\AppointmentController;
use App\Http\Controllers\client\HelpController;
use App\Http\Controllers\client\ReviewController;
use App\Http\Controllers\client\PaymentController;
use App\Http\Controllers\client\AccessoryController;

// Admin Controller Imports
use App\Http\Controllers\admin\PetController as AdminPetController;
use App\Http\Controllers\admin\AccessoryController as AdminAccessoryController;
use App\Http\Controllers\admin\OutletController;
use App\Http\Controllers\admin\SupplierController;
use App\Http\Controllers\admin\AdminUserController;
use App\Http\Controllers\admin\PurchaseManageController;
use App\Http\Controllers\admin\RiderJobController;
use App\Http\Controllers\admin\ReportController;
use App\Http\Controllers\admin\AdminAppointmentController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\AdminVoucherController;
use App\Http\Controllers\admin\WeatherTestController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/

Route::name('client.')->group(function () {
    Route::get('/', function () {
        return view('welcome'); // Points to resources/views/welcome.blade.php
    });

    
    // Auth Routes
    Route::get('/register', function () {
        return view('register');
    })->name('register.page');

    Route::get('/login', function () {
        return view('login');
    })->name('login.page');

    Route::get('/forgot-password', function () {
        return view('client.forget');
    })->name('forget.page');

    Route::get('/home', function () {
        return view('client.home');
    })->name('home');

    Route::post('/forgot-password/send-otp', [AuthController::class, 'sendOTP'])->name('forget.sendOTP');
    Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyOTP'])->name('forget.verifyOTP');
    Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword'])->name('forget.resetPassword');
    Route::post('/forgot-password/clear-session', [AuthController::class, 'clearSession'])->name('forget.clearSession');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.view');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change', [ProfileController::class, 'changePasswordForm'])->name('password.change');
    Route::post('/profile/change', [ProfileController::class, 'changePassword'])->name('password.update');
    Route::post('/profile/upload-photo', [ProfileController::class, 'uploadPhoto'])->name('profile.uploadPhoto');

    // Pet Routes
    Route::get('/pets', [PetController::class, 'index'])->name('pets.index');
    Route::get('/pets/{id}', [PetController::class, 'show'])->name('pets.show');
    Route::delete('/search-history/clear', [PetController::class, 'clearSearchHistory'])->name('search-history.clear');
    Route::delete('/search-history/{id}', [PetController::class, 'destroySearchHistory'])->name('search-history.destroy');

    // Accessory Routes
    Route::get('/accessories', [AccessoryController::class, 'index'])->name('accessories.index');
    Route::get('/accessories/{id}', [AccessoryController::class, 'show'])->name('accessories.show');

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.view');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
    Route::post('/cart/update-variant', [CartController::class, 'updateVariant'])->name('cart.updateVariant');
    Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/apply-voucher', [CartController::class, 'applyVoucher'])->name('cart.applyVoucher');

    // Payment Routes
    Route::post('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::get('/payment-complete', function () {
        return view('Client.paymentsuccess');
    })->name('payment.complete');

    // Order Routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/order/{id}/download-receipt', [OrderController::class, 'downloadReceipt'])->name('order.download-receipt');
    Route::get('/order/{id}/print-receipt', [OrderController::class, 'printReceipt'])->name('order.print-receipt');

    // Appointment Routes
    Route::get('/appointments/create/{petID?}', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('appointments.availableSlots');
    Route::post('/appointments/store', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments/{appointmentID}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Help & Support
    Route::get('/help', [HelpController::class, 'show'])->name('help.show');
    Route::post('/help/ask', [HelpController::class, 'ask'])->name('help.ask');

    // Reviews
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // API Routes
    Route::get('/api/pets/images', [PetController::class, 'getPetsForImageSearch']);
    Route::get('/api/pets/{id}', [PetController::class, 'getPetDetails']);
    Route::post('/api/save-pet-features', [PetController::class, 'savePetFeatures']);
    Route::get('/api/recommendations', [PetController::class, 'getRecommendations']);

    // Other
    Route::get('/generate-features', function () {
        return view('Client.generate_features');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::name('admin.')->prefix('admin')->group(function () {
    Route::get('/test/{id}', function ($id) {
        $user = User::find($id);

        if (!$user) {
            abort(404, "UserID {$id} not found");
        }

        Auth::login($user);
        return redirect()->route('admin.dashboard');
    });

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('pet')->group(function () {
        Route::get('/add', [AdminPetController::class, 'add'])->name('pets.add');
        Route::post('/save', [AdminPetController::class, 'store'])->name('pets.store');
        Route::get('/', [AdminPetController::class, 'index'])->name('pets.index');
        Route::get('/{pet}/edit', [AdminPetController::class, 'edit'])->name('pets.edit');
        Route::put('/{pet}', [AdminPetController::class, 'update'])->name('pets.update');
        Route::delete('/{pet}', [AdminPetController::class, 'destroy'])->name('pets.destroy');
        Route::post('/detect-breed', [AdminPetController::class, 'detectBreed'])->name('pets.detectBreed');
    });

    Route::prefix('accessories')->group(function () {
        Route::get('/', [AdminAccessoryController::class, 'index'])->name('accessories.index');
        Route::get('/add', [AdminAccessoryController::class, 'add'])->name('accessories.add');
        Route::post('/store', [AdminAccessoryController::class, 'store'])->name('accessories.store');
        Route::get('/edit/{accessory}', [AdminAccessoryController::class, 'edit'])->name('accessories.edit');
        Route::put('/update/{accessory}', [AdminAccessoryController::class, 'update'])->name('accessories.update');
        Route::delete('/delete/{accessory}', [AdminAccessoryController::class, 'destroy'])->name('accessories.destroy');
    });

    Route::post('/categories/add', [AdminPetController::class, 'addCategory'])->name('categories.add');

    Route::resource('outlets', OutletController::class);

    Route::resource('suppliers', SupplierController::class);

    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::patch('/users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('users.updateStatus');
    Route::post('/users/store', [AdminUserController::class, 'store'])->name('users.store');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::get('/otp', function () {
        return view('admin.otp');
    })->name('otp.page');

    Route::post('/otp/verify', [AdminUserController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/otp/resend', [AdminUserController::class, 'resendOtp'])->name('otp.resend');

    Route::get('/purchaseManage', [PurchaseManageController::class, 'indexToPurchaseManage'])->name('purchases');
    Route::patch('/purchase/{purchaseID}/status', [PurchaseManageController::class, 'updateStatus'])->name('purchase.updateStatus');
    Route::get('/rider-assignment', [PurchaseManageController::class, 'indexToRiderAssignment'])->name('rider.assignment');
    Route::post('/rider/{riderID}/assignPurchase', [PurchaseManageController::class, 'assignPurchase'])->name('rider.assignPurchase');

    Route::get('/rider/weather/coords', [RiderJobController::class, 'weatherByCoords'])->name('rider.weather.coords');


    Route::get('/rider/jobs', [RiderJobController::class, 'index'])->name('rider.jobs');
    Route::patch('/rider/purchase/{id}/delivered', [RiderJobController::class, 'markDelivered'])->name('rider.purchase.delivered');


    Route::get('/reports', [ReportController::class, 'index'])->name('reports');

    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
    Route::put('/appointments/{id}/status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
    Route::post('/appointments/{id}/send-reminder', [AdminAppointmentController::class, 'sendReminder'])->name('appointments.sendReminder');

    Route::resource('voucher', AdminVoucherController::class);
});
