<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAdminUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin-users', [
            'users' => User::query()->where('is_admin', false)->orderBy('name')->orderBy('id')->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin-user-form', ['user' => null]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveAdminUserRequest $request): RedirectResponse
    {
        User::create($request->safe()->only(['name', 'email', 'password', 'position']));

        return redirect()->route('admin.users.index')->with('userStatus', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        abort_if($user->is_admin, 403);

        return view('admin-user-form', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveAdminUserRequest $request, User $user): RedirectResponse
    {
        $details = $request->safe()->only(['name', 'email', 'position']);

        if ($user->email !== $details['email']) {
            $details['email_verified_at'] = null;
        }

        if ($request->filled('password')) {
            $details['password'] = $request->validated('password');
            $details['remember_token'] = Str::random(60);
        }

        $user->forceFill($details)->save();

        return redirect()->route('admin.users.index')->with('userStatus', 'User details updated successfully.');
    }
}
