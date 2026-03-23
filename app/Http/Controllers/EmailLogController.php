<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailLog::query()->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('to', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('mailable_class', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('mailable')) {
            $query->where('mailable_class', 'like', '%' . $request->input('mailable') . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $logs = $query->paginate(25)->withQueryString();

        // Stats for the summary cards
        $totalSent = EmailLog::where('status', 'sent')->count();
        $totalFailed = EmailLog::where('status', 'failed')->count();
        $todaySent = EmailLog::where('status', 'sent')->whereDate('created_at', today())->count();
        $pendingCount = DB::table('jobs')->count();
        $failedCount = DB::table('failed_jobs')->count();

        return view('email-logs.index', compact(
            'logs',
            'totalSent',
            'totalFailed',
            'todaySent',
            'pendingCount',
            'failedCount',
        ));
    }

    public function show(EmailLog $emailLog)
    {
        $emailLog->load('ticket');

        return view('email-logs.show', compact('emailLog'));
    }

    public function pending()
    {
        $jobs = DB::table('jobs')
            ->orderByDesc('id')
            ->paginate(25);

        // Decode payload for display
        $jobs->getCollection()->transform(function ($job) {
            $payload = json_decode($job->payload, true);
            $job->display_name = $payload['displayName'] ?? 'Unknown';
            $job->short_name = class_basename($payload['displayName'] ?? 'Unknown');
            $job->created_date = \Carbon\Carbon::createFromTimestamp($job->created_at);
            $job->available_date = \Carbon\Carbon::createFromTimestamp($job->available_at);
            $job->reserved = $job->reserved_at !== null;
            return $job;
        });

        return view('email-logs.pending', compact('jobs'));
    }

    public function failed()
    {
        $failedJobs = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->paginate(25);

        // Decode payload for display
        $failedJobs->getCollection()->transform(function ($job) {
            $payload = json_decode($job->payload, true);
            $job->display_name = $payload['displayName'] ?? 'Unknown';
            $job->short_name = class_basename($payload['displayName'] ?? 'Unknown');
            $job->short_exception = \Illuminate\Support\Str::limit($job->exception, 200);
            return $job;
        });

        return view('email-logs.failed', compact('failedJobs'));
    }

    public function retry(string $uuid)
    {
        $job = DB::table('failed_jobs')->where('uuid', $uuid)->first();

        if (!$job) {
            return back()->with('error', 'Failed job not found.');
        }

        Artisan::call('queue:retry', ['id' => [$uuid]]);

        return back()->with('status', 'Job has been pushed back to the queue for retry.');
    }

    public function retryAll()
    {
        $count = DB::table('failed_jobs')->count();

        if ($count === 0) {
            return back()->with('error', 'No failed jobs to retry.');
        }

        Artisan::call('queue:retry', ['id' => ['all']]);

        return back()->with('status', "All {$count} failed jobs have been pushed back to the queue for retry.");
    }

    public function deleteFailed(string $uuid)
    {
        $job = DB::table('failed_jobs')->where('uuid', $uuid)->first();

        if (!$job) {
            return back()->with('error', 'Failed job not found.');
        }

        Artisan::call('queue:forget', ['id' => $uuid]);

        return back()->with('status', 'Failed job has been deleted.');
    }

    public function flushFailed()
    {
        $count = DB::table('failed_jobs')->count();

        if ($count === 0) {
            return back()->with('error', 'No failed jobs to delete.');
        }

        Artisan::call('queue:flush');

        return back()->with('status', "All {$count} failed jobs have been deleted.");
    }
}
