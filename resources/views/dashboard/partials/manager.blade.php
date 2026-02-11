{{-- Manager Dashboard Partial --}}
<div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
    <div class="p-8 text-gray-900">
        <div class="mb-6 text-sm text-slate-700 font-medium bg-slate-50 border border-slate-200 rounded-lg p-4">
            Track Jobs you approved and their current IT status.
        </div>

        @php
            $tab = request('tab', 'pending');
            $tickets = match ($tab) {
                'approved' => $approvedTickets ?? collect(),
                'pending_confirmation' => $pendingConfirmationTickets ?? collect(),
                'completed' => $completedTickets ?? collect(),
                'rejected' => $rejectedTickets ?? collect(),
                default => $pendingTickets ?? collect(),
            };
            $role_tab = request('role_tab');
        @endphp

        <div class="mb-6 flex flex-wrap items-center gap-2">
            <a href="{{ route('dashboard.unified', ['tab' => 'pending', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'pending' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Pending
            </a>
            <a href="{{ route('dashboard.unified', ['tab' => 'approved', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'approved' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Approved
            </a>
            <a href="{{ route('dashboard.unified', ['tab' => 'pending_confirmation', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'pending_confirmation' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Pending Confirmation
            </a>
            <a href="{{ route('dashboard.unified', ['tab' => 'completed', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'completed' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Completed
            </a>
            <a href="{{ route('dashboard.unified', ['tab' => 'rejected', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'rejected' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Rejected
            </a>
        </div>

        @if (!isset($tickets) || $tickets->count() === 0)
            <div class="text-sm text-slate-600">No Jobs found.</div>
        @else
            <div class="overflow-hidden rounded-xl border border-slate-200 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase">Requester</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase">Title</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase">IT Member</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase">Due Date</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-slate-900 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($tickets as $ticket)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm font-semibold">#{{ $ticket->id }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $ticket->requester?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $ticket->title }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $ticket->status }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $ticket->itMember?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $ticket->needed_by?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($ticket->status === 'pending')
                                            <form method="POST" action="{{ route('tickets.approve', $ticket) }}" class="inline">
                                                @csrf
                                                <x-primary-button>Approve</x-primary-button>
                                            </form>
                                        @elseif ($ticket->status === 'it_dept_confirmed_completion')
                                            <div class="flex flex-col items-end gap-2">
                                                <div class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded mb-1">
                                                    <strong>Awaiting Your Confirmation</strong>
                                                </div>
                                                <form method="POST" action="{{ route('tickets.dept_confirm_completion', $ticket) }}" class="inline">
                                                    @csrf
                                                    <x-primary-button class="bg-green-600 hover:bg-green-700">
                                                        Confirm Completion
                                                    </x-primary-button>
                                                </form>
                                                <button 
                                                    onclick="openReopenModal({{ $ticket->id }})" 
                                                    type="button"
                                                    class="inline-flex items-center px-3 py-1.5 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                    Reopen
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">{{ $tickets->links() }}</div>
        @endif
    </div>
</div>

<!-- Reopen Modal -->
<div id="reopenModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Reopen Ticket</h3>
            <form id="reopenForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="reopen_remark" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for reopening <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        name="remark" 
                        id="reopen_remark" 
                        rows="4" 
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500"
                        placeholder="Explain why this ticket needs to be reopened..."></textarea>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        onclick="closeReopenModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        Reopen Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openReopenModal(ticketId) {
    const modal = document.getElementById('reopenModal');
    const form = document.getElementById('reopenForm');
    form.action = `/manager/tickets/${ticketId}/reopen`;
    document.getElementById('reopen_remark').value = '';
    modal.classList.remove('hidden');
}

function closeReopenModal() {
    const modal = document.getElementById('reopenModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('reopenModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeReopenModal();
    }
});
</script>
