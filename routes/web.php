<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketApprovalLinkController;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/status/sections', [TicketController::class, 'publicSectionStatus'])
    ->name('public.section_status');
Route::get('/status/sections/{ticket}', [TicketController::class, 'publicTicketHistory'])
    ->name('public.section_status.show');

// Signed links sent via email to approve/reject a ticket.
Route::get('/ticket-approval/{ticket}/{action}', TicketApprovalLinkController::class)
    ->middleware(['auth', 'signed'])
    ->name('tickets.approval_link');



Route::get('/dashboard', function (Request $request) {
    $user = $request->user()?->load('role', 'roles');
    $userRoles = $user->getAllRoleNames();
    
    // If user has multiple roles, redirect to unified dashboard
    if (count($userRoles) > 1) {
        return redirect()->route('dashboard.unified');
    }
    
    // Single role - redirect to role-specific dashboard
        return match ($user?->role?->name) {
            'employee' => redirect()->route('dashboard.employee'),
            'dept_manager', 'section_manager' => redirect()->route('dashboard.manager'),
            'it_manager' => redirect()->route('dashboard.it_manager', ['tab' => 'approved']),
            'it_member' => redirect()->route('dashboard.it_member'),
            default => redirect()->route('tickets.index'),
        };
})->middleware(['auth'])->name('dashboard');

// Unified dashboard for users with multiple roles
Route::get('/dashboard/unified', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard.unified');






// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard/employee', function (Request $request) {
        $currentUser = $request->user()?->load('role');
        $currentRole = $currentUser?->role?->name;

        $approvalUsersQuery = \App\Models\User::query()
            ->with('role')
            ->orderBy('name');

        // Approver rules:
        // - dept_manager: can approve themself
        // - section_manager: can approve themself OR any dept_manager
        // - others (non-IT): can choose dept_manager or section_manager
        $approvalUsers = match ($currentRole) {
            'dept_manager' => $approvalUsersQuery
                ->whereKey($currentUser->id)
                ->get(),
            'section_manager' => $approvalUsersQuery
                ->where(function ($q) use ($currentUser) {
                        $q->whereKey($currentUser->id)
                        ->orWhereHas('role', fn($r) => $r->where('name', 'dept_manager'));
                    })
                ->get(),
            default => $approvalUsersQuery
                ->whereHas('role', fn($q) => $q->whereIn('name', ['dept_manager', 'section_manager']))
                ->get(),
        };

        $sections = Section::orderBy('name')->get(['id', 'name']);

        return view('dashboard.employee', [
            'approvalUsers' => $approvalUsers,
            'sections' => $sections,
        ]);
    })->middleware('role:!it_manager,!it_member')->name('dashboard.employee');

    Route::post('/tickets/store', [TicketController::class, 'store'])
        ->middleware('role:!it_manager,!it_member')
        ->name('tickets.store');

    Route::get('/tickets', [TicketController::class, 'index'])
        ->middleware('role:!it_manager,!it_member')
        ->name('tickets.index');

    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
        ->name('tickets.show');

    Route::get('/attachments/{attachment}/download', [TicketController::class, 'downloadAttachment'])
        ->name('attachments.download');

    Route::get('/approvals', [TicketController::class, 'approvals'])
        ->middleware('role:dept_manager,section_manager')
        ->name('tickets.approvals');

    Route::post('/approvals/{ticket}/approve', [TicketController::class, 'approve'])
        ->middleware('role:dept_manager,section_manager')
        ->name('tickets.approve');

    Route::post('/approvals/{ticket}/reject', [TicketController::class, 'reject'])
        ->middleware('role:dept_manager,section_manager')
        ->name('tickets.reject');

    Route::post('/approvals/{ticket}/confirm-completion', [TicketController::class, 'deptConfirmCompletion'])
        ->middleware('role:dept_manager,section_manager')
        ->name('tickets.dept_confirm_completion');

    // IT Department Manager routes
    Route::post('/it-dept-manager/tickets/{ticket}/confirm', [TicketController::class, 'itDeptManagerConfirm'])
        ->middleware('role:it-dept-manager')
        ->name('tickets.it_dept_manager_confirm');

    Route::post('/it-dept-manager/tickets/{ticket}/reject', [TicketController::class, 'itDeptManagerReject'])
        ->middleware('role:it-dept-manager')
        ->name('tickets.it_dept_manager_reject');

    Route::post('/it-dept-manager/tickets/{ticket}/confirm-completion', [TicketController::class, 'itDeptManagerConfirmCompletion'])
        ->middleware('role:it-dept-manager')
        ->name('tickets.it_dept_manager_confirm_completion');

    Route::post('/it-dept-manager/tickets/{ticket}/reopen-completion', [TicketController::class, 'itDeptManagerReopenCompletion'])
        ->middleware('role:it-dept-manager')
        ->name('tickets.it_dept_manager_reopen_completion');

    Route::post('/it-manager/tickets/{ticket}/assign', [TicketController::class, 'assignToItMember'])
        ->middleware('role:it_manager')
        ->name('tickets.assign');

    Route::post('/it-manager/tickets/{ticket}/reject', [TicketController::class, 'itManagerReject'])
        ->middleware('role:it_manager')
        ->name('tickets.it_manager_reject');

    Route::post('/it-manager/tickets/{ticket}/confirm', [TicketController::class, 'itManagerConfirm'])
        ->middleware('role:it_manager')
        ->name('tickets.it_manager_confirm');

    Route::post('/it-manager/tickets/{ticket}/reopen', [TicketController::class, 'itManagerReopen'])
        ->middleware('role:it_manager')
        ->name('tickets.it_manager_reopen');

    Route::post('/it-member/tickets/{ticket}/start', [TicketController::class, 'startWork'])
        ->middleware('role:it_member')
        ->name('tickets.it_member_start');

    Route::post('/it-member/tickets/{ticket}/complete', [TicketController::class, 'markCompleted'])
        ->middleware('role:it_member')
        ->name('tickets.it_member_complete');

    Route::post('/manager/tickets/{ticket}/confirm', [TicketController::class, 'deptManagerConfirm'])
        ->middleware('role:dept_manager,section_manager')
        ->name('tickets.dept_confirm');

    Route::post('/manager/tickets/{ticket}/reopen', [TicketController::class, 'deptManagerReopen'])
        ->middleware('role:dept_manager,section_manager')
        ->name('tickets.dept_reopen');

    Route::post('/tickets/{ticket}/confirm', [TicketController::class, 'requesterConfirm'])
        ->middleware('role:!it_manager,!it_member')
        ->name('tickets.requester_confirm');

    Route::post('/tickets/{ticket}/reopen', [TicketController::class, 'requesterReopen'])
        ->middleware('role:!it_manager,!it_member')
        ->name('tickets.reopen');

    Route::get('/dashboard/manager', function () {
        $baseQuery = \App\Models\Ticket::query()
            ->with(['requester:id,name', 'itMember:id,name', 'statusHistories.user:id,name', 'attachments'])
            ->where('approval_user_id', auth()->id())
            ->orderByDesc('id');

        $pendingTickets = (clone $baseQuery)
            ->where('status', 'pending')
            ->paginate(10, ['*'], 'pending_page')
            ->appends(['tab' => 'pending']);

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
            ->appends(['tab' => 'approved']);

        $pendingConfirmationTickets = (clone $baseQuery)
            ->whereIn('status', [
                // No statuses require confirmation now
            ])
            ->paginate(10, ['*'], 'pending_confirmation_page')
            ->appends(['tab' => 'pending_confirmation']);

        $completedTickets = (clone $baseQuery)
            ->whereIn('status', [
                'dept_confirmed',
                'requester_confirmed',
                'it_dept_confirmed_completion',
            ])
            ->paginate(10, ['*'], 'completed_page')
            ->appends(['tab' => 'completed']);

        $rejectedTickets = (clone $baseQuery)
            ->whereIn('status', [
                'dept_rejected',
                'it_dept_rejected',
                'it_manager_rejected',
            ])
            ->paginate(10, ['*'], 'rejected_page')
            ->appends(['tab' => 'rejected']);

        return view('dashboard.manager', compact('pendingTickets', 'approvedTickets', 'pendingConfirmationTickets', 'completedTickets', 'rejectedTickets'));
    })->middleware('role:dept_manager,section_manager')->name('dashboard.manager');

    Route::get('/dashboard/it-manager', function () {
        $itMembers = \App\Models\User::query()
            ->with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'it_member'))
            ->orderBy('name')
            ->get();

        $baseQuery = \App\Models\Ticket::query()
            ->with(['requester:id,name', 'approvalUser:id,name', 'itMember:id,name', 'statusHistories.user:id,name', 'attachments'])
            ->orderByDesc('id');

        $approvedTickets = (clone $baseQuery)
            ->whereIn('status', ['it_dept_approved', 'it_dept_reopened_completion'])
            ->paginate(10, ['*'], 'approved_page')
            ->appends(['tab' => 'approved']);

        $assigningTickets = (clone $baseQuery)
            ->whereIn('status', ['it_assigned', 'it_in_progress'])
            ->paginate(10, ['*'], 'assigning_page')
            ->appends(['tab' => 'assigning']);

        $reopenedTickets = (clone $baseQuery)
            ->whereIn('status', ['it_reopened', 'dept_reopened', 'requester_reopened'])
            ->paginate(10, ['*'], 'reopened_page')
            ->appends(['tab' => 'reopened']);

        $pendingConfirmationTickets = (clone $baseQuery)
            ->where('status', 'it_completed')
            ->paginate(10, ['*'], 'pending_confirmation_page')
            ->appends(['tab' => 'pending_confirmation']);

        $confirmedTickets = (clone $baseQuery)
            ->where('status', 'it_mgr_confirmed')
            ->paginate(10, ['*'], 'confirmed_page')
            ->appends(['tab' => 'confirmed']);

        $completedTickets = (clone $baseQuery)
            ->whereIn('status', ['dept_confirmed', 'requester_confirmed', 'it_dept_confirmed_completion'])
            ->paginate(10, ['*'], 'completed_page')
            ->appends(['tab' => 'completed']);

        return view('dashboard.it_manager', compact('approvedTickets', 'assigningTickets', 'reopenedTickets', 'pendingConfirmationTickets', 'confirmedTickets', 'completedTickets', 'itMembers'));
    })->middleware('role:it_manager')->name('dashboard.it_manager');

    Route::get('/dashboard/it-member', function () {
        $baseQuery = \App\Models\Ticket::query()
            ->with(['requester:id,name', 'approvalUser:id,name', 'statusHistories.user:id,name', 'attachments'])
            ->where('it_member_id', auth()->id())
            ->orderByDesc('id');

        $assigningTickets = (clone $baseQuery)
            ->whereIn('status', ['it_assigned', 'it_in_progress'])
            ->paginate(10, ['*'], 'assigning_page')
            ->appends(['tab' => 'assigning']);

        $reopenedTickets = \App\Models\Ticket::query()
            ->with(['requester:id,name', 'approvalUser:id,name', 'statusHistories.user:id,name', 'attachments'])
            ->whereIn('status', ['it_reopened', 'dept_reopened', 'requester_reopened'])
            ->where(function ($query) {
                $query->where('it_member_id', auth()->id())
                    ->orWhereHas('statusHistories', function ($history) {
                        $history->where('user_id', auth()->id());
                    });
            })
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'reopened_page')
            ->appends(['tab' => 'reopened']);

        $completedTickets = (clone $baseQuery)
            ->whereIn('status', ['it_completed', 'it_dept_confirmed_completion'])
            ->paginate(10, ['*'], 'completed_page')
            ->appends(['tab' => 'completed']);

        $confirmedTickets = (clone $baseQuery)
            ->whereIn('status', ['it_mgr_confirmed', 'dept_confirmed', 'requester_confirmed'])
            ->paginate(10, ['*'], 'confirmed_page')
            ->appends(['tab' => 'confirmed']);

        $rejectedTickets = (clone $baseQuery)
            ->whereIn('status', ['dept_rejected', 'it_dept_rejected', 'it_manager_rejected'])
            ->paginate(10, ['*'], 'rejected_page')
            ->appends(['tab' => 'rejected']);

        return view('dashboard.it_member', compact('assigningTickets', 'reopenedTickets', 'completedTickets', 'confirmedTickets', 'rejectedTickets'));
    })->middleware('role:it_member')->name('dashboard.it_member');
});

// Super Admin Routes
Route::middleware(['auth', 'super-admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/users', [\App\Http\Controllers\SuperAdminController::class, 'index'])->name('users.index');
    Route::get('/users/create', [\App\Http\Controllers\SuperAdminController::class, 'create'])->name('users.create');
    Route::post('/users', [\App\Http\Controllers\SuperAdminController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\SuperAdminController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\SuperAdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [\App\Http\Controllers\SuperAdminController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/change-password', [\App\Http\Controllers\SuperAdminController::class, 'changePassword'])->name('users.change-password');
});

// Email Monitor Routes (Super Admin only)
Route::middleware(['auth', 'super-admin'])->prefix('email-logs')->name('email-logs.')->group(function () {
    Route::get('/', [\App\Http\Controllers\EmailLogController::class, 'index'])->name('index');
    Route::get('/pending', [\App\Http\Controllers\EmailLogController::class, 'pending'])->name('pending');
    Route::get('/failed', [\App\Http\Controllers\EmailLogController::class, 'failed'])->name('failed');
    Route::post('/failed/{uuid}/retry', [\App\Http\Controllers\EmailLogController::class, 'retry'])->name('retry');
    Route::post('/failed/retry-all', [\App\Http\Controllers\EmailLogController::class, 'retryAll'])->name('retry-all');
    Route::delete('/failed/{uuid}', [\App\Http\Controllers\EmailLogController::class, 'deleteFailed'])->name('delete-failed');
    Route::delete('/failed', [\App\Http\Controllers\EmailLogController::class, 'flushFailed'])->name('flush-failed');
    Route::get('/{emailLog}', [\App\Http\Controllers\EmailLogController::class, 'show'])->name('show');
});

// Force Password Change Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/force-change-password', function () {
        if (!auth()->user()->force_password_change) {
            return redirect()->route('dashboard');
        }
        return view('auth.force-change-password');
    })->name('password.force-change');

    Route::post('/force-change-password', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = auth()->user();
        $user->update([
            'password' => $request->password,
            'force_password_change' => false,
        ]);

        return redirect()->route('dashboard')->with('status', 'Password changed successfully! Welcome to the system.');
    })->name('password.force-update');
});

require __DIR__ . '/auth.php';