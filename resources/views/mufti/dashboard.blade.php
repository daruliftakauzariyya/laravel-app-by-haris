@extends('layouts.mufti')
@section('title', 'Dashboard')

@section('content')
@php
    $scoreColor = $score >= 80 ? 'text-emerald-700' : ($score >= 50 ? 'text-amber-700' : 'text-stone-500');
    $scoreStroke = $score >= 80 ? '#15803d' : ($score >= 50 ? '#b45309' : '#78716c');
    $scoreLabel = $score >= 80 ? 'Optimal · Outstanding Throughput' : ($score >= 50 ? 'Steady · Good Flow' : 'Needs Deliberation · Efficiency Index');
@endphp

<!-- Page Header Sub-bar -->
<section class="border-b border-outline-variant/60 pb-3 flex flex-wrap items-baseline justify-between gap-2">
    <div>
        <h1 class="font-headline-lg text-[28px] text-primary tracking-tight font-medium">Dashboard</h1>
        <p class="font-body-sm text-[13px] text-on-surface-variant mt-0.5">Overview of assigned fiqh inquiries, active deliberative tasks, and institutional metrics.</p>
    </div>
    <div class="hidden md:flex items-center gap-1.5 text-label-sm text-[11px] text-secondary uppercase tracking-wider font-semibold">
        <span class="material-symbols-outlined text-[16px] text-emerald-700">verified</span>
        <span>Accredited Research Workspace</span>
    </div>
</section>


<!-- Mobile App APK Download Quick Banner -->
<section class="bg-gradient-to-r from-[#092217] via-[#143d2c] to-[#092217] border border-[#745a26]/40 rounded-xl p-4 md:p-5 text-white shadow-md relative overflow-hidden my-2">
    <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-gradient-to-l from-[#745a26]/20 to-transparent pointer-events-none"></div>

    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-xl bg-white/10 border border-[#e4c283]/40 p-1 flex items-center justify-center flex-shrink-0 shadow-inner">
                <img src="/app-logo.png" alt="Mufti App" class="w-full h-full object-contain rounded-lg" onerror="this.src='/assets/logo/fevi.png'">
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-headline text-lg font-bold text-white tracking-tight">Darul Ifta Mufti Mobile Application</h3>
                    <span class="bg-[#dcfce7] text-[#166534] text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">Android APK</span>
                </div>
                <p class="text-xs text-white/80 mt-0.5">
                    Real-time incoming call alerts, research workbench, and instant OTP access on your Android phone.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5 w-full md:w-auto">
            <a href="{{ asset('downloads/mufti-app.apk') }}" download="Darul-Ifta-Mufti-App.apk" class="inline-flex items-center justify-center gap-2 bg-[#745a26] hover:bg-[#8e6e2f] active:scale-95 text-white font-semibold text-xs md:text-sm px-4 py-2.5 rounded-lg shadow-sm transition-all duration-150 border border-[#e4c283]/50 w-full md:w-auto text-decoration-none">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Download Android APK (7.6 MB)</span>
            </a>
        </div>
    </div>
</section>

<!-- Key Metrics / Stats Row (3 Minimal Light Cards) -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Card 1: ACTIVE TRACK -->
    <article class="bg-surface-container-lowest border border-outline-variant/70 rounded-lg p-6 transition-all duration-150 hover:border-outline relative overflow-hidden shadow-sm">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-primary"></div>
        <div class="flex items-center justify-between text-outline mb-3">
            <span class="font-label-sm text-[11px] tracking-widest text-on-surface-variant font-semibold uppercase">ACTIVE TRACK</span>
            <span class="material-symbols-outlined text-[20px] text-primary">pending_actions</span>
        </div>
        <div class="flex items-baseline gap-2 my-1">
            <span class="font-headline-lg text-[36px] text-primary font-medium">{{ number_format($assigned) }}</span>
        </div>
        <p class="font-body-sm text-[13px] text-on-surface-variant mt-2">
            Currently assigned fiqh inquiries
        </p>
    </article>

    <!-- Card 2: FATWA CONTRIBUTIONS -->
    <article class="bg-surface-container-lowest border border-outline-variant/70 rounded-lg p-6 transition-all duration-150 hover:border-outline relative overflow-hidden shadow-sm">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-secondary"></div>
        <div class="flex items-center justify-between text-outline mb-3">
            <span class="font-label-sm text-[11px] tracking-widest text-on-surface-variant font-semibold uppercase">FATWA CONTRIBUTIONS</span>
            <span class="material-symbols-outlined text-[20px] text-secondary">history_edu</span>
        </div>
        <div class="flex items-baseline gap-2 my-1">
            <span class="font-headline-lg text-[36px] text-primary font-medium">{{ number_format($answered) }}</span>
        </div>
        <p class="font-body-sm text-[13px] text-on-surface-variant mt-2">
            Lifetime scholarly contributions
        </p>
    </article>

    <!-- Card 3: IFTA WORK SCORE -->
    <article class="bg-surface-container-lowest border border-outline-variant/70 rounded-lg p-6 transition-all duration-150 hover:border-outline relative overflow-hidden shadow-sm">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-outline"></div>
        <div class="flex items-center justify-between text-outline mb-3">
            <span class="font-label-sm text-[11px] tracking-widest text-on-surface-variant font-semibold uppercase">IFTA WORK SCORE</span>
            <span class="material-symbols-outlined text-[20px] text-outline">monitoring</span>
        </div>
        <div class="flex items-center justify-between my-1">
            <div class="flex items-baseline gap-2">
                <span class="font-headline-lg text-[36px] text-primary font-medium">{{ $score }}%</span>
            </div>
            <!-- Progress Ring -->
            <div class="relative w-11 h-11 flex items-center justify-center">
                <svg class="w-11 h-11 -rotate-90" viewBox="0 0 36 36">
                    <!-- Background circle -->
                    <path class="text-surface-variant stroke-current" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-width="3"></path>
                    <!-- Active progress -->
                    <path class="stroke-current" style="stroke: {{ $scoreStroke }};" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke-dasharray="{{ $score }}, 100" stroke-linecap="round" stroke-width="3"></path>
                </svg>
                <span class="absolute font-label-sm text-[10px] text-outline font-bold">{{ $score }}</span>
            </div>
        </div>
        <p class="font-body-sm text-[13px] text-on-surface-variant mt-2">
            {{ $scoreLabel }}
        </p>
    </article>
</section>

<!-- Table / Assignment Section -->
<section class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg overflow-hidden shadow-sm">
    <!-- Section Header -->
    <div class="px-6 py-4 border-b border-surface-variant/80 flex flex-wrap items-baseline justify-between gap-2">
        <div>
            <h2 class="font-headline-sm text-[18px] text-primary font-semibold">Recent Inquiries (Active)</h2>
            <p class="font-body-sm text-[13px] text-on-surface-variant">Active inquiries prioritized by Pending 1st, Opened 2nd, and Answered 3rd (no published fatwas)</p>
        </div>
        <div class="flex items-center gap-1.5 text-label-md text-[13px] text-outline">
            <span class="material-symbols-outlined text-[16px]">folder_open</span>
            <span>Active Queue: {{ count($recent) }} inquiries</span>
        </div>
    </div>

    <!-- Mobile Cards View (Visible on Mobile) -->
    <div class="block md:hidden divide-y divide-surface-variant/60">
        @forelse($recent as $q)
        <div class="p-4 flex flex-col gap-2.5">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[12px] font-bold text-primary bg-surface-container px-2 py-0.5 rounded border border-outline-variant">
                    {{ $q->question_id }}
                </span>
                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-surface-container border border-outline-variant/60 text-secondary">
                    {{ $q->madhab?->name ?? 'General' }}
                </span>
            </div>

            <div>
                <h4 class="font-semibold text-on-surface text-[14px] line-clamp-2 mb-1">
                    {{ $q->title ?: strip_tags(substr($q->body, 0, 70)) }}
                </h4>
                <p class="text-[12px] text-on-surface-variant line-clamp-2 mb-0">
                    {{ strip_tags($q->body) }}
                </p>
            </div>

            <div class="flex items-center justify-between pt-1 text-[11px] text-on-surface-variant">
                <span>{{ $q->created_at ? $q->created_at->format('d M Y') : '—' }}</span>
                @if($q->status === 'assigned' && empty($q->draft_answer))
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200" title="Priority 1: Pending">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        1. Pending
                    </span>
                @elseif($q->status === 'assigned' && !empty($q->draft_answer))
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-800 border border-sky-200" title="Priority 2: Opened / In Draft">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                        2. Opened
                    </span>
                @elseif($q->status === 'answered')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-800 border border-indigo-200" title="Priority 3: Answered">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                        3. Answered
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-stone-100 text-stone-700">
                        {{ ucfirst($q->status) }}
                    </span>
                @endif
            </div>

            <div class="pt-1">
                <a href="{{ route('mufti.questions.show', $q) }}" class="w-full py-2 rounded bg-primary text-on-primary text-[13px] font-medium hover:bg-primary/90 transition-all shadow-sm flex items-center justify-center gap-1.5">
                    <span>Answer / Review Inquiry</span>
                    <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                </a>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-on-surface-variant text-[13px]">
            No pending or active inquiries assigned to your profile.
        </div>
        @endforelse
    </div>

    <!-- Desktop Table Framework (Hidden on Mobile) -->
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full text-left border-collapse text-[14px]">
            <thead>
                <tr class="border-b border-surface-variant bg-surface-container-low text-on-surface-variant font-label-md text-[12px] uppercase tracking-wider">
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Question ID</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Inquiry Details</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Madhab</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Date</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Status (Priority)</th>
                    <th class="py-3 px-6 font-semibold text-right" scope="col">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant/50">
                @forelse($recent as $q)
                <tr class="hover:bg-surface-container-low/60 transition-colors">
                    <td class="py-3 px-6 font-mono text-[13px] font-bold text-primary">
                        {{ $q->question_id }}
                    </td>
                    <td class="py-3 px-6 max-w-md">
                        <div class="font-semibold text-on-surface text-[14px] line-clamp-1">
                            {{ $q->title ?: strip_tags(substr($q->body, 0, 70)) }}
                        </div>
                        <div class="text-[12px] text-on-surface-variant line-clamp-1 mt-0.5">
                            {{ strip_tags($q->body) }}
                        </div>
                    </td>
                    <td class="py-3 px-6 text-[12px]">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container border border-outline-variant/60 text-secondary">
                            {{ $q->madhab?->name ?? 'General' }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-[12px] text-on-surface-variant whitespace-nowrap">
                        {{ $q->created_at ? $q->created_at->format('d M Y') : '—' }}
                    </td>
                    <td class="py-3 px-6 text-[12px] whitespace-nowrap">
                        @if($q->status === 'assigned' && empty($q->draft_answer))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                1. Pending
                            </span>
                        @elseif($q->status === 'assigned' && !empty($q->draft_answer))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-sky-50 text-sky-800 border border-sky-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                                2. Opened (Draft)
                            </span>
                        @elseif($q->status === 'answered')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                3. Answered
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-stone-100 text-stone-700">
                                {{ ucfirst($q->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-6 text-right whitespace-nowrap">
                        <a href="{{ route('mufti.questions.show', $q) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-primary text-on-primary text-[12px] font-medium hover:bg-primary/90 transition-all shadow-sm">
                            <span>Answer / Review</span>
                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </td>
                </tr>
                @empty
                <!-- Empty State -->
                <tr>
                    <td class="py-16 px-6 text-center bg-surface-container-lowest" colspan="6">
                        <div class="max-w-sm mx-auto flex flex-col items-center justify-center">
                            <div class="w-14 h-14 rounded-full bg-surface-container flex items-center justify-center text-outline mb-3 border border-outline-variant/50">
                                <span class="material-symbols-outlined text-[28px]">move_to_inbox</span>
                            </div>
                            <h3 class="font-title-md text-[17px] text-primary mb-1 font-semibold">No Active Inquiries Available</h3>
                            <p class="font-body-sm text-[13px] text-on-surface-variant leading-relaxed">
                                You have no pending, opened, or answered fatwas currently in the active queue. All your resolved fatwas can be viewed under Published Fatwas.
                            </p>
                            <div class="mt-4 flex items-center gap-2 text-[11px] font-label-sm text-outline">
                                <a href="{{ route('mufti.questions.published') }}" class="text-primary hover:underline font-semibold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">menu_book</span>
                                    <span>Go to Published Fatwas</span>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Minimal Table Footer -->
    <div class="px-6 py-3 bg-surface-container-low/50 border-t border-surface-variant flex items-center justify-between text-[12px] font-label-md text-outline">
        <span>Showing {{ count($recent) }} active inquiries</span>
        <div class="flex items-center gap-4">
            <a href="{{ route('mufti.questions.published') }}" class="text-secondary hover:text-primary font-medium inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">menu_book</span>
                <span>Published Fatwas ({{ $publishedCount }})</span>
            </a>
            <a href="{{ route('mufti.questions') }}" class="text-primary hover:text-primary/80 font-semibold inline-flex items-center gap-1">
                <span>View All Inquiries</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            </a>
        </div>
    </div>
</section>
@endsection
