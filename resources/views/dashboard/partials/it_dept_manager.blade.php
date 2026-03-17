{{-- IT Department Manager Dashboard Partial --}}
<div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
    <div class="p-8 text-gray-900">
        <div class="mb-6 text-sm text-slate-700 font-medium bg-purple-50 border border-purple-200 rounded-lg p-4">
            Review and confirm jobs approved by department managers before sending to IT Manager for assignment.
        </div>

        @php
            $tab = request('tab', 'pending_approval');
            $tickets = match ($tab) {
                'pending_completion' => $pendingCompletionTickets ?? collect(),
                'confirmed' => $confirmedTickets ?? collect(),
                'completed' => $completedTickets ?? collect(),
                'rejected' => $rejectedTickets ?? collect(),
                default => $pendingApprovalTickets ?? collect(),
            };
            $role_tab = request('role_tab');
        @endphp

        <div class="mb-6 flex flex-wrap items-center gap-2">
            <a href="{{ route('dashboard.unified', ['tab' => 'pending_approval', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'pending_approval' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Pending Approval
            </a>
            <a href="{{ route('dashboard.unified', ['tab' => 'pending_completion', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'pending_completion' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Pending Completion Confirm
            </a>
            <a href="{{ route('dashboard.unified', ['tab' => 'confirmed', 'role_tab' => $role_tab]) }}"
                class="inline-flex items-center rounded-lg border px-4 py-2 text-sm font-semibold transition-all shadow-sm {{ $tab === 'confirmed' ? 'border-blue-700 bg-blue-700 text-white shadow-md hover:bg-blue-800' : 'border-slate-300 bg-slate-100 text-slate-800 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-700' }}">
                Confirmed
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
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase">Priority</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase">Approved By</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase">Due Date</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-slate-900 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($tickets as $ticket)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm font-semibold text-blue-600">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="hover:underline">
                                            #{{ $ticket->id }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $ticket->requester?->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm font-medium">
                                        <div class="max-w-xs truncate" title="{{ $ticket->title }}">
                                            {{ $ticket->title }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ $ticket->category }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $ticket->priority === 'High' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $ticket->priority === 'Normal' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $ticket->priority === 'Low' ? 'bg-green-100 text-green-800' : '' }}">
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>
                                    @php
                                        $approverName = $ticket->approvalUser?->name
                                            ?? ($ticket->statusHistories ?? collect())
                                                ->sortByDesc('id')
                                                ->firstWhere('to_status', 'dept_approved')
                                                ?->user?->name
                                            ?? '-';
                                    @endphp
                                    <td class="px-4 py-3 text-sm">{{ $approverName }}</td>
                                    <td class="px-4 py-3 text-sm">{{ $ticket->needed_by?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        @if ($ticket->status === 'dept_approved')
                                            <div class="flex flex-col items-end gap-2">
                                                <form method="POST" action="{{ route('tickets.it_dept_manager_confirm', $ticket) }}" class="inline">
                                                    @csrf
                                                    <x-primary-button class="bg-purple-600 hover:bg-purple-700">
                                                        Accept
                                                    </x-primary-button>
                                                </form>
                                                <form method="POST" action="{{ route('tickets.it_dept_manager_reject', $ticket) }}"
                                                    class="flex items-center justify-end gap-2">
                                                    @csrf
                                                    <input type="text" name="remark" required
                                                        placeholder="Reject reason"
                                                        class="block w-48 rounded-md border-slate-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                                                    <x-danger-button>
                                                        Reject
                                                    </x-danger-button>
                                                </form>
                                            </div>
                                        @elseif ($ticket->status === 'it_mgr_confirmed')
                                            <div class="flex flex-col items-end gap-2">
                                                <form method="POST" action="{{ route('tickets.it_dept_manager_confirm_completion', $ticket) }}" class="inline">
                                                    @csrf
                                                    <x-primary-button class="bg-green-600 hover:bg-green-700">
                                                        Confirm Completion
                                                    </x-primary-button>
                                                </form>
                                                <form method="POST" action="{{ route('tickets.it_dept_manager_reopen_completion', $ticket) }}"
                                                    class="flex items-center justify-end gap-2">
                                                    @csrf
                                                    <input type="text" name="remark" required
                                                        placeholder="Reopen reason"
                                                        class="block w-48 rounded-md border-slate-300 text-sm shadow-sm focus:border-purple-500 focus:ring-purple-500" />
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 active:bg-orange-900 focus:outline-none focus:border-orange-900 focus:ring ring-orange-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                        Reopen
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-sm text-slate-500">{{ $ticket->status }}</span>
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
