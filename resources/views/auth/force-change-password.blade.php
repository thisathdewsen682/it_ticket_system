<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            {{ __('Change Your Password') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
                <div class="p-8 text-gray-900">

                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 mb-6 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-800">Password Change Required</p>
                            <p class="text-sm text-amber-700 mt-1">You are using a temporary password. Please set a new password to continue using the system.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('password.force-update') }}">
                        @csrf

                        <div class="mb-6">
                            <label class="block font-semibold text-slate-900 mb-2" for="password">New Password</label>
                            <input type="password" name="password" id="password" required autofocus
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Enter your new password">
                        </div>

                        <div class="mb-6">
                            <label class="block font-semibold text-slate-900 mb-2" for="password_confirmation">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Confirm your new password">
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button>
                                {{ __('Set New Password') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
