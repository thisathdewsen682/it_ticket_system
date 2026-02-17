<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load(['role', 'roles']);
        
        // Get all user roles
        $userRoles = $user->getAllRoleNames();
        
        // Determine active role tab from request or default to primary role
        $activeRole = $request->get('role_tab', $user->role->name);
        
        // Ensure active role is one the user actually has
        if (!in_array($activeRole, $userRoles)) {
            $activeRole = $user->role->name;
        }
        
        // Get data based on active role
        $dashboardData = $this->getDashboardDataForRole($activeRole, $user, $request);
        
        return view('dashboard.unified', [
            'userRoles' => $userRoles,
            'activeRole' => $activeRole,
            'dashboardData' => $dashboardData,
            'user' => $user,
        ]);
    }
    
    private function getDashboardDataForRole(string $role, User $user, Request $request): array
    {
        return match ($role) {
            'employee' => $this->getEmployeeDashboard($user, $request),
            'dept_manager', 'section_manager' => $this->getManagerDashboard($user, $request),
            'it-dept-manager' => $this->getItDeptManagerDashboard($user, $request),
            'it_manager' => $this->getItManagerDashboard($user, $request),
            'it_member' => $this->getItMemberDashboard($user, $request),
            default => [],
        };
    }
    
    private function getEmployeeDashboard(User $user, Request $request): array
    {
        $currentRole = $user->role->name;
        
        $approvalUsersQuery = User::query()
            ->with('role')
            ->orderBy('name');

        $approvalUsers = match ($currentRole) {
            'dept_manager' => $approvalUsersQuery
                ->whereKey($user->id)
                ->get(),
            'section_manager' => $approvalUsersQuery
                ->where(function ($q) use ($user) {
                    $q->whereKey($user->id)
                        ->orWhereHas('role', fn($r) => $r->where('name', 'dept_manager'));
                })
                ->get(),
            default => $approvalUsersQuery
                ->whereHas('role', fn($q) => $q->whereIn('name', ['dept_manager', 'section_manager']))
                ->get(),
        };

        $sections = Section::orderBy('name')->get(['id', 'name']);

        return [
            'view' => 'dashboard.partials.employee',
            'approvalUsers' => $approvalUsers,
            'sections' => $sections,
        ];
    }
    
    private function getManagerDashboard(User $user, Request $request): array
    {
        $baseQuery = Ticket::query()
            ->with(['requester:id,name', 'itMember:id,name', 'statusHistories.user:id,name', 'attachments'])
            ->where('approval_user_id', $user->id)
            ->orderByDesc('id');

        $pendingTickets = (clone $baseQuery)
            ->where('status', 'pending')
            ->paginate(10, ['*'], 'pending_page')
            ->appends(['tab' => 'pending', 'role_tab' => $request->get('role_tab')]);

        $approvedTickets = (clone $baseQuery)
            ->whereIn('status', [
                'dept_approved',
                'it_dept_approved',
                'it_assigned',
                'it_reopened',
                'dept_reopened',
                'requester_reopened',
                'it_in_progress',
                'it_completed',
                'it_mgr_confirmed',
            ])
            ->paginate(10, ['*'], 'approved_page')
            ->appends(['tab' => 'approved', 'role_tab' => $request->get('role_tab')]);

        $pendingConfirmationTickets = (clone $baseQuery)
            ->whereIn('status', [
                'it_dept_confirmed_completion',
            ])
            ->paginate(10, ['*'], 'pending_confirmation_page')
            ->appends(['tab' => 'pending_confirmation', 'role_tab' => $request->get('role_tab')]);

        $completedTickets = (clone $baseQuery)
            ->whereIn('status', [
                'dept_confirmed',
                'requester_confirmed',
            ])
            ->paginate(10, ['*'], 'completed_page')
            ->appends(['tab' => 'completed', 'role_tab' => $request->get('role_tab')]);

        $rejectedTickets = (clone $baseQuery)
            ->whereIn('status', [
                'dept_rejected',
                'it_dept_rejected',
                'it_manager_rejected',
            ])
            ->paginate(10, ['*'], 'rejected_page')
            ->appends(['tab' => 'rejected', 'role_tab' => $request->get('role_tab')]);

        return [
            'view' => 'dashboard.partials.manager',
            'pendingTickets' => $pendingTickets,
            'approvedTickets' => $approvedTickets,
            'pendingConfirmationTickets' => $pendingConfirmationTickets,
            'completedTickets' => $completedTickets,
            'rejectedTickets' => $rejectedTickets,
        ];
    }
    
    private function getItDeptManagerDashboard(User $user, Request $request): array
    {
        $baseQuery = Ticket::query()
            ->with(['requester:id,name', 'approvalUser:id,name', 'itMember:id,name', 'statusHistories.user:id,name', 'attachments'])
            ->orderByDesc('id');

        $pendingApprovalTickets = (clone $baseQuery)
            ->where('status', 'dept_approved')
            ->paginate(10, ['*'], 'pending_approval_page')
            ->appends(['tab' => 'pending_approval', 'role_tab' => $request->get('role_tab')]);

        $pendingCompletionTickets = (clone $baseQuery)
            ->where('status', 'it_mgr_confirmed')
            ->paginate(10, ['*'], 'pending_completion_page')
            ->appends(['tab' => 'pending_completion', 'role_tab' => $request->get('role_tab')]);

        $confirmedTickets = (clone $baseQuery)
            ->whereIn('status', ['it_dept_approved', 'it_assigned', 'it_in_progress', 'it_completed'])
            ->paginate(10, ['*'], 'confirmed_page')
            ->appends(['tab' => 'confirmed', 'role_tab' => $request->get('role_tab')]);

        $completedTickets = (clone $baseQuery)
            ->whereIn('status', ['it_dept_confirmed_completion', 'dept_confirmed', 'requester_confirmed'])
            ->paginate(10, ['*'], 'completed_page')
            ->appends(['tab' => 'completed', 'role_tab' => $request->get('role_tab')]);

        $rejectedTickets = (clone $baseQuery)
            ->where('status', 'it_dept_rejected')
            ->paginate(10, ['*'], 'rejected_page')
            ->appends(['tab' => 'rejected', 'role_tab' => $request->get('role_tab')]);

        return [
            'view' => 'dashboard.partials.it_dept_manager',
            'pendingApprovalTickets' => $pendingApprovalTickets,
            'pendingCompletionTickets' => $pendingCompletionTickets,
            'confirmedTickets' => $confirmedTickets,
            'completedTickets' => $completedTickets,
            'rejectedTickets' => $rejectedTickets,
        ];
    }
    
    private function getItManagerDashboard(User $user, Request $request): array
    {
        $itMembers = User::query()
            ->with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'it_member'))
            ->orderBy('name')
            ->get();

        $baseQuery = Ticket::query()
            ->with(['requester:id,name', 'approvalUser:id,name', 'itMember:id,name', 'statusHistories.user:id,name', 'attachments'])
            ->orderByDesc('id');

        $approvedTickets = (clone $baseQuery)
            ->whereIn('status', ['it_dept_approved', 'it_dept_reopened_completion'])
            ->paginate(10, ['*'], 'approved_page')
            ->appends(['tab' => 'approved', 'role_tab' => $request->get('role_tab')]);

        $assigningTickets = (clone $baseQuery)
            ->whereIn('status', ['it_assigned', 'it_in_progress'])
            ->paginate(10, ['*'], 'assigning_page')
            ->appends(['tab' => 'assigning', 'role_tab' => $request->get('role_tab')]);

        $reopenedTickets = (clone $baseQuery)
            ->whereIn('status', ['it_reopened', 'dept_reopened', 'requester_reopened'])
            ->paginate(10, ['*'], 'reopened_page')
            ->appends(['tab' => 'reopened', 'role_tab' => $request->get('role_tab')]);

        $pendingConfirmationTickets = (clone $baseQuery)
            ->where('status', 'it_completed')
            ->paginate(10, ['*'], 'pending_confirmation_page')
            ->appends(['tab' => 'pending_confirmation', 'role_tab' => $request->get('role_tab')]);

        $confirmedTickets = (clone $baseQuery)
            ->where('status', 'it_mgr_confirmed')
            ->paginate(10, ['*'], 'confirmed_page')
            ->appends(['tab' => 'confirmed', 'role_tab' => $request->get('role_tab')]);

        $completedTickets = (clone $baseQuery)
            ->whereIn('status', ['dept_confirmed', 'requester_confirmed'])
            ->paginate(10, ['*'], 'completed_page')
            ->appends(['tab' => 'completed', 'role_tab' => $request->get('role_tab')]);

        return [
            'view' => 'dashboard.partials.it_manager',
            'approvedTickets' => $approvedTickets,
            'assigningTickets' => $assigningTickets,
            'reopenedTickets' => $reopenedTickets,
            'pendingConfirmationTickets' => $pendingConfirmationTickets,
            'confirmedTickets' => $confirmedTickets,
            'completedTickets' => $completedTickets,
            'itMembers' => $itMembers,
        ];
    }
    
    private function getItMemberDashboard(User $user, Request $request): array
    {
        $baseQuery = Ticket::query()
            ->with(['requester:id,name', 'approvalUser:id,name', 'statusHistories.user:id,name', 'attachments'])
            ->where('it_member_id', $user->id)
            ->orderByDesc('id');

        $assigningTickets = (clone $baseQuery)
            ->whereIn('status', ['it_assigned', 'it_in_progress'])
            ->paginate(10, ['*'], 'assigning_page')
            ->appends(['tab' => 'assigning', 'role_tab' => $request->get('role_tab')]);

        $reopenedTickets = Ticket::query()
            ->with(['requester:id,name', 'approvalUser:id,name', 'statusHistories.user:id,name', 'attachments'])
            ->whereIn('status', ['it_reopened', 'dept_reopened', 'requester_reopened'])
            ->where(function ($query) use ($user) {
                $query->where('it_member_id', $user->id)
                    ->orWhereHas('statusHistories', function ($history) use ($user) {
                        $history->where('user_id', $user->id);
                    });
            })
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'reopened_page')
            ->appends(['tab' => 'reopened', 'role_tab' => $request->get('role_tab')]);

        $completedTickets = (clone $baseQuery)
            ->where('status', 'it_completed')
            ->paginate(10, ['*'], 'completed_page')
            ->appends(['tab' => 'completed', 'role_tab' => $request->get('role_tab')]);

        $confirmedTickets = (clone $baseQuery)
            ->whereIn('status', ['it_mgr_confirmed', 'dept_confirmed', 'requester_confirmed'])
            ->paginate(10, ['*'], 'confirmed_page')
            ->appends(['tab' => 'confirmed', 'role_tab' => $request->get('role_tab')]);

        return [
            'view' => 'dashboard.partials.it_member',
            'assigningTickets' => $assigningTickets,
            'reopenedTickets' => $reopenedTickets,
            'completedTickets' => $completedTickets,
            'confirmedTickets' => $confirmedTickets,
        ];
    }
}
