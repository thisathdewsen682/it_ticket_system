<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-full max-w-none mx-auto px-4 sm:px-6 lg:px-8">
            @if (count($userRoles) > 1)
                {{-- Role Tabs - Only show if user has multiple roles --}}
                <div class="mb-6 bg-white border border-slate-200 shadow-lg sm:rounded-xl p-4">
                    <h3 class="text-sm font-medium text-slate-700 mb-3">Switch Role View:</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($userRoles as $roleName)
                            <a href="{{ route('dashboard.unified', ['role_tab' => $roleName]) }}"
                                class="inline-flex items-center px-4 py-2 rounded-lg border text-sm font-semibold transition-all shadow-sm
                                    {{ $activeRole === $roleName 
                                        ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' 
                                        : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ ucfirst(str_replace(['_', '-'], ' ', $roleName)) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Dashboard Content --}}
            @if (isset($dashboardData['view']))
                @include($dashboardData['view'], $dashboardData)
            @else
                <div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
                    <div class="p-6 text-gray-900">
                        <p class="text-slate-600">No dashboard available for this role.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
