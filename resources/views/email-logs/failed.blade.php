<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-900 leading-tight">
                {{ __('Email Monitor — Failed Jobs') }}
            </h2>
            <span class="text-sm text-slate-500">Jobs that failed after all retries</span>
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
                    class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:border-amber-300">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pending Queue
                </a>
                <a href="{{ route('email-logs.failed') }}"
                    class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm border-blue-500 bg-blue-600 text-white shadow-md">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Failed Jobs
                    @if ($failedJobs->total() > 0)
                        <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-blue-400/30 px-2 py-0.5 text-xs font-bold text-white">{{ $failedJobs->total() }}</span>
                    @endif
                </a>
            </div>

            {{-- Bulk Actions --}}
            @if ($failedJobs->total() > 0)
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    <form method="POST" action="{{ route('email-logs.retry-all') }}"
                          onsubmit="return confirm('Are you sure you want to retry all {{ $failedJobs->total() }} failed jobs?');">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center rounded-lg border border-emerald-500 bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:bg-emerald-700">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Retry All Failed Jobs
                        </button>
                    </form>

                    <form method="POST" action="{{ route('email-logs.flush-failed') }}"
                          onsubmit="return confirm('Are you sure you want to permanently delete all {{ $failedJobs->total() }} failed jobs? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center rounded-lg border border-red-300 bg-white px-4 py-2 text-sm font-semibold text-red-600 shadow-sm transition-all hover:bg-red-50">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete All Failed Jobs
                        </button>
                    </form>
                </div>
            @endif

            {{-- Failed Jobs Table --}}
            <div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Job Type</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Queue</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Failed At</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Error</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-slate-900 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($failedJobs as $job)
                                <tr class="odd:bg-white even:bg-slate-50/30 hover:bg-slate-50 transition-colors">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-slate-500">{{ $job->id }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-red-100 text-red-800">
                                            {{ $job->short_name }}
                                        </span>
                                        <div class="text-xs text-slate-400 mt-1 font-mono truncate max-w-[250px]" title="{{ $job->display_name }}">{{ $job->display_name }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $job->queue }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                        <div>{{ \Carbon\Carbon::parse($job->failed_at)->format('M d, Y') }}</div>
                                        <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($job->failed_at)->format('h:i:s A') }}</div>
                                        <div class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm max-w-[350px]">
                                        <details class="group">
                                            <summary class="cursor-pointer text-red-600 hover:text-red-800 text-xs font-medium">
                                                {{ Str::limit($job->short_exception, 80) }}
                                            </summary>
                                            <pre class="mt-2 text-xs text-slate-600 bg-slate-50 rounded-lg p-3 overflow-x-auto max-h-48 overflow-y-auto border border-slate-200">{{ $job->exception }}</pre>
                                        </details>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('email-logs.retry', $job->uuid) }}">
                                                @csrf
                                                <button type="submit" title="Retry this job"
                                                    class="inline-flex items-center rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 shadow-sm transition-all hover:bg-emerald-100">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    Retry
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('email-logs.delete-failed', $job->uuid) }}"
                                                  onsubmit="return confirm('Delete this failed job permanently?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete this failed job"
                                                    class="inline-flex items-center rounded-lg border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 shadow-sm transition-all hover:bg-red-100">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <p class="text-sm text-slate-600 font-medium">No failed jobs. Everything is running smoothly!</p>
                                            <p class="text-xs text-slate-400">Failed jobs will appear here if any email delivery permanently fails.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($failedJobs->hasPages())
                <div class="mt-6">
                    {{ $failedJobs->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
