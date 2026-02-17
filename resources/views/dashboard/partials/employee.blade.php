{{-- Employee Dashboard Partial --}}
<div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
    <div class="p-8 text-gray-900">
        <div class="mb-6">
            <h3 class="text-lg font-bold text-slate-900 mb-2">Submit New Job</h3>
            <p class="text-sm text-slate-600">Submit a new IT Job</p>
        </div>

        @if (session('status'))
            <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-4 text-green-800">
                <div class="text-sm font-medium">
                    {{ session('status') }}
                </div>
            </div>
        @endif

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

        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block font-semibold text-slate-900 mb-2" for="title">Job Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-900 mb-2" for="category">Category</label>
                    <select name="category" id="category" required
                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Select Category --</option>
                        @foreach(['Hardware', 'Software', 'Access', 'Network', 'Email', 'Other'] as $cat)
                            <option value="{{ $cat }}" @selected(old('category') === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-900 mb-2" for="actual_requester_name">
                        Actual Job Requestor Name
                        <span class="text-xs text-slate-500 font-normal">(If requesting on behalf of someone else)</span>
                    </label>
                    <input type="text" name="actual_requester_name" id="actual_requester_name" 
                        value="{{ old('actual_requester_name') }}"
                        placeholder="Leave blank if this is for you"
                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-semibold text-slate-900 mb-2" for="actual_requester_email">
                        Actual Job Requestor Email
                        <span class="text-xs text-slate-500 font-normal">(If requesting on behalf of someone else)</span>
                    </label>
                    <input type="email" name="actual_requester_email" id="actual_requester_email" 
                        value="{{ old('actual_requester_email') }}"
                        placeholder="Leave blank if this is for you"
                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-slate-600">This person will receive notifications about the ticket</p>
                </div>

                <div>
                    <label class="block font-semibold text-slate-900 mb-2" for="priority">Priority</label>
                    <select name="priority" id="priority" required
                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Select Priority --</option>
                        @foreach(['Low', 'Normal', 'High'] as $p)
                            <option value="{{ $p }}" @selected(old('priority') === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-900 mb-2" for="needed_by">Job Completion Deadline</label>
                    <input type="datetime-local" name="needed_by" id="needed_by" value="{{ old('needed_by') }}" required
                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-slate-600">When the job should be completed. Approval deadline is end of this day.</p>
                </div>

                <div>
                    <label class="block font-semibold text-slate-900 mb-2" for="section_id">Section</label>
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

                <div class="md:col-span-2">
                    <label class="block font-semibold text-slate-900 mb-2" for="description">Description</label>
                    <textarea name="description" id="description" rows="5" required
                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block font-semibold text-slate-900 mb-2" for="attachments">Attachments (optional)</label>
                    <input type="file" name="attachments[]" id="attachments" multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip,.txt"
                        class="mt-1 block w-full text-sm text-gray-900 border border-slate-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-slate-600">Max 10MB per file. Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP, TXT</p>
                    <div id="file-list" class="mt-2 text-sm text-slate-700"></div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-900 mb-2" for="approval_user_id">Approval Person</label>
                    <select name="approval_user_id" id="approval_user_id" required
                        class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Select Approval Person --</option>
                        @foreach ($approvalUsers as $approvalUser)
                            <option value="{{ $approvalUser->id }}" @selected((string) old('approval_user_id') === (string) $approvalUser->id)>
                                {{ $approvalUser->name }} ({{ $approvalUser->role->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex space-x-4 pt-6">
                <x-primary-button>Submit Ticket</x-primary-button>
                <a href="{{ route('tickets.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-md font-semibold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    View My Tickets
                </a>
            </div>
        </form>

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
