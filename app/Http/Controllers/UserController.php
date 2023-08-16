<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\UserCreatedMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\UserCreateRequest;
use Exception;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::Query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')->orWhere('email', 'like', '%' . $request->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => ['search' => $request->search]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserCreateRequest $request)
    {
        try {
            $password = Str::random(10);
            $user = User::create($request->validated() + ['password' => Hash::make($password)]);

            Mail::to($user)->send(new UserCreatedMail($user, $password));

            return redirect()->route('users.index')->with('success', 'User Created. Please ask the user to check email.');
        } catch (Exception $e) {
            return redirect()->route('users.index')->with('error', 'Could Not create user/ error sending email');
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
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return Inertia::render('Users/Edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update($request->validated());

            return redirect()->route('users.index')->with('success', 'User Updated.');
        } catch (Exception $e) {
            return redirect()->route('users.index')->with('error', 'User update error.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('users.index')->with('success', 'User Deleted.');
    }
}
