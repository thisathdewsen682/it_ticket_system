<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight">
                {{ __('Approval Requests') }}
            </h2>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($tickets->count() === 0)
                        <div class="text-sm text-slate-600">No pending approval requests.</div>
                    @else
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <div class="text-sm text-slate-600">
                                Pending: <span class="font-medium text-gray-900">{{ $tickets->count() }}</span>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-lg border border-slate-200">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                                ID</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                                Requester</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                                Title</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                                Priority</th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                                Created</th>
                                            <th
                                                class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                                Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 bg-white">
                                        @foreach ($tickets as $ticket)
                                            <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                                    <button type="button" x-data="" class="text-indigo-700 hover:underline"
                                                        x-on:click.prevent="$dispatch('open-modal', 'ticket-history-{{ $ticket->id }}')">
                                                        #{{ $ticket->id }}
                                                    </button>

                                                    <x-modal name="ticket-history-{{ $ticket->id }}" maxWidth="6xl" focusable>
                                                        <div class="p-6">
                                                            <div class="flex items-start justify-between gap-4">
                                                                <div>
                                                                    <h2 class="text-lg font-semibold text-gray-900">Ticket History #{{ $ticket->id }}</h2>
                                                                    <div class="mt-1 text-sm text-slate-600">{{ $ticket->title }}</div>
                                                                </div>
                                                                <x-secondary-button x-on:click="$dispatch('close-modal', 'ticket-history-{{ $ticket->id }}')">Close</x-secondary-button>
                                                            </div>

                                                            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                                <div>
                                                                    <div class="text-sm text-slate-600">Current Status</div>
                                                                    <div class="font-medium">{{ $ticket->status }}</div>
                                                                </div>
                                                                <div>
                                                                    <div class="text-sm text-slate-600">Requester</div>
                                                                    <div class="font-medium">{{ $ticket->requester?->name ?? '-' }}</div>
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
                                                                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Time</th>
                                                                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">User</th>
                                                                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">From</th>
                                                                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">To</th>
                                                                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Remark</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody class="divide-y divide-slate-200 bg-white">
                                                                            @foreach (($ticket->statusHistories ?? collect())->sortByDesc('id') as $h)
                                                                                <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $h->created_at?->format('Y-m-d H:i') }}</td>
                                                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $h->user?->name ?? 'System' }}</td>
                                                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $h->from_status ?? '—' }}</td>
                                                                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">{{ $h->to_status }}</td>
                                                                                    <td class="px-4 py-3 text-sm text-slate-700">{{ $h->remark ?? '—' }}</td>
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
                                                    {{ $ticket->requester?->name ?? '-' }}
                                                </td>
                                                <td class="min-w-[16rem] px-4 py-3 text-sm font-medium text-gray-900">
                                                    <div class="line-clamp-2">{{ $ticket->title }}</div>
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm">
                                                    @php
                                                        $priorityClasses = match ($ticket->priority) {
                                                            'High' => 'bg-red-50 text-red-700 ring-red-200',
                                                            'Normal' => 'bg-slate-50 text-indigo-700 ring-indigo-200',
                                                            'Low' => 'bg-green-50 text-green-700 ring-green-200',
                                                            default => 'bg-gray-100 text-slate-900 ring-slate-200',
                                                        };
                                                    @endphp
                                                    <span
                                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $priorityClasses }}">
                                                        {{ $ticket->priority }}
                                                    </span>
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3 text-sm text-slate-700">
                                                    {{ $ticket->created_at?->format('Y-m-d H:i') }}
                                                </td>
                                                <td class="whitespace-nowrap px-4 py-3">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <form method="POST" action="{{ route('tickets.approve', $ticket) }}">
                                                            @csrf
                                                            <x-primary-button>
                                                                Approve
                                                            </x-primary-button>
                                                        </form>

                                                        <form method="POST" action="{{ route('tickets.reject', $ticket) }}"
                                                            class="flex items-center gap-2">
                                                            @csrf
                                                            <input type="text" name="remark" required
                                                                placeholder="Reject reason"
                                                                class="block w-48 rounded-md border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                                            <x-danger-button>
                                                                Reject
                                                            </x-danger-button>
                                                        </form>
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