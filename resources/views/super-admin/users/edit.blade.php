<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-900 leading-tight">
                {{ __('Edit User') }} - {{ $user->name }}
            </h2>
            <a href="{{ route('super-admin.users.index') }}"
                class="inline-flex items-center px-4 py-2 bg-slate-600 border border-slate-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:bg-slate-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-150">
                Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-lg border border-slate-200 bg-slate-50 p-4 text-blue-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                    <div class="font-semibold mb-2">Please fix the following errors:</div>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Edit User Form -->
            <div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl mb-6">
                <div class="p-8 text-gray-900">
                    <h3 class="text-lg font-semibold text-slate-900 mb-6">User Information</h3>

                    <form method="POST" action="{{ route('super-admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label class="block font-semibold text-slate-900 mb-2" for="name">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-6">
                            <label class="block font-semibold text-slate-900 mb-2" for="employee_no">Employee
                                No</label>
                            <input type="text" name="employee_no" id="employee_no"
                                value="{{ old('employee_no', $user->employee_no) }}" required
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-6">
                            <label class="block font-semibold text-slate-900 mb-2" for="email">Email
                                (optional)</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-6">
                            <label class="block font-semibold text-slate-900 mb-2" for="role_id">Role</label>
                            <select name="role_id" id="role_id" required
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Select Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
                                        {{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_super_admin" value="1" @checked(old('is_super_admin', $user->is_super_admin))
                                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ms-2 text-sm font-semibold text-slate-900">Super Admin</span>
                            </label>
                            <p class="text-xs text-slate-600 mt-1 ml-6">Super admins have full system access including
                                user management</p>
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>
                                {{ __('Update User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
                <div class="p-8 text-gray-900">
                    <h3 class="text-lg font-semibold text-slate-900 mb-6">Change Password</h3>

                    <form method="POST" action="{{ route('super-admin.users.change-password', $user) }}">
                        @csrf

                        <div class="mb-6">
                            <label class="block font-semibold text-slate-900 mb-2" for="new_password">New
                                Password</label>
                            <input type="password" name="new_password" id="new_password" required
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-6">
                            <label class="block font-semibold text-slate-900 mb-2"
                                for="new_password_confirmation">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                required
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-orange-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 shadow-md hover:shadow-lg transition ease-in-out duration-150">
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>