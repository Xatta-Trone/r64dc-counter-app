<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Project;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ProjectsExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CounterStoreRequest;
use App\Models\ProjectTimeData;

class CounterController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::Query()
            ->withCount(['projectData'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        $c = CarbonPeriod::since('00:00')->minutes(5)->until('23:59')->toArray();

        $data = [];
        foreach ($c as $a) {
            $data[] = $a->format('H:i');
        }
        return Inertia::render('Projects/Create', ['times' => $data]);
    }

    public function store(CounterStoreRequest $request)
    {
        $c = CarbonPeriod::since($request->start_time)->minutes($request->interval)->until($request->end_time)->toArray();

        $projectData = [];
        $times = [];

        foreach ($c as $a) {
            $times[] = $a->format('H:i');
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

        try {
            DB::transaction(function () use ($request, $projectData) {
                $project = Project::create(['title' => $request->title]);
                $project->projectData()->createMany($projectData);
            });
            return redirect()->route('home')->with('success', "Project Created.");
        } catch (Exception $e) {
            return redirect()->route('home')->with('error', "Error creating project.");
        }

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

    public function delete(int $id)
    {
        $project = Project::findOrFail($id)->delete();
        return redirect()->route('home')->with('success', "Project deleted.");
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
        $project = Project::findOrFail($id);
        $slug = Str::slug($project->title);
        return Excel::download(new ProjectsExport($project), 'projects-' . $slug . '.xlsx');
    }
}
