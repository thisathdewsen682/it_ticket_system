<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => $request->input('email') ?: null,
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'employee_no' => ['required', 'string', 'max:50', 'unique:' . User::class],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);


        $employeeRoleId = Role::where('name', 'employee')->value('id')
            ?? Role::firstOrCreate(['name' => 'employee'])->id;
        $user = User::create([
            'name' => $request->name,
            'employee_no' => $request->employee_no,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $employeeRoleId,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}