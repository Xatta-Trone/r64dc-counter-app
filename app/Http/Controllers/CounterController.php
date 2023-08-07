<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ProjectsExport;
use Maatwebsite\Excel\Facades\Excel;

class CounterController extends Controller
{
    public function index()  {


        // $c = CarbonPeriod::since('00:00')->minutes(5)->until('23:59')->toArray();

        // dd($c);

        $projects = Project::select('id','title','created_at')->orderBy('id','desc')->paginate(20);


        return view('welcome',compact('projects'));

    }

    public function project(int $id)
    {

        $project = Project::findOrFail($id);


        return view('project', compact('project'));
    }

    public function delete(int $id)
    {

        $project = Project::findOrFail($id)->delete();

        return redirect()->route('home');
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
