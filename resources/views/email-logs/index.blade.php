<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-900 leading-tight">
                {{ __('Email Monitor') }}
            </h2>
            <span class="text-sm text-slate-500">All sent & failed emails are logged here</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Tab Navigation --}}
            <div class="mb-6 flex flex-wrap items-center gap-2">
                <a href="{{ route('email-logs.index') }}"
                    class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm border-blue-500 bg-blue-600 text-white shadow-md">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Sent Emails
                </a>
                <a href="{{ route('email-logs.pending') }}"
                    class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:border-amber-300">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pending Queue
                    @if ($pendingCount > 0)
                        <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-bold text-amber-700">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('email-logs.failed') }}"
                    class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:border-red-300">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Failed Jobs
                    @if ($failedCount > 0)
                        <span class="ml-1.5 inline-flex items-center justify-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700">{{ $failedCount }}</span>
                    @endif
                </a>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Sent</p>
                            <p class="text-2xl font-bold text-slate-900">{{ number_format($totalSent) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Failed</p>
                            <p class="text-2xl font-bold text-red-600">{{ number_format($totalFailed) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Sent Today</p>
                            <p class="text-2xl font-bold text-slate-900">{{ number_format($todaySent) }}</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('email-logs.pending') }}" class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 hover:border-amber-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-500">Pending in Queue</p>
                            <p class="text-2xl font-bold text-amber-600">{{ number_format($pendingCount) }}</p>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Filters --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 mb-6">
                <form method="GET" action="{{ route('email-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <div>
                        <label for="search" class="block text-sm font-medium text-slate-700 mb-1">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Email, subject, or type..."
                            class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                        <select name="status" id="status"
                            class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All</option>
                            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-slate-700 mb-1">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                            class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-slate-700 mb-1">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                            class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="inline-flex items-center rounded-lg border border-blue-500 bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Filter
                        </button>
                        <a href="{{ route('email-logs.index') }}"
                            class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition-all hover:bg-slate-50">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            {{-- Email Log Table --}}
            <div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Date & Time</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">To</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Subject</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Email Type</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Ticket</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($logs as $log)
                                <tr class="odd:bg-white even:bg-slate-50/30 hover:bg-slate-50 transition-colors">
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-500">{{ $log->id }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                        <div>{{ $log->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-slate-500">{{ $log->created_at->format('h:i:s A') }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        @if ($log->status === 'sent')
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-200">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                Sent
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset bg-red-50 text-red-700 ring-red-200">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                                Failed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900 max-w-[200px] truncate" title="{{ $log->to }}">
                                        {{ $log->to }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 max-w-[250px] truncate" title="{{ $log->subject }}">
                                        {{ $log->subject ?? '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-slate-100 text-slate-700">
                                            {{ $log->short_mailable_name }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">
                                        @if ($log->ticket_id)
                                            <a href="{{ route('tickets.show', $log->ticket_id) }}"
                                               class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                                #{{ $log->ticket_id }}
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm">
                                        <a href="{{ route('email-logs.show', $log) }}"
                                           class="text-blue-600 hover:text-blue-800 hover:underline text-sm font-medium">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            <p class="text-sm text-slate-600 font-medium">No email logs found.</p>
                                            <p class="text-xs text-slate-400">Emails will appear here once they are sent from the system.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if ($logs->hasPages())
                <div class="mt-6">
                    {{ $logs->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
