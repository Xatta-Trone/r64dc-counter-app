<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CounterController;

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

Route::get('/', [CounterController::class,'index'])->name('home');
Route::get('/project/{id}', [CounterController::class, 'project'])->name('project');
Route::get('/project/{id}/add-items', [CounterController::class, 'addItems'])->name('project.addItems');
Route::get('/project/delete/{id}', [CounterController::class, 'delete'])->name('project.delete');
Route::get('/project/view/{id}', [CounterController::class, 'view'])->name('project.view');
Route::get('/project/export/{id}', [CounterController::class, 'export'])->name('project.export');
Route::get('/test', function () {
    return view('test');
});
