<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\HomeController;

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

// Redirect index page to login.
Route::get('/', function () {
    return redirect('/login');
});

// Middleware for authentication.
Route::group(['middleware' => ['auth:sanctum', 'verified']], function() {
    // Route for the dashboard.
    Route::get('/dashboard', [HomeController::class, 'home'])->name('dashboard');

    // Route to return accomdations based on filters.
    Route::get('/filter', [HomeController::class, 'filter'])->name('filter');
});
