<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Job Status Tracker | IT Request System</title>

	<link rel="preconnect" href="https://fonts.bunny.net">
	<link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">
	<div class="min-h-screen flex flex-col">
		<!-- Professional Navigation Bar -->
		<nav class="sticky top-0 z-50 border-b border-slate-200 bg-white shadow-sm">
			<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
				<div class="flex h-16 items-center justify-between">
					<a href="{{ url('/') }}" class="text-xl font-bold text-slate-900">
						<span class="text-blue-600">IT</span> Request System
					</a>

					<nav class="flex items-center gap-4">
						@auth
							<a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
								Dashboard
							</a>
						@else
							@if (Route::has('login'))
								<a href="{{ route('login') }}" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
									Login
								</a>
							@endif
						@endauth
					</nav>
				</div>
			</div>
		</nav>

		<!-- Main Content -->
		<main class="flex-1">
			<!-- Hero Section -->
			<div class="bg-gradient-to-r from-slate-900 to-slate-800 px-4 py-16 sm:px-6 lg:px-8">
				<div class="mx-auto max-w-7xl">
					<div class="space-y-4">
						<div class="inline-flex items-center gap-2 rounded-full bg-blue-500/20 px-4 py-2 text-sm font-semibold text-blue-600">
							<span class="inline-block h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
							Live Status Feed
						</div>
						<h1 class="text-4xl font-bold text-white">Job Status Tracker</h1>
						<p class="max-w-2xl text-lg text-slate-300">Real-time visibility into IT job requests across all departments. Filter by section, status, or search by ticket number.</p>
					</div>
				</div>
			</div>

			<!-- Filters Section -->
			<div class="border-b border-slate-200 bg-white px-4 py-8 sm:px-6 lg:px-8">
				<div class="mx-auto max-w-7xl">
					<form method="GET" action="{{ route('public.section_status') }}" class="space-y-6">
						<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
							<!-- Section Filter -->
							<div class="space-y-3">
								<label for="section_id" class="block text-sm font-semibold text-slate-900">Department</label>
								<select id="section_id" name="section_id" class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
									<option value="">All Departments</option>
									@foreach ($sections as $section)
										<option value="{{ $section->id }}" @selected($selectedSection == $section->id)>{{ $section->name }}</option>
									@endforeach
								</select>
							</div>

							<!-- Status Filter -->
							<div class="space-y-3">
								<label for="status" class="block text-sm font-semibold text-slate-900">Status</label>
								<select id="status" name="status" class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm transition-colors focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
									<option value="">All Statuses</option>
									@foreach ($statuses as $value => $label)
										<option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>
									@endforeach
								</select>
							</div>

							<!-- Search Filter -->
							<div class="space-y-3">
								<label for="search" class="block text-sm font-semibold text-slate-900">Search Tickets</label>
								<input id="search" name="search" value="{{ $search }}" type="text" placeholder="Ticket ID or title..." class="block w-full rounded-lg border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm transition-colors placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20" />
							</div>

							<!-- Apply Button -->
							<div class="flex flex-col justify-end">
								<button type="submit" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm hover:shadow-md">
									Apply Filters
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>

			<!-- Results Section -->
			<div class="bg-white px-4 py-12 sm:px-6 lg:px-8">
				<div class="mx-auto max-w-7xl">
					<!-- Results Summary -->
					<div class="mb-8 flex items-center justify-between">
						<div>
							<p class="text-lg font-semibold text-slate-900">
								{{ $tickets->total() }} 
								<span class="text-slate-600">{{ $tickets->total() === 1 ? 'Request' : 'Requests' }}</span>
							</p>
						</div>
						<div class="text-sm text-slate-500">
							Page {{ $tickets->currentPage() }} of {{ $tickets->lastPage() }}
						</div>
					</div>

					<!-- Data Table -->
					@if ($tickets->count() > 0)
						<div class="overflow-hidden rounded-lg border border-slate-200 shadow-sm">
							<table class="w-full divide-y divide-slate-200">
								<!-- Table Header -->
								<thead class="bg-slate-50">
									<tr>
										<th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">ID</th>
										<th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Title</th>
										<th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Department</th>
										<th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Status</th>
										<th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Priority</th>
										<th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Due Date</th>
										<th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Updated</th>
									</tr>
								</thead>

								<!-- Table Body -->
								<tbody class="divide-y divide-slate-200 bg-white">
									@foreach ($tickets as $ticket)
										<tr class="hover:bg-slate-50 transition-colors cursor-pointer">
											<td class="px-6 py-4 whitespace-nowrap">
												<a href="{{ route('public.section_status.show', $ticket) }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">
													#{{ $ticket->id }}
												</a>
											</td>
											<td class="px-6 py-4">
												<a href="{{ route('public.section_status.show', $ticket) }}" class="text-slate-900 hover:text-blue-600 hover:underline font-medium">
													{{ Str::limit($ticket->title, 40) }}
												</a>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												<span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-700">
													{{ $ticket->section->name ?? 'Unassigned' }}
												</span>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												@php
													$statusColors = [
														'pending' => 'bg-yellow-100 text-yellow-800',
														'manager_approved' => 'bg-blue-100 text-blue-800',
														'assigned_to_it_member' => 'bg-purple-100 text-purple-800',
														'in_progress' => 'bg-orange-100 text-orange-800',
														'completed_by_it_member' => 'bg-green-100 text-green-800',
														'approved_by_dept_manager' => 'bg-teal-100 text-teal-800',
														'manager_confirmed' => 'bg-emerald-100 text-emerald-800',
														'reopened_by_it_manager' => 'bg-red-100 text-red-800',
														'reopened_by_requester' => 'bg-red-100 text-red-800',
														'reopened_by_dept_manager' => 'bg-red-100 text-red-800',
													];
													$statusClass = $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-800';
												@endphp
												<span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $statusClass }}">
													{{ $statuses[$ticket->status] ?? ucwords(str_replace('_', ' ', $ticket->status)) }}
												</span>
											</td>
											<td class="px-6 py-4 whitespace-nowrap">
												@php
													$priorityColors = [
														'Low' => 'text-green-700',
														'Medium' => 'text-yellow-700',
														'High' => 'text-orange-700',
														'Critical' => 'text-red-700',
													];
													$priorityClass = $priorityColors[$ticket->priority] ?? 'text-slate-700';
												@endphp
												<span class="font-medium {{ $priorityClass }}">
													{{ $ticket->priority ?? 'N/A' }}
												</span>
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
												{{ $ticket->needed_by ? $ticket->needed_by->format('M d, Y') : '—' }}
											</td>
											<td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
												{{ $ticket->updated_at?->format('M d, Y H:i') ?? '—' }}
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>

						<!-- Pagination -->
						<div class="mt-8 flex items-center justify-between">
							<div class="text-sm text-slate-600">
								Showing <span class="font-semibold">{{ ($tickets->currentPage() - 1) * $tickets->perPage() + 1 }}</span> to 
								<span class="font-semibold">{{ min($tickets->currentPage() * $tickets->perPage(), $tickets->total()) }}</span> of 
								<span class="font-semibold">{{ $tickets->total() }}</span> requests
							</div>
							<div>{{ $tickets->links() }}</div>
						</div>
					@else
						<!-- Empty State -->
						<div class="rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 px-12 py-12 text-center">
							<svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
							</svg>
							<h3 class="mt-4 text-lg font-semibold text-slate-900">No requests found</h3>
							<p class="mt-2 text-slate-600">Try adjusting your filters or search terms.</p>
						</div>
					@endif
				</div>
			</div>
		</main>

		<!-- Footer -->
		<footer class="border-t border-slate-200 bg-white px-4 py-8 sm:px-6 lg:px-8">
			<div class="mx-auto max-w-7xl text-center text-sm text-slate-600">
				<p>© {{ date('Y') }} IT Request System. This is a public status feed. <a href="{{ url('/') }}" class="font-semibold text-blue-600 hover:text-blue-700">Go to home</a></p>
			</div>
		</footer>
			</div>
		</main>

		<footer class="border-t border-emerald-200 bg-white/80 backdrop-blur">
			<div class="mx-auto max-w-screen-2xl px-4 py-6 text-center text-sm text-gray-700 sm:px-6 lg:px-8">
				{{ config('app.name', 'IT JOB REQUEST SYSTEM') }} &middot; Section status viewer
			</div>
		</footer>
	</div>
</body>

</html>
