<?php

use App\Models\User;
use Inertia\Inertia;
use App\Mail\UserCreatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\ProfileController;

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



Route::get('/dashboard', function () {
    return redirect()->route('index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('index');
    Route::get('/projects', [CounterController::class, 'index'])->name('projects.index');
    Route::post('/projects', [CounterController::class, 'store'])->name('projects.store');
    Route::get('/projects-create', [CounterController::class, 'create'])->name('projects.create');
    Route::delete('/projects/{id}', [CounterController::class, 'delete'])->name('projects.delete');
    Route::get('/projects-slots/{id}', [CounterController::class, 'timeSlots'])->name('projects.slots');
    Route::get('/projects-count/{id}', [CounterController::class, 'count'])->name('projects.count');
    Route::get('/project-count-data/{id}', [CounterController::class, 'countData'])->name('projects.countData');

    Route::resource('users', UserController::class)->middleware(['admin']);

    Route::get('test', function () {
        $user = User::first();
        Mail::to($user)->send(new UserCreatedMail($user, '$password'));
    });



    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
