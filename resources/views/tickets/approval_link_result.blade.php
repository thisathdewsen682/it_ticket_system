<x-guest-layout>
    <div class="space-y-3">
        <div class="text-lg font-semibold text-emerald-900">
            Ticket #{{ $ticket->id }}
        </div>

        <div class="text-sm text-gray-700">
            {{ $ticket->title }}
        </div>

        <div class="rounded-md border border-gray-200 bg-gray-50 p-3 text-sm text-gray-800">
            {{ $message ?? 'Done.' }}
        </div>

        <div class="text-xs text-gray-600">
            Current status: <span class="font-medium text-gray-900">{{ $ticket->status }}</span>
        </div>

        <div class="pt-2">
            <a href="{{ url('/') }}" class="text-sm font-medium text-emerald-700 hover:underline">
                Go to Home
            </a>
        </div>
    </div>
</x-guest-layout>
