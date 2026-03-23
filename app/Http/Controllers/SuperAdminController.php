<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeNewUserMail;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class SuperAdminController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderBy('id')->paginate(20);

        return view('super-admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();

        return view('super-admin.users.edit', compact('user', 'roles'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('super-admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'employee_no' => ['required', 'string', 'max:255', 'unique:users,employee_no'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $temporaryPassword = Str::random(12);

        $user = User::create([
            'name' => $validated['name'],
            'employee_no' => $validated['employee_no'],
            'email' => $validated['email'],
            'password' => $temporaryPassword,
            'role_id' => $validated['role_id'],
            'is_super_admin' => $request->has('is_super_admin'),
            'force_password_change' => true,
        ]);

        Mail::to($user->email)->queue(new WelcomeNewUserMail($user, $temporaryPassword));

        return redirect()->route('super-admin.users.index')
            ->with('status', "User \"{$user->name}\" created successfully! A welcome email with login credentials has been sent to {$user->email}.");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'employee_no' => ['required', 'string', 'max:255', 'unique:users,employee_no,' . $user->id],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        // Handle checkbox separately (unchecked won't send value)
        $validated['is_super_admin'] = $request->has('is_super_admin');

        $user->update($validated);

        return redirect()->route('super-admin.users.index')
            ->with('status', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('super-admin.users.index')
            ->with('status', 'User deleted successfully!');
    }

    public function changePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'new_password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('status', 'Password changed successfully!');
    }
}
