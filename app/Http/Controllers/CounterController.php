<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Project;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ParentProject;
use Illuminate\Support\Carbon;
use App\Exports\ProjectsExport;
use App\Models\ProjectTimeData;
use Illuminate\Support\Facades\DB;
use App\Exports\MultipleSheetExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CounterStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Requests\Projects\ProjectsIndexRequest;

class CounterController extends Controller
{
    public function index(ProjectsIndexRequest $request)
    {
        $projects = Project::Query()->withCount(['projectData',])->with(['user:id,name']);

        if ($request->parent_id) {
            $projects->where('parent_project_id', $request->parent_id);
        }


        if ($request->is_deleted && $request->user()->is_admin) {
            $projects->withTrashed()->whereNotNull('deleted_at');
        }

        if ($request->user()->is_admin == false) {
            $projects->where('user_id', $request->user()->id);
        }

        if ($request->date) {
            $projects->whereDate('day', $request->date);
        }

        if ($request->user_id && $request->user()->is_admin) {
            $projects->where('user_id', $request->user_id);
        }


        $projects = $projects->when($request->search, function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%');
        })
            ->orderBy('id', 'desc')
            ->paginate($request->per_page)
            ->withQueryString();

        $users = User::select(['id', 'name'])->orderBy('name', 'asc')->get();
        $parents = ParentProject::select('id', 'title')->orderBy('id', 'desc')->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => $request->validated(),
            'users' => $users ?? [],
            'parents' => $parents,
        ]);
    }

    public function create()
    {
        $c = CarbonPeriod::since('00:00')->minutes(5)->until('24:00')->toArray();
        $parents = ParentProject::select('id', 'title')->orderBy('id', 'desc')->get();


        $data = [];
        foreach ($c as $k => $a) {
            if ($k == count($c) - 1 &&  $a->format('H:i') == "00:00") {
                $data[] = "24:00";
            } else {
                $data[] = $a->format('H:i');
            }
        }

        return Inertia::render('Projects/Create', ['times' => $data, 'parents' => $parents]);
    }

    public function duplicate(int $id)
    {
        $project = Project::with(['FirstProjectData', 'lastProjectData'])->findOrFail($id);

        $c = CarbonPeriod::since('00:00')->minutes(5)->until('24:00')->toArray();

        $data = [];
        foreach ($c as $k => $a) {
            if ($k == count($c) - 1 &&  $a->format('H:i') == "00:00") {
                $data[] = "24:00";
            } else {
                $data[] = $a->format('H:i');
            }
        }

        $parents = ParentProject::select('id', 'title')->orderBy('id', 'desc')->get();

        return Inertia::render('Projects/Create', ['times' => $data, 'project' => $project, 'parents' => $parents]);
    }

    public function store(CounterStoreRequest $request)
    {
        $c = CarbonPeriod::since($request->start_time)->minutes($request->interval)->until($request->end_time)->toArray();


        $projectData = [];
        $times = [];

        foreach ($c as $a) {
            $times[] = $a->format('H:i');
        }

        if ($request->end_time_24 != null) {
            $times[] = "24:00";
        }


        for ($i = 0; $i < count($times); $i++) {
            if ($i == count($times) - 1) {
                break;
            }

            $projectData[] = [
                'start_time' => $times[$i],
                'end_time' => $times[$i + 1],
                'data' => $request->items,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // dd($request->all(), $projectData);

        try {

            $data = [
                'title' => $request->title,
                'day' => Carbon::parse($request->day),
                'intersection' => $request->intersection,
                'approach_name' => $request->approach_name,
                'weather_condition' => $request->weather_condition,
                'user_id' => $request->user()->id,
                'parent_project_id' => $request->parent_project_id,
            ];


            DB::transaction(function () use ($request, $projectData, $data) {
                $project = Project::create($data);
                $project->projectData()->createMany($projectData);
            });
            return redirect()->route('projects.index', ['parent_id' => $request->parent_project_id])->with('success', "Project Created.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', "Error creating project." . $e->getMessage());
        }
    }

    public function edit(string $id)
    {
        $project = Project::findOrFail($id);
        $parents = ParentProject::select('id', 'title')->orderBy('id', 'desc')->get();

        return Inertia::render('Projects/Edit', ['project' => $project, 'parents' => $parents]);
    }

    public function update(ProjectUpdateRequest $request, string $id)
    {
        $project = Project::findOrFail($id)->update(array_merge($request->validated(), ['day' => Carbon::parse($request->day)->format('Y-m-d')]));
        return redirect()->route('projects.index')->with('success', "Project Updated.");
    }

    public function project(int $id)
    {
        $project = Project::findOrFail($id);
        return view('project', compact('project'));
    }

    public function timeSlots(int $id)
    {
        $project = Project::with(['projectData.user'])->findOrFail($id);
        return Inertia::render('Projects/TimeSlots', ['project' => $project]);
    }

    public function count(int $id)
    {
        $project = Project::with(['projectData' => function ($q) {
            $q->with('user')->select('id', 'project_id', 'start_time', 'end_time', 'user_id');
        }])->findOrFail($id);
        return Inertia::render('Projects/Count', ['project' => $project]);
    }

    public function countData(int $id)
    {
        ProjectTimeData::find($id)->update(['user_id' => auth()->id()]);
        $project = ProjectTimeData::with('user')->find($id);
        return response()->json(['data' => $project]);
    }

    public function updateCountData(Request $request, int $id)
    {
        // dd($request->all());
        try {
            $projectData = ProjectTimeData::find($id)->update(['data' => $request->data]);
            return response()->json(['msg' => 'Updated..']);
        } catch (Exception $e) {
            return response()->json(['msg' => 'Error' . $e->getMessage()], 400);
        }
    }

    public function delete(Request $request, int $id)
    {


        if ($request->force) {
            if ($request->user()->is_admin == false) {
                abort(403);
            }
            $project = Project::withTrashed()->findOrFail($id)->forceDelete();
        } else {
            $project = Project::findOrFail($id)->delete();
        }


        return redirect()->route('projects.index')->with('success', "Project deleted.");
    }

    public function view(int $id)
    {
        $project = Project::findOrFail($id);
        return view('table', compact('project'));
    }

    public function addItems(int $id)
    {
        $project = Project::findOrFail($id);
        return view('add-items', compact('project'));
    }

    public function export(int $id)
    {
        $project = Project::with(['user', 'projectData'])->findOrFail($id);
        $slug = Str::slug($project->title);
        return Excel::download(new MultipleSheetExport($project), 'projects-' . $slug . '.xlsx');
    }
}
