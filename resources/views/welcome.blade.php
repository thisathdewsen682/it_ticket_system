<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IT JOB SYSTEM - Home</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <header class="sticky top-0 z-50 border-b border-slate-200 bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-slate-900">
                        <span class="text-blue-600">IT</span> Job System
                    </a>

                    @if (Route::has('login'))
                        <nav class="flex items-center gap-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                    Login
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1">
            <div class="bg-gradient-to-r from-slate-900 to-slate-800 px-4 py-24 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl text-center">
                    <div class="inline-flex items-center gap-2 rounded-full bg-blue-500/20 px-4 py-2 text-sm font-semibold text-blue-400 mb-6">
                        <span class="inline-block h-2 w-2 rounded-full bg-blue-500"></span>
                        
                    </div>
                    
                    <h1 class="text-5xl font-bold text-white sm:text-6xl">
                        IT Job Management
                    </h1>
                    
                    <p class="mt-6 text-xl text-slate-300 max-w-3xl mx-auto">
                        Streamline IT job requests, approvals, assignments, and track progress from submission to completion. Unified platform for IT teams and departments.
                    </p>

                    <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-8 py-3 text-lg font-semibold text-white hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl">
                                Go to Dashboard
                            </a>

                            <a href="{{ route('public.section_status') }}" class="inline-flex items-center rounded-lg border-2 border-slate-400 bg-white/10 px-8 py-3 text-lg font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur">
                                View Job Status
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-8 py-3 text-lg font-semibold text-white hover:bg-blue-700 transition-colors shadow-lg hover:shadow-xl">
                                Login
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg border-2 border-slate-400 bg-white/10 px-8 py-3 text-lg font-semibold text-white hover:bg-white/20 transition-colors backdrop-blur">
                                    Create Account
                                </a>
                            @endif

                            <a href="{{ route('public.section_status') }}" class="inline-flex items-center rounded-lg border border-slate-600 bg-slate-700/50 px-8 py-3 text-lg font-semibold text-slate-100 hover:bg-slate-600/50 transition-colors">
                                Public Status Feed
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="px-4 py-16 sm:px-6 lg:px-8 bg-white">
                <div class="mx-auto max-w-7xl">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl font-bold text-slate-900 sm:text-4xl">Key Features</h2>
                        <p class="mt-4 text-lg text-slate-600">Powerful tools for managing IT requests efficiently</p>
                    </div>

                    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                        <!-- Feature 1 -->
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-8 hover:shadow-lg transition-shadow">
                            <div class="inline-flex items-center justify-center rounded-lg bg-blue-100 p-3 mb-4">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">Easy Submission</h3>
                            <p class="mt-2 text-slate-600">Submit IT requests with detailed information and attachments in minutes.</p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-8 hover:shadow-lg transition-shadow">
                            <div class="inline-flex items-center justify-center rounded-lg bg-blue-100 p-3 mb-4">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">Track Progress</h3>
                            <p class="mt-2 text-slate-600">Real-time status updates and approval workflows for complete visibility.</p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-8 hover:shadow-lg transition-shadow">
                            <div class="inline-flex items-center justify-center rounded-lg bg-blue-100 p-3 mb-4">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">Efficient Management</h3>
                            <p class="mt-2 text-slate-600">Streamlined approval and assignment processes for faster resolution.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="bg-slate-900 px-4 py-16 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-4xl text-center">
                    <h2 class="text-3xl font-bold text-white sm:text-4xl">Ready to get started?</h2>
                    <p class="mt-4 text-lg text-slate-300">Join your organization's IT Job Management system today.</p>
                    
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700 transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700 transition-colors">
                                Sign In
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg border-2 border-blue-400 bg-transparent px-6 py-3 font-semibold text-white hover:bg-white/10 transition-colors">
                                    Create Account
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-slate-900">
            <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div>
                        <h3 class="text-white font-semibold mb-3"><span class="text-blue-400">IT</span> Job System</h3>
                        <p class="text-slate-400 text-sm">Streamline IT operations with an intelligent request management platform.</p>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-3">Quick Links</h4>
                        <ul class="space-y-2 text-sm text-slate-400">
                            <li><a href="{{ route('public.section_status') }}" class="hover:text-white transition-colors">Public Status</a></li>
                            @auth
                            <li><a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Dashboard</a></li>
                            @else
                            <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Login</a></li>
                            @endauth
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-3">Support</h4>
                        <p class="text-slate-400 text-sm">Need help? Contact your IT department for assistance with the system.</p>
                    </div>
                </div>
                <div class="border-t border-slate-800 pt-8 text-center text-sm text-slate-500">
                    <p>© {{ date('Y') }} IT JOB SYSTEM. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
