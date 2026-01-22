<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            {{ __('IT Member Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
                <div class="p-8 text-gray-900">
                    <div class="mb-6 text-sm text-slate-700 font-medium bg-slate-50 border border-slate-200 rounded-lg p-4">
                        Assigned tickets will appear here.
                    </div>

                    @php
                        $tab = request('tab', 'assigning');
                        $tickets = match ($tab) {
                            'reopened' => $reopenedTickets ?? collect(),
                            'completed' => $completedTickets ?? collect(),
                            'confirmed' => $confirmedTickets ?? collect(),
                            default => $assigningTickets ?? collect(),
                        };
                    @endphp

                    <div class="mb-6 flex flex-wrap items-center gap-2">
                        <a href="{{ route('dashboard.it_member', ['tab' => 'assigning']) }}"
                            class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'assigning' ? 'border-blue-500 bg-blue-600 text-white shadow-md' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:border-emerald-300' }}">
                            Assigning
                        </a>
                        <a href="{{ route('dashboard.it_member', ['tab' => 'reopened']) }}"
                            class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'reopened' ? 'border-blue-500 bg-blue-600 text-white shadow-md' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:border-emerald-300' }}">
                            Reopened
                        </a>
                        <a href="{{ route('dashboard.it_member', ['tab' => 'completed']) }}"
                            class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'completed' ? 'border-blue-500 bg-blue-600 text-white shadow-md' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:border-emerald-300' }}">
                            Completed
                        </a>
                        <a href="{{ route('dashboard.it_member', ['tab' => 'confirmed']) }}"
                            class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'confirmed' ? 'border-blue-500 bg-blue-600 text-white shadow-md' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:border-emerald-300' }}">
                            Manager Confirmed
                        </a>
                    </div>

                    @if (!isset($tickets) || $tickets->count() === 0)
                        <div class="text-sm text-slate-600">No tickets found.</div>
                    @else
                        <div class="overflow-hidden rounded-xl border border-slate-200 shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                                ID</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                                Requester</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                                Title</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                                Due</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                                Instructions</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-4 py-3 text-right text-xs font-bold text-slate-900 uppercase tracking-wider">
                                                Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        @foreach ($tickets as $ticket)
                                            <tr class="odd:bg-white even:bg-slate-50/30 hover:bg-slate-50 transition-colors">
                                                <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-gray-900">
                                                    <button type="button" x-data="" class="text-blue-700 hover:text-slate-900 hover:underline font-semibold"
                                                        x-on:click.prevent="$dispatch('open-modal', 'ticket-history-{{ $ticket->id }}')">
                                                        #{{ $ticket->id }}
                                                    </button>

                                                    <x-modal name="ticket-history-{{ $ticket->id }}" maxWidth="2xl" focusable>
                                                        <div class="p-6">
                                                            <div class="flex items-start justify-between gap-4">
                                                                <div>
                                                                    <h2 class="text-lg font-semibold text-gray-900">Ticket
                                                                        History #{{ $ticket->id }}</h2>
                                                                    <div class="mt-1 text-sm text-slate-600">{{ $ticket->title }}
                                                                    </div>
                                                                </div>
                                                                <x-secondary-button
                                                                    x-on:click="$dispatch('close-modal', 'ticket-history-{{ $ticket->id }}')">Close</x-secondary-button>
                                                            </div>

                                                            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                                <div>
                                                                    <div class="text-sm text-slate-600">Current Status</div>
                                                                    <div class="font-medium">{{ $ticket->status }}</div>
                                                                </div>
                                                                <div>
                                                                    <div class="text-sm text-slate-600">Requester</div>
                                                                    <div class="font-medium">
                                                                        {{ $ticket->requester?->name ?? '-' }}</div>
                                                                </div>
                                                                <div>
                                                                    <div class="text-sm text-slate-600">Approval</div>
                                                                    <div class="font-medium">
                                                                        {{ $ticket->approvalUser?->name ?? '-' }}</div>
                                                                </div>
                                                                <div>
                                                                    <div class="text-sm text-slate-600">Updated</div>
                                                                    <div class="font-medium">
                                                                        {{ $ticket->updated_at?->format('Y-m-d H:i') }}</div>
                                                                </div>
                                                            </div>

                                                            @if($ticket->attachments && $ticket->attachments->count() > 0)
                                                                <div class="mt-4">
                                                                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Attachments ({{ $ticket->attachments->count() }})</h3>
                                                                    <div class="grid grid-cols-1 gap-2">
                                                                        @foreach($ticket->attachments as $attachment)
                                                                            <a href="{{ route('attachments.download', $attachment) }}" 
                                                                               class="flex items-center gap-2 p-2 border border-slate-200 rounded hover:bg-gray-50 text-sm">
                                                                                @if($attachment->isPdf())
                                                                                    <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                                        <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                                                                    </svg>
                                                                                @elseif($attachment->isImage())
                                                                                    <svg class="w-6 h-6 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                                                    </svg>
                                                                                @else
                                                                                    <svg class="w-6 h-6 text-gray-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                                                    </svg>
                                                                                @endif
                                                                                <span class="flex-1 truncate">{{ $attachment->original_filename }}</span>
                                                                                <span class="text-xs text-gray-500">{{ $attachment->getFileSizeHumanAttribute() }}</span>
                                                                            </a>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <div class="mt-6 overflow-hidden rounded-lg border border-slate-200">
                                                                <div class="overflow-x-auto">
                                                                    <table class="min-w-full divide-y divide-slate-200">
                                                                        <thead class="bg-gray-50">
                                                                            <tr>
                                                                                <th
                                                                                    class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                                                                    Time</th>
                                                                                <th
                                                                                    class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                                                                    User</th>
                                                                                <th
                                                                                    class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                                                                    From</th>
                                                                                <th
                                                                                    class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                                                                    To</th>
                                                                                <th
                                                                                    class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                                                                    Remark</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="divide-y divide-slate-200 bg-white">
                                                                            @foreach (($ticket->statusHistories ?? collect())->sortByDesc('id') as $h)
                                                                                <tr
                                                                                    class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                                                                    <td
                                                                                        class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                                                                        {{ $h->created_at?->format('Y-m-d H:i') }}
                                                                                    </td>
                                                                                    <td
                                                                                        class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                                                                        {{ $h->user?->name ?? 'System' }}</td>
                                                                                    <td
                                                                                        class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                                                                        {{ $h->from_status ?? '—' }}</td>
                                                                                    <td
                                                                                        class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                                                                        {{ $h->to_status }}</td>
                                                                                    <td class="px-4 py-3 text-sm text-slate-700">
                                                                                        {{ $h->remark ?? '—' }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </x-modal>
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                                    {{ $ticket->requester?->name ?? '-' }}</td>
                                                <td class="min-w-[16rem] px-4 py-3 text-sm font-medium text-gray-900">
                                                    <div class="line-clamp-2">{{ $ticket->title }}</div>
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                                    {{ $ticket->it_due_at ? $ticket->it_due_at->format('Y-m-d H:i') : '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-slate-700">
                                                    <div class="line-clamp-2">{{ $ticket->it_instructions ?? '-' }}</div>
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                                    @php
                                                        $statusValue = strtolower((string) $ticket->status);
                                                        $statusLabel = match ($statusValue) {
                                                            'it_assigned' => 'Assigned',
                                                            'it_reopened' => 'Reopened',
                                                            'it_in_progress' => 'In Progress',
                                                            'it_completed' => 'Completed (Awaiting IT Manager Confirm)',
                                                            'it_mgr_confirmed' => 'IT Manager Confirmed',
                                                            'dept_confirmed' => 'Dept Confirmed',
                                                            'requester_confirmed' => 'Requester Confirmed',
                                                            default => (string) $ticket->status,
                                                        };
                                                    @endphp
                                                    {{ $statusLabel }}
                                                    @if (($ticket->statusHistories ?? collect())->count() > 0)
                                                        <div class="mt-1 text-xs text-gray-500">
                                                            @foreach ($ticket->statusHistories->sortByDesc('id')->take(2) as $h)
                                                                <div>
                                                                    {{ $h->created_at?->format('Y-m-d H:i') }} —
                                                                    {{ $h->user?->name ?? 'System' }}: {{ $h->from_status ?? '—' }} →
                                                                    {{ $h->to_status }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                                    @if (in_array($ticket->status, ['it_assigned', 'it_reopened'], true))
                                                        <form method="POST" action="{{ route('tickets.it_member_start', $ticket) }}"
                                                            class="inline-flex">
                                                            @csrf
                                                            <x-primary-button>
                                                                Start
                                                            </x-primary-button>
                                                        </form>
                                                    @elseif ($ticket->status === 'it_in_progress')
                                                        <form method="POST"
                                                            action="{{ route('tickets.it_member_complete', $ticket) }}"
                                                            class="inline-flex">
                                                            @csrf
                                                            <x-primary-button>
                                                                Complete
                                                            </x-primary-button>
                                                        </form>
                                                    @else
                                                        <span class="text-sm text-slate-700">{{ $ticket->status }}</span>
                                                    @endif

                                                    <div class="mt-2 text-right text-xs text-gray-500">
                                                        Status: {{ $ticket->status }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>