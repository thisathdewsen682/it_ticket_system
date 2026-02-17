{{-- IT Manager Dashboard Partial --}}
<div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
    <div class="p-8 text-gray-900">
        <div class="mb-6 text-sm text-slate-700 font-medium bg-slate-50 border border-slate-200 rounded-lg p-4">
            Manage IT Department tickets and assign to IT members.
        </div>

        @php
            $tab = request('tab', 'approved');
            $tickets = match ($tab) {
                'assigning' => $assigningTickets ?? collect(),
                'reopened' => $reopenedTickets ?? collect(),
                'pending_confirmation' => $pendingConfirmationTickets ?? collect(),
                'confirmed' => $confirmedTickets ?? collect(),
                'completed' => $completedTickets ?? collect(),
                default => $approvedTickets ?? collect(),
            };
            $role_tab = request('role_tab');
        @endphp

        <div class="mb-6 flex flex-wrap items-center gap-2">
            <a href="{{ route('dashboard.unified', ['tab' => 'approved', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'approved' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Approved
            </a>
            <a href="{{ route('dashboard.unified', ['tab' => 'assigning', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'assigning' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Assigning
            </a>
            <a href="{{ route('dashboard.unified', ['tab' => 'reopened', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'reopened' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Reopened
            </a>
            <a href="{{ route('dashboard.unified', ['tab' => 'pending_confirmation', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'pending_confirmation' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Pending Confirmation
            </a>
            <a href="{{ route('dashboard.unified', ['tab' => 'confirmed', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'confirmed' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Confirmed
            </a>
            <a href="{{ route('dashboard.unified', ['tab' => 'completed', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'completed' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Completed
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
                                    <td class="px-4 py-3 text-right">
                                        @if (in_array($ticket->status, ['it_dept_approved', 'it_dept_reopened_completion', 'it_reopened', 'dept_reopened', 'requester_reopened']))
                                            <div class="flex flex-col gap-2">
                                                @if ($ticket->status === 'it_dept_reopened_completion')
                                                    <div class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded mb-1">
                                                        <strong>Reopened by IT Dept Manager</strong>
                                                    </div>
                                                @endif
                                                <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="inline-flex gap-2">
                                                    @csrf
                                                    <select name="it_member_id" required class="rounded border-slate-300 text-sm">
                                                        <option value="">Select IT Member</option>
                                                        @foreach($itMembers as $member)
                                                            <option value="{{ $member->id }}" {{ $ticket->it_member_id == $member->id ? 'selected' : '' }}>
                                                                {{ $member->name }}{{ $ticket->it_member_id == $member->id ? ' (Current)' : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <x-primary-button>{{ $ticket->status === 'it_dept_reopened_completion' ? 'Reassign' : 'Assign' }}</x-primary-button>
                                                </form>
                                                <form method="POST" action="{{ route('tickets.it_manager_reject', $ticket) }}"
                                                    class="flex items-center justify-end gap-2">
                                                    @csrf
                                                    <input type="text" name="remark" required
                                                        placeholder="Reject reason"
                                                        class="block w-48 rounded-md border-slate-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500" />
                                                    <x-danger-button>
                                                        Reject
                                                    </x-danger-button>
                                                </form>
                                            </div>
                                        @elseif ($ticket->status === 'it_completed')
                                            <form method="POST" action="{{ route('tickets.it_manager_confirm', $ticket) }}" class="inline">
                                                @csrf
                                                <x-primary-button>Confirm</x-primary-button>
                                            </form>
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