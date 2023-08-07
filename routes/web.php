<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\ProfileController;

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


Route::get('/dashboard', function () {
    return redirect(route('home'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/', [CounterController::class, 'index'])->name('home');
    Route::get('/project/{id}', [CounterController::class, 'project'])->name('project');
    Route::get('/project/{id}/add-items', [CounterController::class, 'addItems'])->name('project.addItems');
    Route::get('/project/delete/{id}', [CounterController::class, 'delete'])->name('project.delete');
    Route::get('/project/view/{id}', [CounterController::class, 'view'])->name('project.view');
    Route::get('/project/export/{id}', [CounterController::class, 'export'])->name('project.export');

});




require __DIR__ . '/auth.php';
