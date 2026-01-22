<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-900 leading-tight">
                {{ __('Create Ticket') }}
            </h2>

            <a href="{{ route('tickets.index') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-blue-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150">
                View My Tickets
            </a>
        </div>
    </x-slot>

    <div class="py-12 pb-24">
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
            <div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
                <div class="p-8 text-gray-900">

                    <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Title --}}
                            <div>
                                <label class="block font-semibold text-slate-900 mb-2" for="title">
                                    Ticket Title
                                </label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Category --}}
                            <div>
                                <label class="block font-semibold text-slate-900 mb-2" for="category">
                                    Category
                                </label>
                                <select name="category" id="category" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Select Category --</option>
                                    @foreach(['Hardware', 'Software', 'Access', 'Network', 'Email', 'Other'] as $cat)
                                        <option value="{{ $cat }}" @selected(old('category') === $cat)>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Priority --}}
                            <div>
                                <label class="block font-semibold text-slate-900 mb-2" for="priority">
                                    Priority
                                </label>
                                <select name="priority" id="priority" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Select Priority --</option>
                                    @foreach(['Low', 'Normal', 'High'] as $p)
                                        <option value="{{ $p }}" @selected(old('priority') === $p)>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Due Date --}}
                            <div>
                                <label class="block font-semibold text-slate-900 mb-2" for="needed_by">
                                    Job Completion Deadline
                                </label>
                                <input type="datetime-local" name="needed_by" id="needed_by" value="{{ old('needed_by') }}" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-slate-600">When the job should be completed. Approval deadline is end of this day.</p>
                            </div>

                            {{-- Section --}}
                            <div>
                                <label class="block font-semibold text-slate-900 mb-2" for="section_id">
                                    Section
                                </label>
                                <select name="section_id" id="section_id" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Select Section --</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" @selected(old('section_id') == $section->id)>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Description --}}
                            <div class="md:col-span-2">
                                <label class="block font-semibold text-slate-900 mb-2" for="description">
                                    Description
                                </label>
                                <textarea name="description" id="description" rows="5" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                            </div>

                            {{-- File Attachments --}}
                            <div class="md:col-span-2">
                                <label class="block font-semibold text-slate-900 mb-2" for="attachments">
                                    Attachments (optional)
                                </label>
                                <input type="file" name="attachments[]" id="attachments" multiple
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.txt"
                                    class="mt-1 block w-full text-sm text-gray-900 border border-slate-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-slate-600">Max 10MB per file. Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP, TXT</p>
                                <div id="file-list" class="mt-2 text-sm text-slate-700"></div>
                            </div>

                            {{-- Approval Person --}}
                            <div>
                                <label class="block font-semibold text-slate-900 mb-2" for="approval_user_id">
                                    Approval Person
                                </label>
                                <select name="approval_user_id" id="approval_user_id" required
                                    class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">-- Select Approval Person --</option>
                                    @foreach ($approvalUsers as $user)
                                        <option value="{{ $user->id }}" @selected((string) old('approval_user_id') === (string) $user->id)>
                                            {{ $user->name }} ({{ $user->role->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="flex space-x-4 pt-6">
                            <x-primary-button>
                                Submit Ticket
                            </x-primary-button>

                            <a href="{{ url('/dashboard/employee') }}"
                                class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                        </div>

                    </form>

                    {{-- File upload preview script --}}
                    <script>
                        document.getElementById('attachments').addEventListener('change', function(e) {
                            const fileList = document.getElementById('file-list');
                            fileList.innerHTML = '';
                            
                            if (this.files.length > 0) {
                                const ul = document.createElement('ul');
                                ul.className = 'list-disc list-inside';
                                
                                Array.from(this.files).forEach(file => {
                                    const li = document.createElement('li');
                                    const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
                                    li.textContent = `${file.name} (${sizeInMB} MB)`;
                                    ul.appendChild(li);
                                });
                                
                                fileList.appendChild(ul);
                            }
                        });
                    </script>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>