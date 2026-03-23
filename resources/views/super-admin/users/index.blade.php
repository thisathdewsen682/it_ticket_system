<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight">
            {{ __('Super Admin - User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-2xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 rounded-lg border border-slate-200 bg-slate-50 p-4 text-blue-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden border border-slate-200 shadow-lg sm:rounded-xl">
                <div class="p-8 text-gray-900">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">All System Users</h3>
                            <p class="text-sm text-slate-600 mt-1">Manage user accounts, roles, and permissions</p>
                        </div>
                        <a href="{{ route('super-admin.users.create') }}"
                            class="inline-flex items-center px-4 py-2.5 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Create User
                        </a>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-slate-200 shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                            ID</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                            Name</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                            Employee No</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                            Email</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                            Role</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                            Super Admin</th>
                                        <th
                                            class="px-4 py-3 text-right text-xs font-bold text-slate-900 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    @forelse ($users as $user)
                                        <tr
                                            class="odd:bg-white even:bg-slate-50/30 hover:bg-slate-50 transition-colors">
                                            <td class="whitespace-nowrap px-4 py-3 text-sm font-semibold text-gray-900">
                                                {{ $user->id }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $user->name }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-700">{{ $user->employee_no }}</td>
                                            <td class="px-4 py-3 text-sm text-slate-700">{{ $user->email ?? 'N/A' }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                <span
                                                    class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $user->role?->name === 'it_manager' ? 'bg-purple-100 text-purple-800' : ($user->role?->name === 'it_member' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-slate-900') }}">
                                                    {{ $user->role?->name ?? 'No Role' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                @if ($user->is_super_admin)
                                                    <span
                                                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-slate-100 text-blue-800">Yes</span>
                                                @else
                                                    <span
                                                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-gray-100 text-slate-900">No</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm text-right">
                                                <a href="{{ route('super-admin.users.edit', $user) }}"
                                                    class="text-blue-600 hover:text-slate-900 font-semibold">Edit</a>

                                                @if ($user->id !== auth()->id())
                                                    <form method="POST" action="{{ route('super-admin.users.destroy', $user) }}"
                                                        class="inline-block ml-3"
                                                        onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-600">No users
                                                found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>