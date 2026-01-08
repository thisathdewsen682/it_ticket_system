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