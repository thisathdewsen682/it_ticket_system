<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IT JOB REQUEST SYSTEM</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-emerald-50 to-teal-50 text-gray-900">
    <div class="min-h-screen flex flex-col">
        <header class="relative z-20 border-b border-emerald-200 bg-white/80 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-screen-2xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}" class="font-semibold tracking-tight text-emerald-900 hover:text-emerald-600">
                    IT JOB REQUEST SYSTEM
                </a>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center rounded-lg border border-emerald-600 bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 shadow-sm">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                                Log in
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="inline-flex items-center rounded-lg border border-emerald-600 bg-emerald-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 shadow-sm">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        <main class="relative isolate flex-1">
            <div class="absolute inset-0 -z-10 overflow-hidden pointer-events-none" style="pointer-events: none"
                aria-hidden="true">
                <div class="absolute -top-24 -left-24 h-96 w-96 rounded-full bg-emerald-200 blur-3xl opacity-40"></div>
                <div
                    class="absolute -bottom-40 -right-24 h-[28rem] w-[28rem] rounded-full bg-teal-200 blur-3xl opacity-40">
                </div>
            </div>

            <div class="mx-auto max-w-screen-2xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-lg">
                    <div class="p-8 sm:p-12">
                        <div class="grid grid-cols-1 gap-10 lg:grid-cols-12 lg:items-center">
                            <div class="lg:col-span-7">
                                <div
                                    class="inline-flex items-center gap-2 rounded-full border border-emerald-300 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-800">
                                    IT Department Portal
                                </div>

                                <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                                    IT JOB REQUEST SYSTEM
                                </h1>

                                <p class="mt-4 text-base text-gray-700 sm:text-lg">
                                    Submit IT job requests, track approvals, assign tasks, and view the full status
                                    history
                                    from start to finish.
                                </p>

                                <div class="mt-8 flex flex-wrap items-center gap-3">
                                    @auth
                                        <a href="{{ route('dashboard') }}"
                                            class="inline-flex items-center rounded-lg border border-emerald-600 bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 shadow-md hover:shadow-lg transition-all">
                                            Go to Dashboard
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="inline-flex items-center rounded-lg border border-emerald-600 bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 shadow-md hover:shadow-lg transition-all">
                                            Log in
                                        </a>

                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}"
                                                class="inline-flex items-center rounded-lg border-2 border-emerald-600 bg-white px-5 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 shadow-sm hover:shadow-md transition-all">
                                                Create an Account
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            </div>

                            <div class="lg:col-span-5">
                                <div
                                    class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-teal-50 p-6 shadow-sm">
                                    <div class="text-sm font-semibold text-gray-900">How it works</div>
                                    <ol class="mt-3 space-y-2 text-sm text-gray-700">
                                        <li><span class="font-semibold">1.</span> Employee submits a request</li>
                                        <li><span class="font-semibold">2.</span> Manager approves / rejects</li>
                                        <li><span class="font-semibold">3.</span> IT Manager assigns IT Member</li>
                                        <li><span class="font-semibold">4.</span> IT Member updates work status</li>
                                        <li><span class="font-semibold">5.</span> Confirm / reopen if needed</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div
                                class="rounded-xl border border-emerald-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                                <div class="text-sm font-semibold text-emerald-900">Fast Request</div>
                                <div class="mt-1 text-sm text-gray-600">Create a ticket with priority and category.
                                </div>
                            </div>
                            <div
                                class="rounded-xl border border-emerald-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                                <div class="text-sm font-semibold text-emerald-900">Clear Workflow</div>
                                <div class="mt-1 text-sm text-gray-600">Approval → assignment → updates → confirmation.
                                </div>
                            </div>
                            <div
                                class="rounded-xl border border-emerald-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                                <div class="text-sm font-semibold text-emerald-900">Full History</div>
                                <div class="mt-1 text-sm text-gray-600">Every status change is stored with remarks.
                                </div>
                            </div>
                            <div
                                class="rounded-xl border border-emerald-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                                <div class="text-sm font-semibold text-emerald-900">Role Based</div>
                                <div class="mt-1 text-sm text-gray-600">Dashboards for each role.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="border-t border-emerald-200 bg-white">
            <div class="mx-auto max-w-screen-2xl px-4 py-6 text-center text-sm text-gray-700 sm:px-6 lg:px-8">
                © {{ date('Y') }} KOHOKU LANKA IT DEPARTMENT. ALL RIGHTS RESERVED.
            </div>
        </footer>
    </div>
</body>

</html>