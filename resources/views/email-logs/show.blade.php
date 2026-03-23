<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('email-logs.index') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="font-bold text-xl text-slate-900 leading-tight">
                {{ __('Email Log #' . $emailLog->id) }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
                <div class="p-8">

                    {{-- Status Banner --}}
                    @if ($emailLog->status === 'sent')
                        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 mb-6 flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium text-emerald-800">This email was sent successfully.</span>
                        </div>
                    @else
                        <div class="rounded-lg border border-red-200 bg-red-50 p-4 mb-6">
                            <div class="flex items-center gap-3 mb-2">
                                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-medium text-red-800">This email failed to send.</span>
                            </div>
                            @if ($emailLog->error_message)
                                <p class="text-sm text-red-700 ml-8">{{ $emailLog->error_message }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Details Grid --}}
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <dt class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Date & Time</dt>
                                <dd class="text-sm text-slate-900 font-medium">{{ $emailLog->created_at->format('M d, Y \a\t h:i:s A') }}</dd>
                            </div>

                            <div>
                                <dt class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Time Ago</dt>
                                <dd class="text-sm text-slate-900 font-medium">{{ $emailLog->created_at->diffForHumans() }}</dd>
                            </div>
                        </div>

                        <hr class="border-slate-200">

                        <div>
                            <dt class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">From</dt>
                            <dd class="text-sm text-slate-900 font-medium">{{ $emailLog->from }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">To</dt>
                            <dd class="text-sm text-slate-900 font-medium">{{ $emailLog->to }}</dd>
                        </div>

                        @if ($emailLog->cc)
                            <div>
                                <dt class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">CC</dt>
                                <dd class="text-sm text-slate-900 font-medium">{{ $emailLog->cc }}</dd>
                            </div>
                        @endif

                        @if ($emailLog->bcc)
                            <div>
                                <dt class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">BCC</dt>
                                <dd class="text-sm text-slate-900 font-medium">{{ $emailLog->bcc }}</dd>
                            </div>
                        @endif

                        <hr class="border-slate-200">

                        <div>
                            <dt class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Subject</dt>
                            <dd class="text-sm text-slate-900 font-medium">{{ $emailLog->subject ?? 'N/A' }}</dd>
                        </div>

                        <div>
                            <dt class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Email Type (Mailable Class)</dt>
                            <dd class="flex items-center gap-2 mt-1">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold bg-purple-100 text-purple-800">
                                    {{ $emailLog->short_mailable_name }}
                                </span>
                                <span class="text-xs text-slate-400 font-mono">{{ $emailLog->mailable_class }}</span>
                            </dd>
                        </div>

                        @if ($emailLog->ticket_id)
                            <hr class="border-slate-200">
                            <div>
                                <dt class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Related Ticket</dt>
                                <dd>
                                    <a href="{{ route('tickets.show', $emailLog->ticket_id) }}"
                                       class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                        Ticket #{{ $emailLog->ticket_id }}
                                        @if ($emailLog->ticket)
                                            — {{ $emailLog->ticket->title ?? '' }}
                                        @endif
                                    </a>
                                </dd>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Back link --}}
            <div class="mt-6">
                <a href="{{ route('email-logs.index') }}"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Email Monitor
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
