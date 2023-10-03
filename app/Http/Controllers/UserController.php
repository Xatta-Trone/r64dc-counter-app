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
use App\Http\Requests\Users\UsersIndexRequest;
use Exception;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UsersIndexRequest $request)
    {
        if ($request->user()->is_admin == false) {
            abort(403);
        }

        $users = User::Query();

        if ($request->active == "") {
            // all users
            $users->withTrashed();
        }

        if ($request->active == 2) {
            // deleted users
            $users->withTrashed()->whereNotNull('deleted_at');
        }

        // admin filters
        if ($request->is_admin == 1) {
            $users->where('is_admin', 1);
        }

        if ($request->is_admin == 0) {
            $users->where('is_admin', 0);
        }

        $users = $users->when($request->search, function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')->orWhere('email', 'like', '%' . $request->search . '%');
        })
            ->orderBy('id', 'desc')
            ->paginate($request->per_page)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->validated()
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
        if ($request->user()->is_admin == false) {
            abort(403);
        }

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
    public function edit(Request $request, string $id)
    {
        if ($request->user()->is_admin == false) {
            abort(403);
        }

        $user = User::withTrashed()->findOrFail($id);
        return Inertia::render('Users/Edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        if ($request->user()->is_admin == false) {
            abort(403);
        }

        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->update($request->validated());

            return redirect()->route('users.index')->with('success', 'User Updated.');
        } catch (Exception $e) {
            return redirect()->route('users.index')->with('error', 'User update error.');
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

        if ($request->force) {
            User::withTrashed()->findOrFail($id)->forceDelete();
        } else {
            User::findOrFail($id)->delete();
        }


        return redirect()->route('users.index')->with('success', 'User Deleted.');
    }

    public function restore(Request $request, string $id)
    {
        if ($request->user()->is_admin == false) {
            abort(403);
        }

        User::withTrashed()->findOrFail($id)->update(['deleted_at' => null]);

        return redirect()->route('users.index')->with('success', 'User Restored.');
    }
}
