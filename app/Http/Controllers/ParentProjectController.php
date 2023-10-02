<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParentProjectIndexRequest;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\ParentProject;
use App\Http\Requests\ParentProjectStoreRequest;

class ParentProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ParentProjectIndexRequest $request)
    {
        $projects = ParentProject::query()->withCount(['projects',]);

        if ($request->is_deleted && $request->user()->is_admin) {
            $projects->withTrashed()->whereNotNull('deleted_at');
        }

        $projects = $projects->when($request->search, function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%');
        })
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 10)
            ->withQueryString();

        return Inertia::render('Parent-Projects/Index', [
            'projects' => $projects,
            'filters' => $request->validated(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Parent-Projects/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ParentProjectStoreRequest $request)
    {
        if ($request->user()->is_admin == false) {
            abort(403);
        }
        $project = ParentProject::create($request->validated());
        return redirect()->route('parent-projects.index')->with('success', "Project Folder Created.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->user()->is_admin == false) {
            abort(403);
        }
        $project = ParentProject::findOrFail($id);
        return Inertia::render('Parent-Projects/Edit', ['project' => $project]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ParentProjectStoreRequest $request, string $id)
    {
        if ($request->user()->is_admin == false) {
            abort(403);
        }
        $project = ParentProject::findOrFail($id)->update($request->validated());
        return redirect()->route('parent-projects.index')->with('success', "Project Folder Updated.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {

        if ($request->force) {
            if ($request->user()->is_admin == false) {
                abort(403);
            }
            $project = ParentProject::withTrashed()->findOrFail($id)->forceDelete();
        } else {
            $project = ParentProject::findOrFail($id)->delete();
        }


        return redirect()->route('parent-projects.index')->with('success', "Project Folder deleted.");
    }

    public function restore(Request $request, string $id)
    {
        if ($request->user()->is_admin == false) {
            abort(403);
        }

        ParentProject::withTrashed()->findOrFail($id)->update(['deleted_at' => null]);

        return redirect()->route('parent-projects.index')->with('success', 'Project Folder Restored.');
    }
}
