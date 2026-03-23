<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-900 leading-tight">
                {{ __('Email Monitor — Pending Queue') }}
            </h2>
            <span class="text-sm text-slate-500">Jobs waiting to be processed</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Tab Navigation --}}
            <div class="mb-6 flex flex-wrap items-center gap-2">
                <a href="{{ route('email-logs.index') }}"
                    class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:border-emerald-300">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Sent Emails
                </a>
                <a href="{{ route('email-logs.pending') }}"
                    class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm border-blue-500 bg-blue-600 text-white shadow-md">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pending Queue
                    @if ($jobs->total() > 0)
                        <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-blue-400/30 px-2 py-0.5 text-xs font-bold text-white">{{ $jobs->total() }}</span>
                    @endif
                </a>
                <a href="{{ route('email-logs.failed') }}"
                    class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:border-red-300">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Failed Jobs
                </a>
            </div>

            {{-- Info Box --}}
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <p class="text-sm font-medium text-amber-800">These are jobs waiting in the queue to be processed by the queue worker.</p>
                    <p class="text-xs text-amber-700 mt-1">Jobs include emails, notifications, and other background tasks. They are automatically processed when the queue worker is running.</p>
                </div>
            </div>

            {{-- Pending Jobs Table --}}
            <div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Job Type</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Queue</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Attempts</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Created</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Available At</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($jobs as $job)
                                <tr class="odd:bg-white even:bg-slate-50/30 hover:bg-slate-50 transition-colors">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-slate-500">{{ $job->id }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-amber-100 text-amber-800">
                                            {{ $job->short_name }}
                                        </span>
                                        <div class="text-xs text-slate-400 mt-1 font-mono truncate max-w-[300px]" title="{{ $job->display_name }}">{{ $job->display_name }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $job->queue }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $job->attempts }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        @if ($job->reserved)
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset bg-blue-50 text-blue-700 ring-blue-200">
                                                <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                Processing
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-200">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Waiting
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                        <div>{{ $job->created_date->format('M d, Y') }}</div>
                                        <div class="text-xs text-slate-500">{{ $job->created_date->format('h:i:s A') }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                        <div>{{ $job->available_date->format('M d, Y') }}</div>
                                        <div class="text-xs text-slate-500">{{ $job->available_date->diffForHumans() }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="text-sm text-slate-600 font-medium">No pending jobs in the queue.</p>
                                            <p class="text-xs text-slate-400">All jobs have been processed. New jobs will appear here when emails are queued.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($jobs->hasPages())
                <div class="mt-6">
                    {{ $jobs->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
