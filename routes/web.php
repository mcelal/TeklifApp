<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\ProposalController;

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
    return redirect()->route('login');

    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    /*
     * Customer Routes
     */
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::post('autocomplete', [CustomerController::class, 'autocomplete'])->name('autocomplete');
    });
    Route::resource('customers', CustomerController::class);

    /*
     * Cars Routes
     */
    Route::prefix('cars')->name('cars.')->group(function () {
        Route::post('autocomplete', [CarController::class, 'autocomplete'])->name('autocomplete');
    });
    Route::resource('cars', CarController::class);

    /*
     * Proposal Routes
     */
    Route::prefix('proposals')->name('proposals.')->group(function () {

    });
    Route::resource('proposals', ProposalController::class);

});

