<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\VehicleClassifier;
use App\Http\Requests\VehicleClassifierStoreRequest;

class VehicleClassifierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->user()->is_admin == false) {
            abort(403);
        }

        $vehicles = VehicleClassifier::Query();
        $perPage =  $request->per_page ?? 15;


        $vehicles = $vehicles->when($request->search, function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%');
        })
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Vehicles/Index', [
            'vehicles' => $vehicles,
            'filters' => ["search" => $request->search ?? ""]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Vehicles/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleClassifierStoreRequest $request)
    {
        if ($request->user()->is_admin == false) {
            abort(403);
        }

        // check
        $check = VehicleClassifier::where('name', $request->name)->first();

        if ($check) {
            return redirect()->back()->with('error', 'Item already exists.');
        }

        try {
            $vehicle = VehicleClassifier::create($request->validated());

            return redirect()->route('vehicle-classifiers.index')->with('success', 'Item Created.');
        } catch (Exception $e) {
            return redirect()->route('vehicle-classifiers.index')->with('error', 'Could Not create item.');
        }
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

        $vehicle = VehicleClassifier::findOrFail($id);
        return Inertia::render('Vehicles/Edit', ['vehicle' => $vehicle]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehicleClassifierStoreRequest $request, string $id)
    {
        if ($request->user()->is_admin == false) {
            abort(403);
        }

        // check
        $check = VehicleClassifier::where('id', '<>', $id)->where('name', $request->name)->first();

        if ($check) {
            return redirect()->back()->with('error', 'Item already exists.');
        }

        try {
            $item = VehicleClassifier::findOrFail($id);
            $item->update($request->validated());

            return redirect()->route('vehicle-classifiers.index')->with('success', 'Item Updated.');
        } catch (Exception $e) {
            return redirect()->route('vehicle-classifiers.index')->with('error', 'Item update error.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        if ($request->user()->is_admin == false) {
            abort(403);
        }
        VehicleClassifier::findOrFail($id)->delete();

        return redirect()->route('vehicle-classifiers.index')->with('success', 'Item Deleted.');
    }
}
