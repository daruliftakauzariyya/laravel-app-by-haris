@extends('layouts.mufti')
@section('title', ($question->title ?: 'Fatwa #' . $question->question_id) . ' — Fatwa Reader')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Noto+Naskh+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    .arabic-quote-box {
        font-family: 'Amiri', 'Noto Naskh Arabic', serif;
        direction: rtl;
        font-size: 1.28rem;
        line-height: 2.1;
        background: #fdfaf6;
        border-right: 4px solid #745a26;
        padding: 16px 20px;
        border-radius: 6px;
        color: #1d1b19;
    }
    .fatwa-content {
        line-height: 1.85;
        font-size: 15.5px;
        color: #2c2927;
    }
    .fatwa-content p {
        margin-bottom: 1.1rem;
    }
    .fatwa-content p:last-child {
        margin-bottom: 0;
    }
    @media print {
        header, nav, .no-print, #mobileDrawer, .page-action-bar {
            display: none !important;
        }
        body, main {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
        }
        .print-only {
            display: block !important;
        }
    }
</style>
@endpush

@section('content')
<!-- Top Breadcrumb & Action Toolbar -->
<section class="border-b border-outline-variant/60 pb-3 flex flex-wrap items-center justify-between gap-3 page-action-bar">
    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2 text-[12px] font-label-md text-outline">
            <a href="{{ route('mufti.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
            <span>/</span>
            <a href="{{ route('mufti.questions.published') }}" class="hover:text-primary transition-colors">Published Fatwas</a>
            <span>/</span>
            <span class="font-mono font-bold text-primary">{{ $question->question_id }}</span>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="font-headline-lg text-[22px] sm:text-[26px] text-primary tracking-tight font-medium m-0">
                {{ $question->title ?: 'Fatwa #' . $question->question_id }}
            </h1>
        </div>
    </div>

    <!-- Toolbar Action Buttons -->
    <div class="flex items-center gap-2 flex-wrap">
        <button type="button" onclick="window.print()" class="px-3 py-1.5 rounded bg-surface-container border border-outline-variant text-on-surface hover:bg-surface-container-high text-[13px] font-medium inline-flex items-center gap-1.5 transition-all shadow-xs" title="Print this Fatwa">
            <span class="material-symbols-outlined text-[16px]">print</span>
            <span class="hidden sm:inline">Print</span>
        </button>

        <button type="button" onclick="copyCitation()" id="copyBtn" class="px-3 py-1.5 rounded bg-surface-container border border-outline-variant text-on-surface hover:bg-surface-container-high text-[13px] font-medium inline-flex items-center gap-1.5 transition-all shadow-xs" title="Copy scholarly citation text">
            <span class="material-symbols-outlined text-[16px]" id="copyIcon">content_copy</span>
            <span id="copyText">Copy Citation</span>
        </button>

        <a href="{{ route('mufti.questions.show', $question) }}" class="px-3 py-1.5 rounded bg-surface-container border border-outline-variant text-primary hover:bg-primary/10 text-[13px] font-semibold inline-flex items-center gap-1.5 transition-all shadow-xs" title="Open in Scholarly Drafting Workspace">
            <span class="material-symbols-outlined text-[16px]">edit_note</span>
            <span>Workspace</span>
        </a>

        @if($question->status === 'published')
        <a href="{{ route('fatwa.show', $question) }}" target="_blank" class="px-3 py-1.5 rounded bg-primary text-on-primary hover:bg-primary/90 text-[13px] font-medium inline-flex items-center gap-1.5 transition-all shadow-xs" title="Open Public Website View">
            <span>Public View</span>
            <span class="material-symbols-outlined text-[15px]">open_in_new</span>
        </a>
        @endif
    </div>
</section>

<!-- Main Reader Layout: 8 cols dossier + 4 cols scholarly metadata sidebar -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Left / Center Dossier Content (8 cols) -->
    <div class="lg:col-span-8 flex flex-col gap-6">

        <!-- Question Dossier Card -->
        <article class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-6 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between pb-3 border-b border-surface-variant/60 mb-4 flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-secondary">help_center</span>
                    <span class="font-label-sm text-[12px] font-bold text-secondary uppercase tracking-wider">Inquiry Dossier</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[12px] font-bold text-primary bg-surface-container px-2.5 py-0.5 rounded border border-outline-variant">
                        {{ $question->question_id }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container border border-outline-variant/60 text-secondary">
                        {{ $question->madhab?->name ?? 'General Madhab' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-primary/10 text-primary border border-primary/20">
                        {{ $question->category?->name ?? 'General' }}
                    </span>
                </div>
            </div>

            @if($question->title)
            <h2 class="font-headline-md text-[20px] sm:text-[22px] font-semibold text-primary mb-3 leading-snug">
                {{ $question->title }}
            </h2>
            @endif

            <div class="fatwa-content text-on-surface bg-surface-container-low/40 p-4 rounded-md border border-outline-variant/40 mb-4 leading-relaxed">
                {!! nl2br(e($question->body)) !!}
            </div>

            <div class="flex items-center justify-between text-[12px] text-outline pt-2 border-t border-surface-variant/50 flex-wrap gap-2">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[15px]">person</span>
                    <span>Questioner: <strong class="text-on-surface-variant">{{ $question->user?->name ?? 'Anonymous Inquirer' }}</strong></span>
                    @if($question->user?->country)
                        <span>({{ $question->user->country }})</span>
                    @endif
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                    <span>Asked on {{ $question->created_at ? $question->created_at->format('d M Y • h:i A') : '—' }}</span>
                </div>
            </div>
        </article>

        <!-- Official Fatwa Response Card -->
        <article class="bg-surface-container-lowest border-2 border-primary/20 rounded-lg p-6 sm:p-7 shadow-sm relative">
            <!-- Decorative Scholar Header Badge -->
            <div class="flex items-center justify-between pb-4 border-b border-surface-variant/80 mb-5 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center border border-primary/20">
                        <span class="material-symbols-outlined text-[22px]">history_edu</span>
                    </div>
                    <div>
                        <div class="font-headline-sm text-[18px] font-bold text-primary">Authoritative Islamic Ruling</div>
                        <div class="text-[12px] text-on-surface-variant">Formal Legal Verdict (Fatwa) of Darul Ifta Kauzariyya</div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if($question->status === 'published')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[12px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                            Published
                        </span>
                    @elseif($question->status === 'answered')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[12px] font-bold bg-indigo-50 text-indigo-800 border border-indigo-200">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                            Answered
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[12px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                            {{ ucfirst($question->status) }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Bismillah Header -->
            <div class="text-center my-3 font-serif arabic-quote-box border-none bg-transparent text-[22px] text-primary" style="font-family: 'Amiri', serif;">
                بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ
            </div>

            <!-- Fatwa Answer Body -->
            <div class="fatwa-content py-2 leading-relaxed text-on-surface">
                @if($question->answer)
                    {!! \App\Models\QuestionAndAnswer::parseFatwaReferences($question->answer) !!}
                @elseif($question->draft_answer)
                    <div class="p-4 rounded bg-amber-50/70 border border-amber-200/80 text-amber-900 text-[13px] mb-3">
                        <div class="font-bold flex items-center gap-1 mb-1">
                            <span class="material-symbols-outlined text-[16px]">pending</span>
                            <span>Draft in Progress (Not Finalized)</span>
                        </div>
                        {!! \App\Models\QuestionAndAnswer::parseFatwaReferences($question->draft_answer) !!}
                    </div>
                @else
                    <div class="p-8 text-center text-outline italic text-[14px]">
                        No formal fatwa response text submitted for this inquiry yet.
                    </div>
                @endif
            </div>

            <!-- Wallahu A'lam Conclusion -->
            <div class="text-center my-4 font-serif arabic-quote-box border-none bg-transparent text-[18px] text-secondary" style="font-family: 'Amiri', serif;">
                وَاللهُ تَعَالَى أَعْلَمُ بِالصَّوَابِ
            </div>

            <!-- Scholar Signatures & Verification Badge Row -->
            <div class="mt-6 pt-5 border-t border-surface-variant/80 bg-surface-container-low/40 -mx-6 -mb-6 p-6 rounded-b-lg grid grid-cols-1 sm:grid-cols-3 gap-4 text-[12px]">
                <div>
                    <span class="text-outline font-semibold uppercase tracking-wider block text-[10px] mb-1">Prepared / Answered By</span>
                    <strong class="text-primary text-[13px] block">{{ $question->assignedMufti?->name ?? 'Darul Ifta Jurisconsult' }}</strong>
                    <span class="text-on-surface-variant">{{ $question->assignedMufti?->email ?? '' }}</span>
                </div>
                <div>
                    <span class="text-outline font-semibold uppercase tracking-wider block text-[10px] mb-1">Verified By Scholar</span>
                    <strong class="text-secondary text-[13px] block">
                        {{ $question->approver?->name ?? ($question->publisher?->name ?? 'Ifta Verification Board') }}
                    </strong>
                    <span class="text-emerald-700 font-medium inline-flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px]">verified</span>
                        <span>Authenticated</span>
                    </span>
                </div>
                <div>
                    <span class="text-outline font-semibold uppercase tracking-wider block text-[10px] mb-1">Formal Publication</span>
                    <strong class="text-primary text-[13px] block">
                        {{ $question->published_at ? $question->published_at->format('d M Y • h:i A') : ($question->updated_at ? $question->updated_at->format('d M Y') : '—') }}
                    </strong>
                    <span class="text-outline">Darul Ifta Kauzariyya Archive</span>
                </div>
            </div>
        </article>

        <!-- Classical Islamic References & Footnotes Card -->
        @if($question->references && $question->references->count() > 0)
        <article class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-6 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-surface-variant/60 mb-4">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-primary">menu_book</span>
                    <h3 class="font-title-md text-[17px] font-semibold text-primary m-0">Classical References & Citations</h3>
                </div>
                <span class="text-[11px] font-semibold bg-surface-container px-2.5 py-0.5 rounded-full border border-outline-variant text-secondary">
                    {{ $question->references->count() }} Source {{ Str::plural('Record', $question->references->count()) }}
                </span>
            </div>

            <div class="space-y-4">
                @foreach($question->references as $idx => $ref)
                <div class="bg-surface-container-low/50 border border-outline-variant/60 rounded-lg p-4 transition-all hover:border-outline">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-primary/10 text-primary text-[11px] font-bold flex items-center justify-center border border-primary/20">
                                {{ $idx + 1 }}
                            </span>
                            <span class="font-headline-sm text-[15px] font-bold text-primary">
                                {{ $ref->book_name }}
                            </span>
                        </div>
                        @if($ref->volume || $ref->page)
                        <span class="text-[11px] font-mono font-semibold text-outline bg-surface-container px-2 py-0.5 rounded border border-outline-variant/60">
                            {{ $ref->volume ? 'Vol: ' . $ref->volume : '' }}{{ $ref->volume && $ref->page ? ' • ' : '' }}{{ $ref->page ? 'Pg: ' . $ref->page : '' }}
                        </span>
                        @endif
                    </div>

                    @if($ref->arabic_text)
                    <div class="arabic-quote-box my-2.5">
                        {{ $ref->arabic_text }}
                    </div>
                    @endif

                    @if($ref->footnote)
                    <p class="text-[12px] text-on-surface-variant mt-2 italic pl-2 border-l-2 border-outline-variant mb-0">
                        {{ $ref->footnote }}
                    </p>
                    @endif
                </div>
                @endforeach
            </div>
        </article>
        @endif

    </div>

    <!-- Right Column: Scholarly Metadata & Related Fatwas (4 cols) -->
    <div class="lg:col-span-4 flex flex-col gap-6">

        <!-- Engagement & Quick Facts -->
        <article class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-5 shadow-sm">
            <h4 class="font-title-md text-[15px] font-bold text-primary pb-2.5 border-b border-surface-variant/60 mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-secondary">analytics</span>
                <span>Dossier Metrics</span>
            </h4>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-surface-container p-3 rounded-lg border border-outline-variant/60 text-center">
                    <div class="flex items-center justify-center gap-1 text-sky-800 mb-1">
                        <span class="material-symbols-outlined text-[17px]">visibility</span>
                        <span class="text-[11px] font-bold uppercase">Views</span>
                    </div>
                    <div class="font-headline-md text-[20px] font-bold text-primary">{{ number_format($question->views) }}</div>
                </div>

                <div class="bg-surface-container p-3 rounded-lg border border-outline-variant/60 text-center">
                    <div class="flex items-center justify-center gap-1 text-rose-800 mb-1">
                        <span class="material-symbols-outlined text-[17px]">favorite</span>
                        <span class="text-[11px] font-bold uppercase">Likes</span>
                    </div>
                    <div class="font-headline-md text-[20px] font-bold text-primary">{{ number_format($question->likes) }}</div>
                </div>
            </div>

            <ul class="space-y-2.5 text-[12px] text-on-surface-variant border-t border-surface-variant/50 pt-3 m-0 p-0 list-none">
                <li class="flex justify-between">
                    <span class="text-outline">Jurisprudence Track:</span>
                    <strong class="text-on-surface">{{ $question->madhab?->name ?? 'General' }}</strong>
                </li>
                <li class="flex justify-between">
                    <span class="text-outline">Category:</span>
                    <strong class="text-on-surface">{{ $question->category?->name ?? 'General' }}</strong>
                </li>
                <li class="flex justify-between">
                    <span class="text-outline">Language:</span>
                    <strong class="text-on-surface uppercase">{{ $question->language ?? 'English' }}</strong>
                </li>
                <li class="flex justify-between">
                    <span class="text-outline">Assigned Scholar:</span>
                    <strong class="text-primary">{{ $question->assignedMufti?->name ?? 'Unassigned' }}</strong>
                </li>
                <li class="flex justify-between">
                    <span class="text-outline">Assigned On:</span>
                    <span class="text-on-surface font-mono">{{ $question->created_at ? $question->created_at->format('d M Y') : '—' }}</span>
                </li>
                <li class="flex justify-between">
                    <span class="text-outline">Published Date:</span>
                    <span class="text-on-surface font-mono">{{ $question->published_at ? $question->published_at->format('d M Y') : 'Not yet published' }}</span>
                </li>
            </ul>
        </article>

        <!-- Related Fatwas in Same Subject -->
        @if($relatedFatwas && $relatedFatwas->count() > 0)
        <article class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-5 shadow-sm">
            <div class="flex items-center justify-between pb-2.5 border-b border-surface-variant/60 mb-3">
                <h4 class="font-title-md text-[15px] font-bold text-primary m-0 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-secondary">auto_stories</span>
                    <span>Related Jurisprudence</span>
                </h4>
                <span class="text-[11px] text-outline font-semibold">{{ $relatedFatwas->count() }} records</span>
            </div>

            <div class="divide-y divide-surface-variant/60">
                @foreach($relatedFatwas as $rel)
                <div class="py-2.5 first:pt-0 last:pb-0">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-mono text-[11px] font-bold text-primary bg-surface-container px-1.5 py-0.2 rounded border border-outline-variant">
                            {{ $rel->question_id }}
                        </span>
                        <span class="text-[10px] text-secondary font-semibold">
                            {{ $rel->madhab?->name ?? '' }}
                        </span>
                    </div>
                    <a href="{{ route('mufti.fatwa.read', $rel) }}" class="font-semibold text-[13px] text-on-surface hover:text-primary line-clamp-2 leading-snug">
                        {{ $rel->title ?: strip_tags(substr($rel->body, 0, 70)) }}
                    </a>
                </div>
                @endforeach
            </div>
        </article>
        @endif

        <!-- Quick Citation Formatter Box -->
        <article class="bg-surface-container-low border border-outline-variant/80 rounded-lg p-4 text-[12px]">
            <div class="flex items-center gap-2 mb-2 text-primary font-bold">
                <span class="material-symbols-outlined text-[16px]">format_quote</span>
                <span>Standard Citation Reference</span>
            </div>
            <textarea id="citationSource" readonly class="w-full text-[11px] bg-surface-container-lowest border border-outline-variant p-2 rounded text-on-surface-variant font-mono resize-none focus:outline-none" rows="3">Darul Ifta Kauzariyya, Fatwa #{{ $question->question_id }}: "{{ $question->title ?: strip_tags(substr($question->body, 0, 50)) }}", Resolved by {{ $question->assignedMufti?->name ?? 'Mufti' }}, {{ $question->published_at ? $question->published_at->format('Y') : date('Y') }}.</textarea>
        </article>

    </div>

</div>

@push('scripts')
<script>
    function copyCitation() {
        const text = document.getElementById('citationSource').value;
        navigator.clipboard.writeText(text).then(() => {
            const btnText = document.getElementById('copyText');
            const icon = document.getElementById('copyIcon');
            btnText.innerText = 'Copied!';
            icon.innerText = 'check';
            setTimeout(() => {
                btnText.innerText = 'Copy Citation';
                icon.innerText = 'content_copy';
            }, 2500);
        });
    }
</script>
@endpush
@endsection
