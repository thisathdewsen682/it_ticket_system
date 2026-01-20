<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-emerald-900 leading-tight">
                {{ __('IT Manager Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-4 text-green-800">
                    <div class="text-sm font-medium">
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-red-800">
                    <div class="text-sm font-medium mb-2">Error:</div>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden border border-emerald-200 shadow-lg sm:rounded-xl">
                <div class="p-8 text-gray-900">
                    <div class="mb-6 text-sm text-gray-700 font-medium bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                        Tickets approved by department managers will appear here.
                    </div>

                    @php
                        $tab = request('tab', 'approved');
                        $tickets = match ($tab) {
                            'assigning' => $assigningTickets ?? collect(),
                            'pending_confirmation' => $pendingConfirmationTickets ?? collect(),
                            'confirmed' => $confirmedTickets ?? collect(),
                            'completed' => $completedTickets ?? collect(),
                            default => $approvedTickets ?? collect(),
                        };
                    @endphp

                    <div class="mb-6 flex flex-wrap items-center gap-2">
                        <a href="{{ route('dashboard.it_manager', ['tab' => 'approved']) }}"
                            class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'approved' ? 'border-emerald-500 bg-emerald-600 text-white shadow-md' : 'border-gray-300 bg-white text-gray-700 hover:bg-emerald-50 hover:border-emerald-300' }}">
                            Approved
                        </a>
                        <a href="{{ route('dashboard.it_manager', ['tab' => 'assigning']) }}"
                            class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'assigning' ? 'border-emerald-500 bg-emerald-600 text-white shadow-md' : 'border-gray-300 bg-white text-gray-700 hover:bg-emerald-50 hover:border-emerald-300' }}">
                            Assigning
                        </a>
                        <a href="{{ route('dashboard.it_manager', ['tab' => 'pending_confirmation']) }}"
                            class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'pending_confirmation' ? 'border-emerald-500 bg-emerald-600 text-white shadow-md' : 'border-gray-300 bg-white text-gray-700 hover:bg-emerald-50 hover:border-emerald-300' }}">
                            Pending Confirmation
                        </a>
                        <a href="{{ route('dashboard.it_manager', ['tab' => 'confirmed']) }}"
                            class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'confirmed' ? 'border-emerald-500 bg-emerald-600 text-white shadow-md' : 'border-gray-300 bg-white text-gray-700 hover:bg-emerald-50 hover:border-emerald-300' }}">
                            Confirmed
                        </a>
                        <a href="{{ route('dashboard.it_manager', ['tab' => 'completed']) }}"
                            class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'completed' ? 'border-emerald-500 bg-emerald-600 text-white shadow-md' : 'border-gray-300 bg-white text-gray-700 hover:bg-emerald-50 hover:border-emerald-300' }}">
                            Completed
                        </a>
                    </div>

                    @if (!isset($tickets) || $tickets->count() === 0)
                        <div class="text-sm text-gray-600">No tickets found.</div>
                    @else
                        <div class="overflow-hidden rounded-xl border border-emerald-200 shadow-sm">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-emerald-200">
                                    <thead class="bg-emerald-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-emerald-900 uppercase tracking-wider">
                                                ID</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-emerald-900 uppercase tracking-wider">
                                                Requester</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-emerald-900 uppercase tracking-wider">
                                                Title</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-emerald-900 uppercase tracking-wider">
                                                Category</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-emerald-900 uppercase tracking-wider">
                                                Priority</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-emerald-900 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-emerald-900 uppercase tracking-wider">
                                                Assigned</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-bold text-emerald-900 uppercase tracking-wider">
                                                Due Date</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Created</th>
                                            <th
                                                class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach ($tickets as $ticket)
                                                                            <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                                                    <button type="button" x-data="" class="text-indigo-700 hover:underline"
                                                                                        x-on:click.prevent="$dispatch('open-modal', 'ticket-history-{{ $ticket->id }}')">
                                                                                        #{{ $ticket->id }}
                                                                                    </button>

                                                                                    <x-modal name="ticket-history-{{ $ticket->id }}" maxWidth="2xl" focusable>
                                                                                        <div class="p-6">
                                                                                            <div class="flex items-start justify-between gap-4">
                                                                                                <div>
                                                                                                    <h2 class="text-lg font-semibold text-gray-900">Ticket History #{{ $ticket->id }}</h2>
                                                                                                    <div class="mt-1 text-sm text-gray-600">{{ $ticket->title }}</div>
                                                                                                </div>
                                                                                                <x-secondary-button x-on:click="$dispatch('close-modal', 'ticket-history-{{ $ticket->id }}')">Close</x-secondary-button>
                                                                                            </div>

                                                                                            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                                                                <div>
                                                                                                    <div class="text-sm text-gray-600">Current Status</div>
                                                                                                    <div class="font-medium">{{ $ticket->status }}</div>
                                                                                                </div>
                                                                                                <div>
                                                                                                    <div class="text-sm text-gray-600">Requester</div>
                                                                                                    <div class="font-medium">{{ $ticket->requester?->name ?? '-' }}</div>
                                                                                                </div>
                                                                                                <div>
                                                                                                    <div class="text-sm text-gray-600">IT Member</div>
                                                                                                    <div class="font-medium">{{ $ticket->itMember?->name ?? '-' }}</div>
                                                                                                </div>
                                                                                                <div>
                                                                                                    <div class="text-sm text-gray-600">Updated</div>
                                                                                                    <div class="font-medium">{{ $ticket->updated_at?->format('Y-m-d H:i') }}</div>
                                                                                                </div>
                                                                                            </div>

                                                                                            @if($ticket->attachments && $ticket->attachments->count() > 0)
                                                                                                <div class="mt-4">
                                                                                                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Attachments ({{ $ticket->attachments->count() }})</h3>
                                                                                                    <div class="grid grid-cols-1 gap-2">
                                                                                                        @foreach($ticket->attachments as $attachment)
                                                                                                            <a href="{{ route('attachments.download', $attachment) }}" 
                                                                                                               class="flex items-center gap-2 p-2 border border-gray-200 rounded hover:bg-gray-50 text-sm">
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

                                                                                            <div class="mt-6 overflow-hidden rounded-lg border border-gray-200">
                                                                                                <div class="overflow-x-auto">
                                                                                                    <table class="min-w-full divide-y divide-gray-200">
                                                                                                        <thead class="bg-gray-50">
                                                                                                            <tr>
                                                                                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                                                                                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                                                                                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">From</th>
                                                                                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">To</th>
                                                                                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Remark</th>
                                                                                                            </tr>
                                                                                                        </thead>
                                                                                                        <tbody class="divide-y divide-gray-200 bg-white">
                                                                                                            @foreach (($ticket->statusHistories ?? collect())->sortByDesc('id') as $h)
                                                                                                                <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                                                                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $h->created_at?->format('Y-m-d H:i') }}</td>
                                                                                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $h->user?->name ?? 'System' }}</td>
                                                                                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $h->from_status ?? '—' }}</td>
                                                                                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $h->to_status }}</td>
                                                                                                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $h->remark ?? '—' }}</td>
                                                                                                                </tr>
                                                                                                            @endforeach
                                                                                                        </tbody>
                                                                                                    </table>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </x-modal>
                                                                                </td>
                                                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                                                    {{ $ticket->requester?->name ?? '-' }}</td>
                                                                                <td class="min-w-[16rem] px-4 py-3 text-sm font-medium text-gray-900">
                                                                                    <div class="line-clamp-2">{{ $ticket->title }}</div>
                                                                                </td>
                                                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                                                    {{ $ticket->category }}</td>
                                                                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                                                                    @php
                                                                                        $priorityClasses = match ($ticket->priority) {
                                                                                            'High' => 'bg-red-50 text-red-700 ring-red-200',
                                                                                            'Normal' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
                                                                                            'Low' => 'bg-green-50 text-green-700 ring-green-200',
                                                                                            default => 'bg-gray-100 text-gray-800 ring-gray-200',
                                                                                        };
                                                                                    @endphp
                                             <span
                                                                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $priorityClasses }}">
                                                                                        {{ $ticket->priority }}
                                                                                    </span>
                                                                                </td>
                                                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                                                    @php
                                                                                        $statusValue = strtolower((string) $ticket->status);
                                                                                        $statusLabel = match ($statusValue) {
                                                                                            'dept_approved' => 'Dept Approved',
                                                                                            'it_assigned' => 'IT Assigned',
                                                                                            'it_reopened' => 'Reopened (Back to IT)',
                                                                                            'it_in_progress' => 'In Progress',
                                                                                            'it_completed' => 'Completed (Awaiting IT Confirm)',
                                                                                            'it_mgr_confirmed' => 'IT Manager Confirmed',
                                                                                            'dept_confirmed' => 'Department Confirmed',
                                                                                            'requester_confirmed' => 'Requester Confirmed',
                                                                                            default => (string) $ticket->status,
                                                                                        };
                                                                                    @endphp
                                                                                    {{ $statusLabel }}
                                                                                    @if (($ticket->statusHistories ?? collect())->count() > 0)
                                                                                        <div class="mt-1 text-xs text-gray-500">
                                                                                            @foreach ($ticket->statusHistories->sortByDesc('id')->take(2) as $h)
                                                                                                <div>
                                                                                                    {{ $h->created_at?->format('Y-m-d H:i') }} — {{ $h->user?->name ?? 'System' }}: {{ $h->from_status ?? '—' }} → {{ $h->to_status }}@if($h->remark) — {{ $h->remark }}@endif
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                                                    {{ $ticket->itMember?->name ?? '-' }}
                                                                                </td>
                                                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                                                    @if($ticket->needed_by)
                                                                                        <span class="{{ now()->greaterThan($ticket->needed_by) ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                                                                            {{ $ticket->needed_by->format('Y-m-d') }}
                                                                                        </span>
                                                                                    @else
                                                                                        -
                                                                                    @endif
                                                                                </td>
                                                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                                                    {{ $ticket->created_at?->format('Y-m-d H:i') }}</td>
                                                                                <td class="whitespace-nowrap px-4 py-3">
                                                                                    @if (in_array($ticket->status, ['dept_approved', 'it_reopened'], true))
                                                                                        <form method="POST" action="{{ route('tickets.assign', $ticket) }}"
                                                                                            class="flex flex-col items-end gap-2">
                                                                                            @csrf

                                                                                            <div class="flex flex-wrap items-center justify-end gap-2">
                                                                                                <select name="it_member_id" required
                                                                                                    class="block w-44 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                                                                    <option value="">Select IT Member</option>
                                                                                                    @foreach (($itMembers ?? []) as $member)
                                                                                                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                                                                                                    @endforeach
                                                                                                </select>

                                                                                                <div class="flex flex-col">
                                                                                                    <label class="text-xs text-gray-600 mb-1">Completion Date *</label>
                                                                                                    <input type="datetime-local" name="it_due_at" required
                                                                                                        class="block w-48 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                                                                                </div>
                                                                                            </div>

                                                                                            <textarea name="it_instructions" rows="2"
                                                                                                placeholder="Instructions (optional)"
                                                                                                class="block w-[22rem] max-w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>

                                                                                            <x-primary-button>
                                                                                                Assign
                                                                                            </x-primary-button>
                                                                                        </form>
                                                                                    @elseif ($ticket->status === 'it_completed')
                                                                                        <div class="flex flex-col items-end gap-2">
                                                                                            <form method="POST" action="{{ route('tickets.it_manager_confirm', $ticket) }}" class="flex justify-end">
                                                                                                @csrf
                                                                                                <x-primary-button>
                                                                                                    Confirm
                                                                                                </x-primary-button>
                                                                                            </form>

                                                                                            <form method="POST" action="{{ route('tickets.it_manager_reopen', $ticket) }}" class="flex items-center justify-end gap-2">
                                                                                                @csrf
                                                                                                <input type="text" name="remark" required placeholder="Reopen reason"
                                                                                                    class="block w-48 rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                                                                                <x-danger-button>
                                                                                                    Reopen
                                                                                                </x-danger-button>
                                                                                            </form>
                                                                                        </div>
                                                                                    @else
                                                                                        <div class="text-right text-sm text-gray-700">{{ $ticket->status }}</div>
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