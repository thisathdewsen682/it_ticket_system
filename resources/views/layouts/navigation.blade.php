{{-- filepath: d:\Thisath\Company Projects\it_ticket_system\resources\views\layouts\navigation.blade.php --}}
<nav x-data="{ open: false }" class="bg-white/90 backdrop-blur border-b border-emerald-200 shadow-sm relative z-40">
    @php
        $roleName = auth()->user()?->role?->name;

        $dashboardUrl = match ($roleName) {
            'employee' => route('dashboard.employee'),
            'dept_manager', 'section_manager' => route('dashboard.manager'),
            'it_manager' => route('dashboard.it_manager'),
            'it_member' => route('dashboard.it_member'),
            default => route('dashboard'),
        };
    @endphp

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ $dashboardUrl }}">
                        <div class="font-bold text-emerald-900 leading-tight hover:text-emerald-600 transition-colors">
                            IT JOB REQUEST SYSTEM
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="$dashboardUrl" :active="request()->routeIs('dashboard.*') || request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @if ($roleName && !in_array($roleName, ['it_manager', 'it_member'], true))
                        <x-nav-link :href="route('dashboard.employee')" :active="request()->routeIs('dashboard.employee')">
                            {{ __('Create Ticket') }}
                        </x-nav-link>

                        <x-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.index')">
                            {{ __('My Tickets') }}
                        </x-nav-link>
                    @endif

                    @if ($roleName && !in_array($roleName, ['employee', 'it_manager', 'it_member'], true))
                        <x-nav-link :href="route('tickets.approvals')" :active="request()->routeIs('tickets.approvals')">
                            {{ __('Approvals') }}
                        </x-nav-link>
                    @endif

                    @if (auth()->user()?->is_super_admin)
                        <x-nav-link :href="route('super-admin.users.index')" :active="request()->routeIs('super-admin.*')">
                            {{ __('Super Admin Panel') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-emerald-50 hover:border-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="$dashboardUrl" :active="request()->routeIs('dashboard.*') || request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            @if ($roleName && !in_array($roleName, ['it_manager', 'it_member'], true))
                <x-responsive-nav-link :href="route('dashboard.employee')"
                    :active="request()->routeIs('dashboard.employee')">
                    {{ __('Create Ticket') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('tickets.index')" :active="request()->routeIs('tickets.index')">
                    {{ __('My Tickets') }}
                </x-responsive-nav-link>
            @endif

            @if ($roleName && !in_array($roleName, ['employee', 'it_manager', 'it_member'], true))
                <x-responsive-nav-link :href="route('tickets.approvals')" :active="request()->routeIs('tickets.approvals')">
                    {{ __('Approvals') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>