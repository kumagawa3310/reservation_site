<?php

use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\SlotGenerationRuleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ReservationSlotController;
use App\Http\Controllers\StayPlanController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ReservationController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/top', function () {
    return view('top');
})->middleware(['auth', 'verified'])->name('top');

Route::middleware('auth')->group(function () {
    Route::get('/access', fn() => view('access'))->name('access');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 宿泊プラン（利用者用）
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('plans.show');

    // 予約
    Route::get('/plans/{plan}/reserve', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/plans/{plan}/reserve', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}/complete', [ReservationController::class, 'complete'])->name('reservations.complete');

    // お問い合わせ（利用者用）
    Route::get('/contact', [ContactController::class, 'create'])->name('contact.create'); // 入力画面
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');  // 送信処理 
});

// 管理者用
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', AdminUserController::class);
    
    // お問い合わせ管理
    Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
    Route::patch('contacts/{contact}/status', [AdminContactController::class, 'updateStatus'])->name('contacts.updateStatus');

    // 宿泊プラン管理
    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/', [StayPlanController::class, 'index'])->name('index');
        Route::get('/create', [StayPlanController::class, 'create'])->name('create');
        Route::post('/', [StayPlanController::class, 'store'])->name('store');
        Route::get('/{plan}/edit', [StayPlanController::class, 'edit'])->name('edit');
        Route::patch('/{plan}', [StayPlanController::class, 'update'])->name('update');
        Route::delete('/{plan}', [StayPlanController::class, 'destroy'])->name('destroy');
    });

    // 予約枠管理
    Route::prefix('reservation-slots')->name('slots.')->group(function () {
        Route::get('/', [ReservationSlotController::class, 'index'])->name('index');
        Route::post('/bulk', [ReservationSlotController::class, 'bulkStore'])->name('bulkStore');
        Route::get('/{slot}/edit', [ReservationSlotController::class, 'edit'])->name('edit');
        Route::patch('/{slot}', [ReservationSlotController::class, 'update'])->name('update');
        Route::delete('/{slot}', [ReservationSlotController::class, 'destroy'])->name('destroy');
    });
    
    // 自動生成ルール管理
    Route::post('slot-rules/generate', [SlotGenerationRuleController::class, 'generate'])->name('slot-rules.generate');
    Route::resource('slot-rules', SlotGenerationRuleController::class)->except(['show', 'create', 'edit']);
});

require __DIR__.'/auth.php';
