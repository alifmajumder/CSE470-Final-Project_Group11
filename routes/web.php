<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MissedCallController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskWorkerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CommunitySavingsController;
use App\Http\Controllers\ForecastController;

Route::get('/', [TaskController::class, 'home'])->name('home');

Route::get('/register', [AuthController::class, 'create']);
Route::post('/register', [AuthController::class, 'store']);

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout']);

// --------------------------------------------------------------------------
// Feature 2: SMS Alert & Missed Call System
// --------------------------------------------------------------------------
Route::post('/webhook/missed-call', [MissedCallController::class, 'handleWebhook'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('webhook.missed-call');

Route::post('/sms/send-alert', [SmsController::class, 'sendAlertViaHttp'])
    ->middleware(['auth'])
    ->name('sms.send-alert');

Route::get('/sms/dashboard', [SmsController::class, 'dashboard'])
    ->name('sms.dashboard');

// --------------------------------------------------------------------------
// Feature 20: Impact Dashboard
// --------------------------------------------------------------------------
Route::get('/impact', [App\Http\Controllers\ImpactController::class, 'dashboard'])
    ->name('impact.dashboard');

Route::get('/lang/{locale}', function (string $locale) {
    $supported = ['en', 'bn'];
    if (in_array($locale, $supported, true)) {
        session(['locale' => $locale]);
    }
    return redirect()->back()->withHeaders(['Vary' => 'Accept-Language']);
})->name('lang.switch')->where('locale', 'en|bn');

// --------------------------------------------------------------------------
// Feature 19: Admin Dashboard & User Verification
// --------------------------------------------------------------------------
Route::get('/admin/users', [AdminController::class, 'index'])
    ->middleware('auth');
    
Route::post('/admin/verify/{id}', [AdminController::class, 'verifyUser'])
    ->middleware('auth');

// --------------------------------------------------------------------------
// Feature 12: Group Task Creation
// --------------------------------------------------------------------------
Route::get('/tasks/create', [TaskController::class, 'create'])->middleware('auth');
Route::post('/tasks', [TaskController::class, 'store'])->middleware('auth');
Route::get('/my-tasks', [TaskController::class, 'myTasks'])->middleware('auth');

// --------------------------------------------------------------------------
// Feature 05: Fair Wage Calculation
// --------------------------------------------------------------------------
Route::get('/tasks/{id}', [TaskController::class, 'show'])->middleware('auth');

// --------------------------------------------------------------------------
// Feature: Worker Job Assignment & Feature 8: Digital Contract
// --------------------------------------------------------------------------
Route::post('/tasks/{task}/workers', [TaskWorkerController::class, 'store'])
    ->middleware('auth')->name('tasks.workers.store');
Route::post('/tasks/{task}/take', [TaskWorkerController::class, 'take'])
    ->middleware('auth')->name('tasks.workers.take');
Route::post('/tasks/{task}/workers/{taskWorker}/complete', [TaskWorkerController::class, 'complete'])
    ->middleware('auth')->name('tasks.workers.complete');
Route::delete('/tasks/{task}/workers/{taskWorker}', [TaskWorkerController::class, 'cancel'])
    ->middleware('auth')->name('tasks.workers.cancel');
Route::post('/tasks/{task}/workers/{taskWorker}/approve', [TaskWorkerController::class, 'approve'])
    ->middleware('auth')->name('tasks.workers.approve');
Route::post('/tasks/{task}/workers/{taskWorker}/reject', [TaskWorkerController::class, 'reject'])
    ->middleware('auth')->name('tasks.workers.reject');
Route::post('/tasks/{task}/workers/{taskWorker}/rate', [TaskWorkerController::class, 'rateWorker'])
    ->middleware('auth')->name('tasks.workers.rate');

// NEW CONTRACT ROUTES
Route::post('/tasks/{task}/workers/{taskWorker}/contract-otp', [TaskWorkerController::class, 'generateContractOtp'])
    ->middleware('auth')->name('tasks.workers.contract.otp');
Route::post('/tasks/{task}/workers/{taskWorker}/contract-forward', [TaskWorkerController::class, 'forwardContractOtp'])
    ->middleware('auth')->name('tasks.workers.contract.forward');
Route::post('/tasks/{task}/workers/{taskWorker}/contract-confirm', [TaskWorkerController::class, 'confirmContract'])
    ->middleware('auth')->name('tasks.workers.contract.confirm');

// --------------------------------------------------------------------------
// Feature: Work Completion Photo Upload
// --------------------------------------------------------------------------
Route::post('/tasks/{task}/workers/{taskWorker}/completion-photo', [TaskWorkerController::class, 'uploadCompletionPhoto'])
    ->middleware('auth')->name('tasks.workers.completion-photo');

// --------------------------------------------------------------------------
// Feature: Payment Record & Receipt
// --------------------------------------------------------------------------
Route::get('/tasks/{task}/workers/{taskWorker}/payments/create', [PaymentController::class, 'create'])
    ->middleware('auth')->name('payments.create');
Route::post('/tasks/{task}/workers/{taskWorker}/payments', [PaymentController::class, 'store'])
    ->middleware('auth')->name('payments.store');
Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])
    ->middleware('auth')->name('payments.receipt');
Route::post('/payments/{payment}/confirm', [PaymentController::class, 'confirm'])
    ->middleware('auth')->name('payments.confirm');
Route::get('/my-payments', [PaymentController::class, 'myPayments'])
    ->middleware('auth')->name('payments.mine');

// --------------------------------------------------------------------------
// Feature: Skill Badge System
// --------------------------------------------------------------------------
Route::get('/my-badges', [BadgeController::class, 'myBadges'])
    ->middleware('auth')->name('badges.mine');
Route::get('/workers/{user}/badges', [BadgeController::class, 'profile'])
    ->middleware('auth')->name('badges.profile');

// --------------------------------------------------------------------------
// User Profile & Trust Score Settings
// --------------------------------------------------------------------------
Route::get('/profile', [ProfileController::class, 'show'])->middleware('auth')->name('profile.show');
Route::post('/profile', [ProfileController::class, 'update'])->middleware('auth')->name('profile.update');

Route::post('/notifications/mark-read', [NotificationController::class, 'markAllRead'])
    ->middleware('auth')->name('notifications.read');

// --------------------------------------------------------------------------
// Feature: Community Savings Pool (Digital Somiti)
// --------------------------------------------------------------------------
Route::get('/savings', [CommunitySavingsController::class, 'index'])
    ->middleware('auth')->name('savings.index');
Route::post('/savings', [CommunitySavingsController::class, 'create'])
    ->middleware('auth')->name('savings.create');
Route::post('/savings/{pool}/members', [CommunitySavingsController::class, 'addMember'])
    ->middleware('auth')->name('savings.members.add');
Route::delete('/savings/{pool}/members/{member}', [CommunitySavingsController::class, 'removeMember'])
    ->middleware('auth')->name('savings.members.remove');
Route::post('/savings/{pool}/deposit', [CommunitySavingsController::class, 'deposit'])
    ->middleware('auth')->name('savings.deposit');
Route::post('/savings/{pool}/auto-deposit', [CommunitySavingsController::class, 'setAutoDeposit'])
    ->middleware('auth')->name('savings.auto.set');
Route::post('/savings/{pool}/auto-deposit/disable', [CommunitySavingsController::class, 'disableAutoDeposit'])
    ->middleware('auth')->name('savings.auto.disable');
Route::post('/savings/{pool}/withdraw', [CommunitySavingsController::class, 'withdraw'])
    ->middleware('auth')->name('savings.withdraw');

// --------------------------------------------------------------------------
// Feature 15: Seasonal Labor Demand Forecast
// --------------------------------------------------------------------------
Route::get('/forecast', [ForecastController::class, 'index'])->name('forecast.index');