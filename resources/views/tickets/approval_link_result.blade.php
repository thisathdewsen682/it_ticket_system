<x-guest-layout>
    <div class="space-y-3">
        <div class="text-lg font-semibold text-slate-900">
            Ticket #{{ $ticket->id }}
        </div>

        <div class="text-sm text-slate-700">
            {{ $ticket->title }}
        </div>

        <div class="rounded-md border border-slate-200 bg-gray-50 p-3 text-sm text-slate-900">
            {{ $message ?? 'Done.' }}
        </div>

        <div class="text-xs text-slate-600">
            Current status: <span class="font-medium text-gray-900">{{ $ticket->status }}</span>
        </div>

        @if($ticket->attachments && $ticket->attachments->count() > 0)
            <div class="pt-4 border-t border-slate-200">
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

        <div class="pt-2 flex gap-4">
            <a href="{{ route('tickets.show', $ticket) }}" class="text-sm font-medium text-blue-700 hover:underline">
                View Full Ticket Details
            </a>
            <a href="{{ url('/') }}" class="text-sm font-medium text-slate-600 hover:underline">
                Go to Home
            </a>
        </div>
    </div>
</x-guest-layout>
