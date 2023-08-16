<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Project;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\ProjectsExport;
use App\Models\ProjectTimeData;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CounterStoreRequest;

class CounterController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::Query()->withCount(['projectData',])->with(['user:id,name']);

        if ($request->user()->is_admin == false) {
            $projects = $projects->where('user_id', $request->user()->id);
        }
        $projects = $projects->when($request->search, function ($q) use ($request) {
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
        // dd($request->all(), Carbon::parse($request->day));
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

            $data = [
                'title' => $request->title,
                'day' => Carbon::parse($request->day),
                'intersection' => $request->intersection,
                'approach_name' => $request->approach_name,
                'weather_condition' => $request->weather_condition,
                'user_id' => $request->user()->id,
            ];


            DB::transaction(function () use ($request, $projectData, $data) {
                $project = Project::create($data);
                $project->projectData()->createMany($projectData);
            });
            return redirect()->route('projects.index')->with('success', "Project Created.");
        } catch (Exception $e) {
            return redirect()->back()->with('error', "Error creating project." . $e->getMessage());
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

    public function delete(int $id)
    {
        $project = Project::findOrFail($id)->delete();
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
        return Excel::download(new ProjectsExport($project), 'projects-' . $slug . '.xlsx');
    }
}
