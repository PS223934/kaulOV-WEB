<?php

use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserCreditController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\StopController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\RideController;
use App\Models\Person;
use App\Models\User;
use App\Models\UserCredit;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use PHPUnit\TextUI\XmlConfiguration\Group;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    // a product-list component is used in the welcome view (and in the dashboard view)
    // the component is defined in app\View\Components\ProductList.php
    // the component is rendered in the welcome view with the following line:
    // <x-product-list />
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// management routes protected by the role middleware for management and the admin user
Route::middleware(['role:management|admin'])->group(function () {
    Route::resource('persons', PersonController::class);
});

Route::middleware(['permission:persoonlijk top-up wallet'])->group(function () {
    Route::get('topup', [UserCreditController::class, 'index'])->name('topup.index');
    Route::post('topup', [UserCreditController::class, 'a2bal'])->name('topup.a2bal');
    Route::get('topup/success', [UserCreditController::class, 'a2success'])->name('topup.success');
    Route::get('topup/failed', [UserCreditController::class, 'a2failed'])->name('topup.failed');
});

Route::middleware(['permission:reisgeschiedenis bekijken'])->group(function () {
    Route::get('history', [ScheduleController::class, 'viewHistory'])->name('schedule.viewHistory');
});

Route::middleware(['permission:persoonlijk rooster bekijken'])->group(function () {
    Route::get('roster', [ScheduleController::class, 'viewRoster'])->name('schedule.viewRoster');
});

Route::middleware(['permission:schedules beheren'])->group(function () {
    Route::resource('scheduler/stops', StopController::class);
    Route::resource('scheduler/lines', LineController::class);
    Route::resource('scheduler/schedules', ScheduleController::class);
});

Route::middleware(['permission:toegang-app'])->group(function () {
    Route::post('app-login', [TokenController::class, 'generate'])->name('app-login.generate');
        Route::get('app-login/create', [TokenController::class, 'create'])->name('app-login.create');
        Route::get('app-login', [TokenController::class, 'index'])->name('app-login.index');
});

// the route for management to create an employee from a user (change the role)
Route::middleware(['role:management|admin'])->group(function () {
    Route::resource('persons', PersonController::class);
    Route::resource('users', UserController::class);
});

Route::middleware(['permission:kanker'])->group(function () {
    Route::resource('rides', RideController::class);
});

require __DIR__.'/auth.php';
