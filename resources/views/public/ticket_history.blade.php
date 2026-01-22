<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticket #{{ $ticket->id }} History | IT JOB REQUEST SYSTEM</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-50 text-gray-900">
    <div class="min-h-screen flex flex-col">
        <header class="relative z-20 border-b border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto flex h-16 w-full max-w-screen-2xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}" class="font-semibold tracking-tight text-slate-900 hover:text-blue-600">
                    IT JOB REQUEST SYSTEM
                </a>
                <div class="flex gap-2 text-sm">
                    <a href="{{ route('public.section_status') }}"
                        class="inline-flex items-center rounded-lg border border-blue-600 bg-white px-3 py-1.5 font-semibold text-blue-800 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                        ← Back to status
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <div class="w-full max-w-6xl mx-auto px-8 py-12 sm:px-14 lg:px-18">
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white/95 shadow-xl">
                    <div class="border-b border-slate-100 bg-white px-10 py-9 sm:px-14">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="space-y-2">
                                <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-900">
                                    Ticket history
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">Ticket #{{ $ticket->id }}</h1>
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-blue-800">
                                        {{ $statuses[$ticket->status] ?? ucwords(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-700">{{ $ticket->title }}</p>
                                <div class="flex flex-wrap gap-3 text-sm text-slate-700">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1">Section: <strong class="text-gray-900 ms-1">{{ $ticket->section->name ?? 'Unassigned' }}</strong></span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1">Priority: <strong class="text-gray-900 ms-1">{{ $ticket->priority ?? 'N/A' }}</strong></span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1">Needed by: <strong class="text-gray-900 ms-1">{{ $ticket->needed_by ? $ticket->needed_by->format('M d, Y') : 'Not set' }}</strong></span>
                                </div>
                            </div>
                            <div class="rounded-xl border border-slate-100 bg-slate-50 px-5 py-3 text-sm text-slate-900 sm:text-right">
                                <div class="font-semibold text-slate-900">Public view</div>
                                <div class="text-xs text-slate-600">History is read-only.</div>
                            </div>
                        </div>
                    </div>

                    <div class="px-10 py-10 sm:px-14">
                        <div class="mb-4 text-sm text-slate-700">Full status trail for this ticket.</div>
                        <div class="overflow-x-auto rounded-2xl border border-slate-100 bg-white px-6 sm:px-8">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead class="bg-slate-50/50">
                                    <tr>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-900">When</th>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-900">From</th>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-900">To</th>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-900">By</th>
                                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-900">Remark</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 bg-white">
                                    @forelse ($histories as $history)
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="px-5 py-4 text-sm text-gray-900">{{ $history->created_at?->format('M d, Y h:i A') }}</td>
                                            <td class="px-5 py-4 text-sm text-slate-900">{{ $statuses[$history->from_status] ?? ($history->from_status ? ucwords(str_replace('_', ' ', $history->from_status)) : '—') }}</td>
                                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $statuses[$history->to_status] ?? ucwords(str_replace('_', ' ', $history->to_status)) }}</td>
                                            <td class="px-5 py-4 text-sm text-slate-900">{{ $history->user?->name ?? 'System' }}</td>
                                            <td class="px-5 py-4 text-sm text-slate-900">{{ $history->remark ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-5 py-6 text-center text-sm text-slate-600">No history found for this ticket.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="border-t border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto w-full max-w-screen-2xl px-4 py-6 text-center text-sm text-slate-700 sm:px-6 lg:px-8">
                {{ config('app.name', 'IT JOB REQUEST SYSTEM') }} &middot; Ticket history
            </div>
        </footer>
    </div>
</body>

</html>
