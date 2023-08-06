<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

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
}
