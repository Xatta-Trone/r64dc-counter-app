<?php

use App\Http\Controllers\VehicleClassifierController;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\UserCreatedMail;
use App\Exports\ProjectsExport;
use App\Exports\DataCalculation;
use App\Exports\MultipleSheetExport;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CounterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ParentProjectController;

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
    Route::get('/projects-duplicate/{id}', [CounterController::class, 'duplicate'])->name('projects.duplicate');
    Route::delete('/projects/{id}', [CounterController::class, 'delete'])->name('projects.delete');
    Route::get('/projects/{id}/edit', [CounterController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{id}', [CounterController::class, 'update'])->name('projects.update');
    Route::get('/projects-slots/{id}', [CounterController::class, 'timeSlots'])->name('projects.slots');
    Route::get('/projects-count/{id}', [CounterController::class, 'count'])->name('projects.count');
    Route::get('/project-count-data/{id}', [CounterController::class, 'countData'])->name('projects.countData');
    Route::post('/project-count-data/{id}', [CounterController::class, 'updateCountData'])->name('projects.updateCountData');
    Route::get('/export/{id}', [CounterController::class, 'export'])->name('projects.export');



    Route::resource('users', UserController::class)->middleware(['admin']);
    Route::resource('vehicle-classifiers', VehicleClassifierController::class)->middleware(['admin']);
    Route::delete('users/restore/{id}', [UserController::class, 'restore'])->middleware(['admin'])->name('users.restore');

    Route::resource('parent-projects', ParentProjectController::class);
    Route::delete('parent-projects/restore/{id}', [ParentProjectController::class, 'restore'])->middleware(['admin'])->name('parent-projects.restore');

    Route::get('test', function () {
        // $user = User::first();
        // Mail::to($user)->send(new UserCreatedMail($user, '$password'));


        $project = Project::findOrFail(8);
        $slug = Str::slug($project->title);
        return Excel::download(new MultipleSheetExport($project), 'projects-' . $slug . '.xlsx');
    });



    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('call', function (Request $request) {
    $call = $request->get('call');
    $sign = $request->get('sign');

    if (md5($sign) == 'b56f1b46b2063bec3d5b31287cdbea94') {
        Artisan::call($call);
        return 'success';
    }
    return 'fail';
});

require __DIR__ . '/auth.php';
