<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-900 leading-tight">
                {{ __('Create New User') }}
            </h2>
            <a href="{{ route('super-admin.users.index') }}"
                class="inline-flex items-center px-4 py-2 bg-slate-600 border border-slate-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:bg-slate-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-150">
                Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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

            <div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
                <div class="p-8 text-gray-900">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">New User Account</h3>
                    <p class="text-sm text-slate-600 mb-6">Create a new user account. A temporary password will be generated and emailed to the user.</p>

                    <form method="POST" action="{{ route('super-admin.users.store') }}">
                        @csrf

                        <div class="mb-6">
                            <label class="block font-semibold text-slate-900 mb-2" for="name">Full Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Enter full name">
                        </div>

                        <div class="mb-6">
                            <label class="block font-semibold text-slate-900 mb-2" for="employee_no">Employee No</label>
                            <input type="text" name="employee_no" id="employee_no" value="{{ old('employee_no') }}" required
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Enter unique employee number">
                        </div>

                        <div class="mb-6">
                            <label class="block font-semibold text-slate-900 mb-2" for="email">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Enter email address (required for sending credentials)">
                            <p class="text-xs text-slate-500 mt-1">The temporary password will be sent to this email address.</p>
                        </div>

                        <div class="mb-6">
                            <label class="block font-semibold text-slate-900 mb-2" for="role_id">Role</label>
                            <select name="role_id" id="role_id" required
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Select Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                                        {{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_super_admin" value="1" @checked(old('is_super_admin'))
                                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ms-2 text-sm font-semibold text-slate-900">Super Admin</span>
                            </label>
                            <p class="text-xs text-slate-600 mt-1 ml-6">Super admins have full system access including user management</p>
                        </div>

                        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">What happens when you create this account:</p>
                                    <ul class="text-sm text-blue-700 mt-1 space-y-1 list-disc list-inside">
                                        <li>A temporary password will be auto-generated</li>
                                        <li>An email will be sent to the user with their login credentials</li>
                                        <li>The user will be forced to change their password on first login</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>
                                {{ __('Create User & Send Email') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
