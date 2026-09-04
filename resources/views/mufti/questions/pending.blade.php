@extends('layouts.mufti')
@section('title', 'Pending Tasks')

@section('content')
<!-- Page Header Sub-bar -->
<section class="border-b border-outline-variant/60 pb-3 flex flex-wrap items-baseline justify-between gap-2">
    <div>
        <h1 class="font-headline-lg text-[28px] text-primary tracking-tight font-medium">Pending Tasks</h1>
        <p class="font-body-sm text-[13px] text-on-surface-variant mt-0.5">Inquiries awaiting scholarly resolution, verification, or draft submission.</p>
    </div>
    <div class="flex items-center gap-1.5 text-label-sm text-[11px] text-secondary uppercase tracking-wider font-semibold">
        <span class="material-symbols-outlined text-[16px] text-amber-700">hourglass_top</span>
        <span>Urgent Deliberation Queue</span>
    </div>
</section>

<!-- KPI Card -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <article class="bg-surface-container-lowest border border-outline-variant/70 rounded-lg p-5 shadow-sm relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-amber-600"></div>
        <div class="flex items-center justify-between text-outline mb-2">
            <span class="font-label-sm text-[11px] tracking-widest text-on-surface-variant font-semibold uppercase">PENDING TASKS</span>
            <span class="material-symbols-outlined text-[20px] text-amber-700">pending_actions</span>
        </div>
        <div class="flex items-baseline gap-2 my-1">
            <span class="font-headline-lg text-[32px] text-primary font-medium">{{ $questions->total() }}</span>
        </div>
        <p class="font-body-sm text-[12px] text-on-surface-variant">
            Awaiting your authoritative response
        </p>
    </article>
</div>

<!-- Pending Table Container -->
<section class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg overflow-hidden shadow-sm">
    <!-- Mobile Cards View (Visible on small screens) -->
    <div class="block md:hidden divide-y divide-surface-variant/60">
        @forelse($questions as $q)
        <div class="p-4 flex flex-col gap-3">
            <div class="flex items-center justify-between gap-2">
                <span class="font-mono text-[12px] font-bold text-primary bg-surface-container px-2 py-0.5 rounded border border-outline-variant">
                    {{ $q->question_id }}
                </span>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-surface-container border border-outline-variant/60 text-secondary">
                    {{ $q->category->name ?? 'Uncategorized' }}
                </span>
            </div>

            <div>
                <h4 class="font-semibold text-on-surface text-[14px] line-clamp-2 mb-1">
                    {{ Str::limit(strip_tags($q->title ?: $q->body), 80) }}
                </h4>
                <div class="text-[12px] text-on-surface-variant line-clamp-2">
                    {{ strip_tags($q->body) }}
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-surface-variant/40 text-[11px]">
                <div class="text-on-surface-variant">
                    <span class="text-outline">Assigned:</span>
                    <strong class="text-on-surface font-medium">{{ $q->assigned_at ? $q->assigned_at->format('d M Y, h:i A') : '—' }}</strong>
                </div>

                <div>
                    @if($q->assigned_at)
                        @php
                            $diffDays = (int) $q->assigned_at->diffInDays(now());
                        @endphp
                        @if($diffDays >= 7)
                            <span class="inline-flex items-center gap-1 font-bold text-rose-800 bg-rose-50 px-2 py-0.5 rounded border border-rose-200 text-[10px]">
                                <span class="material-symbols-outlined text-[12px] text-rose-600">error</span>
                                {{ $q->assigned_at->diffForHumans(null, true) }} (Urgent)
                            </span>
                        @elseif($diffDays >= 3)
                            <span class="inline-flex items-center gap-1 font-semibold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 text-[10px]">
                                <span class="material-symbols-outlined text-[12px] text-amber-600">hourglass_top</span>
                                {{ $q->assigned_at->diffForHumans(null, true) }} waiting
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-[10px]">
                                <span class="material-symbols-outlined text-[12px] text-emerald-600">schedule</span>
                                {{ $q->assigned_at->diffForHumans(null, true) }} waiting
                            </span>
                        @endif
                    @else
                        <span class="text-outline">—</span>
                    @endif
                </div>
            </div>

            <div class="pt-1">
                <a href="{{ route('mufti.questions.show', $q) }}" class="w-full inline-flex items-center justify-center gap-1.5 py-2 rounded bg-primary text-on-primary text-[12px] font-semibold hover:bg-primary/90 transition-all shadow-sm">
                    <span>Answer Now</span>
                    <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                </a>
            </div>
        </div>
        @empty
        <div class="py-12 px-4 text-center">
            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center mx-auto mb-2.5 border border-emerald-200">
                <span class="material-symbols-outlined text-[24px]">check_circle</span>
            </div>
            <h3 class="font-title-md text-[15px] text-primary font-semibold">All Caught Up!</h3>
            <p class="font-body-sm text-[12px] text-on-surface-variant mt-1">
                No pending inquiries assigned to you at the moment.
            </p>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table View (Hidden on mobile) -->
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full text-left border-collapse text-[14px]">
            <thead>
                <tr class="border-b border-surface-variant bg-surface-container-low text-on-surface-variant font-label-md text-[12px] uppercase tracking-wider">
                    <th class="py-3.5 px-6 font-semibold text-left" scope="col">QU-ID</th>
                    <th class="py-3.5 px-6 font-semibold text-left" scope="col">Question Title & Body</th>
                    <th class="py-3.5 px-6 font-semibold text-left" scope="col">Category</th>
                    <th class="py-3.5 px-6 font-semibold text-left" scope="col">Assigned On</th>
                    <th class="py-3.5 px-6 font-semibold text-left" scope="col">Waiting Time</th>
                    <th class="py-3.5 px-6 font-semibold text-right" scope="col">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant/50">
                @forelse($questions as $q)
                <tr class="hover:bg-surface-container-low/60 transition-colors">
                    <td class="py-3.5 px-6 font-mono text-[13px] font-bold text-primary whitespace-nowrap">
                        {{ $q->question_id }}
                    </td>
                    <td class="py-3.5 px-6 max-w-md">
                        <div class="font-semibold text-on-surface text-[14px] line-clamp-1">
                            {{ Str::limit(strip_tags($q->title ?: $q->body), 75) }}
                        </div>
                        <div class="text-[12px] text-on-surface-variant line-clamp-1 mt-0.5">
                            {{ strip_tags($q->body) }}
                        </div>
                    </td>
                    <td class="py-3.5 px-6 text-[12px] whitespace-nowrap">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container border border-outline-variant/60 text-secondary">
                            {{ $q->category->name ?? 'Uncategorized' }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-[12px] text-on-surface-variant whitespace-nowrap">
                        <div class="font-medium text-on-surface">{{ $q->assigned_at ? $q->assigned_at->format('d M Y') : '—' }}</div>
                        <div class="text-[11px] text-outline mt-0.5">{{ $q->assigned_at ? $q->assigned_at->format('h:i A') : '' }}</div>
                    </td>
                    <td class="py-3.5 px-6 text-[12px] whitespace-nowrap">
                        @if($q->assigned_at)
                            @php
                                $diffDays = (int) $q->assigned_at->diffInDays(now());
                            @endphp
                            @if($diffDays >= 7)
                                <span class="inline-flex items-center gap-1 font-bold text-rose-800 bg-rose-50 px-2.5 py-1 rounded border border-rose-200 text-[11px]">
                                    <span class="material-symbols-outlined text-[13px] text-rose-600">error</span>
                                    <span>{{ $q->assigned_at->diffForHumans(null, true) }} (Urgent)</span>
                                </span>
                            @elseif($diffDays >= 3)
                                <span class="inline-flex items-center gap-1 font-semibold text-amber-900 bg-amber-50 px-2.5 py-1 rounded border border-amber-200 text-[11px]">
                                    <span class="material-symbols-outlined text-[13px] text-amber-700">hourglass_top</span>
                                    <span>{{ $q->assigned_at->diffForHumans(null, true) }} waiting</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 font-semibold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded border border-emerald-200 text-[11px]">
                                    <span class="material-symbols-outlined text-[13px] text-emerald-600">schedule</span>
                                    <span>{{ $q->assigned_at->diffForHumans(null, true) }} waiting</span>
                                </span>
                            @endif
                        @else
                            <span class="text-outline">—</span>
                        @endif
                    </td>
                    <td class="py-3.5 px-6 text-right whitespace-nowrap">
                        <a href="{{ route('mufti.questions.show', $q) }}" class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded bg-primary text-on-primary text-[12px] font-medium hover:bg-primary/90 transition-all shadow-sm">
                            <span>Answer Now</span>
                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </td>
                </tr>
                @empty
                <!-- Empty State -->
                <tr>
                    <td class="py-16 px-6 text-center bg-surface-container-lowest" colspan="6">
                        <div class="max-w-sm mx-auto flex flex-col items-center justify-center">
                            <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center mb-3 border border-emerald-200">
                                <span class="material-symbols-outlined text-[28px]">check_circle</span>
                            </div>
                            <h3 class="font-title-md text-[17px] text-primary mb-1 font-semibold">All Caught Up!</h3>
                            <p class="font-body-sm text-[13px] text-on-surface-variant leading-relaxed">
                                No pending inquiries assigned to you at the moment.
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($questions->hasPages())
    <div class="px-6 py-3 bg-surface-container-low/50 border-t border-surface-variant flex items-center justify-center text-[12px]">
        {{ $questions->links() }}
    </div>
    @endif
</section>
@endsection
