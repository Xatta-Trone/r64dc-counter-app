<?php

use App\Models\ProjectIntersection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/intersections', function (Request $request) {
    $parentProject = $request->get('parent_id');

    $intersections = [];

    if ($parentProject) {
        $intersections = ProjectIntersection::where('parent_project_id', $parentProject)->get();
    }

    return response()->json($intersections);
});
