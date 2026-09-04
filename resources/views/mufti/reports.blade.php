@extends('layouts.mufti')
@section('title', 'Scholar Reports & Analytics')

@section('content')
<!-- Page Header Sub-bar & Filter Controls -->
<section class="border-b border-outline-variant/60 pb-4 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
    <div>
        <h1 class="font-headline-lg text-[26px] sm:text-[28px] text-primary tracking-tight font-medium">Scholarly Reports & Analytics</h1>
        <p class="font-body-sm text-[13px] text-on-surface-variant mt-0.5">Authoritative performance metrics, monthly publication velocity, and public readership analytics.</p>
    </div>

    <!-- Actions & Filter Bar -->
    <div class="flex flex-wrap items-center gap-2.5">
        <!-- Date Horizon Dropdown -->
        <form method="GET" action="{{ route('mufti.reports') }}" class="flex flex-wrap items-center gap-2 m-0" id="filterForm">
            <select name="date_range" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-[13px] font-medium text-on-surface focus:ring-1 focus:ring-primary">
                <option value="all" {{ $dateRange == 'all' ? 'selected' : '' }}>All Time</option>
                <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>Today</option>
                <option value="this_week" {{ $dateRange == 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_30_days" {{ $dateRange == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                <option value="this_year" {{ $dateRange == 'this_year' ? 'selected' : '' }}>This Year</option>
            </select>
        </form>

        <!-- CSV Export -->
        <a href="{{ route('mufti.reports.export', request()->all()) }}" class="px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high border border-outline-variant text-[13px] font-medium text-on-surface inline-flex items-center gap-1.5 transition-colors shadow-sm">
            <span class="material-symbols-outlined text-[16px] text-secondary">download</span>
            <span>Export CSV</span>
        </a>

        <!-- Print -->
        <button onclick="window.print()" class="px-3 py-1.5 rounded-lg bg-surface-container hover:bg-surface-container-high border border-outline-variant text-[13px] font-medium text-on-surface inline-flex items-center gap-1.5 transition-colors shadow-sm">
            <span class="material-symbols-outlined text-[16px] text-outline">print</span>
            <span class="hidden sm:inline">Print</span>
        </button>
    </div>
</section>

<!-- KPI Cards Row (4 Minimal Cards) -->
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    <!-- Card 1: ASSIGNED INQUIRIES -->
    <article class="bg-surface-container-lowest border border-outline-variant/70 rounded-lg p-5 transition-all duration-150 shadow-sm relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary"></div>
        <div class="flex items-center justify-between text-outline mb-2">
            <span class="font-label-sm text-[11px] tracking-widest text-on-surface-variant font-semibold uppercase">ASSIGNED INQUIRIES</span>
            <span class="material-symbols-outlined text-[18px] text-primary">assignment</span>
        </div>
        <div class="flex items-baseline gap-2 my-1">
            <span class="font-headline-lg text-[32px] text-primary font-medium">{{ number_format($totalAssigned) }}</span>
        </div>
        <p class="font-body-sm text-[12px] text-on-surface-variant">
            Pending deliberation: <strong>{{ number_format($totalPending) }}</strong>
        </p>
    </article>

    <!-- Card 2: ANSWERED & DRAFTED -->
    <article class="bg-surface-container-lowest border border-outline-variant/70 rounded-lg p-5 transition-all duration-150 shadow-sm relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-secondary"></div>
        <div class="flex items-center justify-between text-outline mb-2">
            <span class="font-label-sm text-[11px] tracking-widest text-on-surface-variant font-semibold uppercase">RESOLVED / ANSWERED</span>
            <span class="material-symbols-outlined text-[18px] text-secondary">history_edu</span>
        </div>
        <div class="flex items-baseline gap-2 my-1">
            <span class="font-headline-lg text-[32px] text-primary font-medium">{{ number_format($totalAnswered) }}</span>
        </div>
        <p class="font-body-sm text-[12px] text-on-surface-variant">
            Completion rate: <strong>{{ $completionRate }}%</strong>
        </p>
    </article>

    <!-- Card 3: PUBLISHED FATWAS -->
    <article class="bg-surface-container-lowest border border-outline-variant/70 rounded-lg p-5 transition-all duration-150 shadow-sm relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-emerald-700"></div>
        <div class="flex items-center justify-between text-outline mb-2">
            <span class="font-label-sm text-[11px] tracking-widest text-on-surface-variant font-semibold uppercase">PUBLISHED ARCHIVE</span>
            <span class="material-symbols-outlined text-[18px] text-emerald-700">verified</span>
        </div>
        <div class="flex items-baseline gap-2 my-1">
            <span class="font-headline-lg text-[32px] text-primary font-medium">{{ number_format($totalPublished) }}</span>
        </div>
        <p class="font-body-sm text-[12px] text-on-surface-variant">
            Live on public fatwa archive
        </p>
    </article>

    <!-- Card 4: PUBLIC READS & LIKES -->
    <article class="bg-surface-container-lowest border border-outline-variant/70 rounded-lg p-5 transition-all duration-150 shadow-sm relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-amber-600"></div>
        <div class="flex items-center justify-between text-outline mb-2">
            <span class="font-label-sm text-[11px] tracking-widest text-on-surface-variant font-semibold uppercase">READERSHIP & REACH</span>
            <span class="material-symbols-outlined text-[18px] text-amber-700">visibility</span>
        </div>
        <div class="flex items-baseline gap-2 my-1">
            <span class="font-headline-lg text-[32px] text-primary font-medium">{{ number_format($totalViews) }}</span>
            <span class="text-[13px] text-on-surface-variant">Reads</span>
        </div>
        <p class="font-body-sm text-[12px] text-on-surface-variant">
            Appreciations: <strong class="text-amber-800">{{ number_format($totalLikes) }} ★</strong>
        </p>
    </article>
</section>

<!-- Middle Section: 12-Month Trajectory & Subject Specialization -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- 12-Month Performance Trend (7 cols) -->
    <div class="lg:col-span-7 bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-6 shadow-sm">
        <div class="flex items-center justify-between pb-3 border-b border-surface-variant/60 mb-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-primary">analytics</span>
                <h3 class="font-headline-sm text-[17px] font-semibold text-primary m-0">Monthly Contribution Trajectory (12 Months)</h3>
            </div>
            <span class="text-[11px] font-semibold text-outline uppercase tracking-wider">Publication Velocity</span>
        </div>

        @if($monthlyTrends->isEmpty())
            <div class="p-8 text-center text-outline text-[13px]">
                No monthly activity logged in the past 12 months.
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach($monthlyTrends as $m)
                @php
                    $maxAssigned = max(1, $monthlyTrends->max('assigned'));
                    $percentAssigned = round(($m->assigned / $maxAssigned) * 100);
                    $percentPublished = round(($m->published / $maxAssigned) * 100);
                @endphp
                <div>
                    <div class="flex justify-between text-[12px] mb-1 font-medium">
                        <span class="text-primary font-semibold">{{ $m->month_year }}</span>
                        <span class="text-on-surface-variant">
                            Assigned: <strong>{{ $m->assigned }}</strong> · Published: <strong class="text-emerald-800">{{ $m->published }}</strong>
                        </span>
                    </div>
                    <div class="w-full bg-surface-container rounded-full h-2.5 overflow-hidden flex">
                        <div class="bg-primary/40 h-full" style="width: {{ $percentAssigned }}%" title="Assigned: {{ $m->assigned }}"></div>
                        <div class="bg-emerald-600 h-full" style="width: {{ $percentPublished }}%" title="Published: {{ $m->published }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mt-4 pt-3 border-t border-surface-variant/60 text-[11px] text-outline">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded bg-primary/40"></span>
                    <span>Inquiries Assigned</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded bg-emerald-600"></span>
                    <span>Published Fatwas</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Category / Topic Specialization (5 cols) -->
    <div class="lg:col-span-5 bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-6 shadow-sm">
        <div class="flex items-center justify-between pb-3 border-b border-surface-variant/60 mb-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-secondary">category</span>
                <h3 class="font-headline-sm text-[17px] font-semibold text-primary m-0">Specialization & Topics</h3>
            </div>
            <span class="text-[11px] font-semibold text-outline uppercase tracking-wider">Subject Domains</span>
        </div>

        @if($categoryBreakdown->isEmpty())
            <div class="p-8 text-center text-outline text-[13px]">
                No category responses recorded yet.
            </div>
        @else
            <div class="flex flex-col gap-3">
                @foreach($categoryBreakdown as $cat)
                @php
                    $catPercent = $totalAnswered > 0 ? round(($cat->questions_count / $totalAnswered) * 100) : 0;
                @endphp
                <div class="p-3 bg-surface-container-low rounded-lg border border-surface-variant">
                    <div class="flex items-center justify-between text-[13px] mb-1.5">
                        <span class="font-semibold text-primary">{{ $cat->name }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-white text-secondary border border-outline-variant">
                            {{ $cat->questions_count }} {{ Str::plural('Fatwa', $cat->questions_count) }} ({{ $catPercent }}%)
                        </span>
                    </div>
                    <div class="w-full bg-surface-variant rounded-full h-1.5 overflow-hidden">
                        <div class="bg-secondary h-full" style="width: {{ $catPercent }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Top Most-Read Fatwas by Scholar -->
@if($topFatwas->isNotEmpty())
<section class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-surface-variant/80 flex flex-wrap items-baseline justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-amber-700">stars</span>
            <h3 class="font-headline-sm text-[17px] font-semibold text-primary m-0">Top Published Fatwas by You (Most Read)</h3>
        </div>
        <span class="text-[12px] text-outline">Ranked by global readership views</span>
    </div>

    <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse text-[14px]">
            <thead>
                <tr class="border-b border-surface-variant bg-surface-container-low text-on-surface-variant font-label-md text-[12px] uppercase tracking-wider">
                    <th class="py-3 px-6 font-semibold text-left" scope="col">QU-ID</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Fatwa Title</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Category</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Madhab</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Reads</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Likes</th>
                    <th class="py-3 px-6 font-semibold text-right" scope="col">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant/50">
                @foreach($topFatwas as $tf)
                <tr class="hover:bg-surface-container-low/60 transition-colors">
                    <td class="py-3 px-6 font-mono text-[13px] font-bold text-primary">
                        {{ $tf->question_id }}
                    </td>
                    <td class="py-3 px-6 max-w-md">
                        <a href="{{ route('fatwa.show', $tf) }}" target="_blank" class="font-semibold text-primary hover:underline text-[14px] line-clamp-1">
                            {{ $tf->title ?: strip_tags(substr($tf->body, 0, 70)) }}
                        </a>
                    </td>
                    <td class="py-3 px-6 text-[12px]">
                        <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold bg-surface-container border border-outline-variant text-secondary">
                            {{ $tf->category->name ?? 'General' }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-[12px] text-on-surface-variant">
                        {{ $tf->madhab?->name ?? 'General' }}
                    </td>
                    <td class="py-3 px-6 text-[13px] font-semibold text-primary">
                        {{ number_format($tf->views) }}
                    </td>
                    <td class="py-3 px-6 text-[13px] font-semibold text-amber-800">
                        {{ number_format($tf->likes) }} ★
                    </td>
                    <td class="py-3 px-6 text-right whitespace-nowrap">
                        <a href="{{ route('fatwa.show', $tf) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-surface-container hover:bg-surface-container-high border border-outline-variant text-[12px] font-medium text-on-surface transition-colors">
                            <span>Open Public View</span>
                            <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif

<!-- Detailed Inquiries Dossier -->
<section class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg overflow-hidden shadow-sm">
    <div class="px-6 py-4 border-b border-surface-variant/80 flex flex-wrap items-baseline justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px] text-primary">folder_managed</span>
            <h3 class="font-headline-sm text-[17px] font-semibold text-primary m-0">Inquiries Dossier</h3>
        </div>
        <span class="text-[12px] text-outline">Showing {{ $recentInquiries->firstItem() ?? 0 }} to {{ $recentInquiries->lastItem() ?? 0 }} of {{ $recentInquiries->total() }} records</span>
    </div>

    <!-- Mobile Cards View -->
    <div class="block md:hidden divide-y divide-surface-variant/60">
        @forelse($recentInquiries as $q)
        <div class="p-4 flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[12px] font-bold text-primary bg-surface-container px-2 py-0.5 rounded border border-outline-variant">
                    {{ $q->question_id }}
                </span>
                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-surface-container border border-outline-variant/60 text-secondary">
                    {{ $q->category->name ?? 'General' }}
                </span>
            </div>
            <div>
                <h4 class="font-semibold text-on-surface text-[14px] line-clamp-1 mb-0.5">
                    {{ $q->title ?: strip_tags(substr($q->body, 0, 70)) }}
                </h4>
                <div class="text-[11px] text-outline">
                    Submitted: {{ $q->created_at ? $q->created_at->format('d M Y') : '—' }} · Views: <strong>{{ number_format($q->views) }}</strong>
                </div>
            </div>
            <div class="flex items-center justify-between pt-1">
                @if($q->status === 'published')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">Published</span>
                @elseif($q->status === 'answered')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">Answered</span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">Pending</span>
                @endif
                <a href="{{ route('mufti.questions.show', $q) }}" class="text-[12px] font-semibold text-primary hover:underline">Review / Edit →</a>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-outline text-[13px]">No records found for this time horizon.</div>
        @endforelse
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full text-left border-collapse text-[14px]">
            <thead>
                <tr class="border-b border-surface-variant bg-surface-container-low text-on-surface-variant font-label-md text-[12px] uppercase tracking-wider">
                    <th class="py-3 px-6 font-semibold text-left" scope="col">ID</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Inquiry Title</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Category</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Madhab</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Status</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Date</th>
                    <th class="py-3 px-6 font-semibold text-right" scope="col">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant/50">
                @forelse($recentInquiries as $q)
                <tr class="hover:bg-surface-container-low/60 transition-colors">
                    <td class="py-3 px-6 font-mono text-[13px] font-bold text-primary">
                        {{ $q->question_id }}
                    </td>
                    <td class="py-3 px-6 max-w-sm">
                        <div class="font-semibold text-on-surface text-[14px] line-clamp-1">
                            {{ $q->title ?: strip_tags(substr($q->body, 0, 70)) }}
                        </div>
                    </td>
                    <td class="py-3 px-6 text-[12px]">
                        {{ $q->category->name ?? 'General' }}
                    </td>
                    <td class="py-3 px-6 text-[12px] text-on-surface-variant">
                        {{ $q->madhab?->name ?? 'General' }}
                    </td>
                    <td class="py-3 px-6 text-[12px]">
                        @if($q->status === 'published')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">Published</span>
                        @elseif($q->status === 'answered')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">Answered</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">Assigned</span>
                        @endif
                    </td>
                    <td class="py-3 px-6 text-[12px] text-on-surface-variant whitespace-nowrap">
                        {{ $q->created_at ? $q->created_at->format('d M Y') : '—' }}
                    </td>
                    <td class="py-3 px-6 text-right whitespace-nowrap">
                        <a href="{{ route('mufti.questions.show', $q) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-surface-container hover:bg-surface-container-high border border-outline-variant text-[12px] font-medium text-on-surface transition-colors">
                            <span>Review</span>
                            <span class="material-symbols-outlined text-[14px]">edit_note</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 px-6 text-center text-outline text-[13px]">
                        No records match the selected time filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($recentInquiries->hasPages())
    <div class="px-6 py-3 bg-surface-container-low/50 border-t border-surface-variant flex items-center justify-center text-[12px]">
        {{ $recentInquiries->links() }}
    </div>
    @endif
</section>
@endsection
