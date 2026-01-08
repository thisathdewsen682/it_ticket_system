<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
