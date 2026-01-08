<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-emerald-900 leading-tight">
                {{ __('Create Ticket') }}
            </h2>

            <a href="{{ route('tickets.index') }}"
                class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-emerald-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:bg-emerald-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-150">
                View My Tickets
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-4 text-green-800">
                    <div class="flex">
                        <div class="text-sm font-medium">
                            {{ session('status') }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-red-800">
                    <div class="text-sm font-medium mb-2">Please fix the following:</div>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Ticket Form --}}
            <div class="bg-white overflow-hidden border border-emerald-200 shadow-lg sm:rounded-xl">
                <div class="p-8 text-gray-900">

                    <form method="POST" action="{{ route('tickets.store') }}">
                        @csrf

                        {{-- Title --}}
                        <div class="mb-6">
                            <label class="block font-semibold text-emerald-900 mb-2" for="title">
                                Ticket Title
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        {{-- Category --}}
                        <div class="mb-6">
                            <label class="block font-semibold text-emerald-900 mb-2" for="category">
                                Category
                            </label>
                            <select name="category" id="category" required
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">-- Select Category --</option>
                                @foreach(['Hardware', 'Software', 'Access', 'Network', 'Email', 'Other'] as $cat)
                                    <option value="{{ $cat }}" @selected(old('category') === $cat)>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Priority --}}
                        <div class="mb-6">
                            <label class="block font-semibold text-emerald-900 mb-2" for="priority">
                                Priority
                            </label>
                            <select name="priority" id="priority" required
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">-- Select Priority --</option>
                                @foreach(['Low', 'Normal', 'High'] as $p)
                                    <option value="{{ $p }}" @selected(old('priority') === $p)>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Due Date --}}
                        <div class="mb-6">
                            <label class="block font-semibold text-emerald-900 mb-2" for="needed_by">
                                Due Date (approval closes after this day)
                            </label>
                            <input type="datetime-local" name="needed_by" id="needed_by" value="{{ old('needed_by') }}" required
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        {{-- Description --}}
                        <div class="mb-6">
                            <label class="block font-semibold text-emerald-900 mb-2" for="description">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="5" required
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
                        </div>

                        {{-- Location --}}
                        <div class="mb-6">
                            <label class="block font-semibold text-emerald-900 mb-2" for="location">
                                Location (branch / floor / room / desk)
                            </label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        {{-- Approval Person --}}
                        <div class="mb-6">
                            <label class="block font-semibold text-emerald-900 mb-2" for="approval_user_id">
                                Approval Person
                            </label>
                            <select name="approval_user_id" id="approval_user_id" required
                                class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">-- Select Approval Person --</option>
                                @foreach ($approvalUsers as $user)
                                    <option value="{{ $user->id }}" @selected((string) old('approval_user_id') === (string) $user->id)>
                                        {{ $user->name }} ({{ $user->role->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex space-x-4">
                            <x-primary-button>
                                Submit Ticket
                            </x-primary-button>

                            <a href="{{ url('/dashboard/employee') }}"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>