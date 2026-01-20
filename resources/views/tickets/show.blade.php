<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Ticket History') }} #{{ $ticket->id }}
            </h2>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <div class="text-sm text-gray-600">Title</div>
                            <div class="font-medium">{{ $ticket->title }}</div>
                        </div>
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
                            <div class="text-sm text-gray-600">Approval Person</div>
                            <div class="font-medium">{{ $ticket->approvalUser?->name ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Updated</div>
                            <div class="font-medium">{{ $ticket->updated_at?->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>

                    @if($ticket->attachments && $ticket->attachments->count() > 0)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Attachments</h3>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($ticket->attachments as $attachment)
                                    <a href="{{ route('attachments.download', $attachment) }}" 
                                       class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition">
                                        <div class="flex-shrink-0">
                                            @if($attachment->isPdf())
                                                <svg class="w-10 h-10 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/>
                                                </svg>
                                            @elseif($attachment->isImage())
                                                <svg class="w-10 h-10 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                </svg>
                                            @else
                                                <svg class="w-10 h-10 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $attachment->original_filename }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $attachment->getFileSizeHumanAttribute() }}
                                            </p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
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
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Time</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            User</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            From</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            To</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Remark</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach (($ticket->statusHistories ?? collect())->sortByDesc('id') as $h)
                                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                {{ $h->created_at?->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                {{ $h->user?->name ?? 'System' }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                {{ $h->from_status ?? '—' }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                {{ $h->to_status }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700">
                                                {{ $h->remark ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>