@extends('layouts.mufti')
@section('title', 'My Scholarly Work')

@section('content')
<!-- Page Header Sub-bar -->
<section class="border-b border-outline-variant/60 pb-3 flex flex-wrap items-baseline justify-between gap-2">
    <div>
        <h1 class="font-headline-lg text-[28px] text-primary tracking-tight font-medium">My Scholarly Work</h1>
        <p class="font-body-sm text-[13px] text-on-surface-variant mt-0.5">Assigned inquiries, scholarly responses, and verified jurisprudence records.</p>
    </div>
    <div class="flex items-center gap-1 text-[12px] font-label-md text-outline">
        <span class="material-symbols-outlined text-[16px]">folder_special</span>
        <span>Repository Active</span>
    </div>
</section>

{{-- Tab Navigation --}}
<div class="border-b border-surface-variant/80 flex items-center gap-6 overflow-x-auto text-nowrap">
    <a href="{{ route('mufti.questions', ['tab' => 'assigned']) }}"
       class="pb-3 text-[14px] font-medium inline-flex items-center gap-2 transition-all {{ $tab == 'assigned' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }}">
        <span class="material-symbols-outlined text-[18px]">assignment</span>
        <span>Assigned Inquiries</span>
        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $tab == 'assigned' ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">
            {{ $counts['assigned'] }}
        </span>
    </a>

    <a href="{{ route('mufti.questions', ['tab' => 'verified']) }}"
       class="pb-3 text-[14px] font-medium inline-flex items-center gap-2 transition-all {{ $tab == 'verified' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }}">
        <span class="material-symbols-outlined text-[18px]">verified_user</span>
        <span>Verified by Me</span>
        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $tab == 'verified' ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">
            {{ $counts['verified'] }}
        </span>
    </a>

    @if(Auth::user()->can_check_approve)
    <a href="{{ route('mufti.questions', ['tab' => 'approved']) }}"
       class="pb-3 text-[14px] font-medium inline-flex items-center gap-2 transition-all {{ $tab == 'approved' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }}">
        <span class="material-symbols-outlined text-[18px]">approval_delegation</span>
        <span>Approved by Me</span>
        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $tab == 'approved' ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant' }}">
            {{ $counts['approved'] }}
        </span>
    </a>
    @endif

    <a href="{{ route('mufti.questions.published') }}"
       class="pb-3 text-[14px] font-medium inline-flex items-center gap-2 transition-all {{ request()->routeIs('mufti.questions.published') || $tab == 'published' ? 'text-primary font-bold border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }}">
        <span class="material-symbols-outlined text-[18px]">menu_book</span>
        <span>Published Fatwas</span>
        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ request()->routeIs('mufti.questions.published') || $tab == 'published' ? 'bg-emerald-700 text-white' : 'bg-surface-container text-on-surface-variant' }}">
            {{ $counts['published'] }}
        </span>
    </a>
</div>

{{-- Main Table Container --}}
<section class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg overflow-hidden shadow-sm">
    <!-- Mobile Cards View (Visible on Mobile) -->
    <div class="block md:hidden divide-y divide-surface-variant/60">
        @forelse($questions as $q)
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
                <div class="text-[12px] text-on-surface-variant line-clamp-2 mb-1">
                    {{ strip_tags($q->body) }}
                </div>
                <div class="text-[11px] text-outline">
                    Questioner: <strong class="text-on-surface-variant">{{ $q->user->name }}</strong> ({{ $q->user->country ?? '—' }})
                </div>
            </div>

            <div class="flex items-center justify-between pt-1 text-[11px] text-on-surface-variant">
                <span>{{ $q->created_at ? $q->created_at->format('d M Y') : '—' }}</span>
                @if($q->status === 'assigned')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Assigned
                    </span>
                @elseif($q->status === 'answered')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Answered
                    </span>
                @elseif($q->status === 'published')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Published
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-stone-100 text-stone-700">
                        {{ ucfirst($q->status) }}
                    </span>
                @endif
            </div>

            <div class="pt-1">
                @if($tab == 'assigned')
                    <a href="{{ route('mufti.questions.show', $q) }}" class="w-full py-2 rounded bg-primary text-on-primary text-[13px] font-medium hover:bg-primary/90 transition-all shadow-sm flex items-center justify-center gap-1.5">
                        <span>{{ ($q->answer || $q->draft_answer) ? 'Continue Draft' : 'Write Fatwa' }}</span>
                        <span class="material-symbols-outlined text-[15px]">edit_note</span>
                    </a>
                @else
                    <a href="{{ route('mufti.questions.history', $q) }}" class="w-full py-2 rounded bg-surface-container border border-outline-variant text-on-surface text-[13px] font-medium hover:bg-surface-container-high transition-all flex items-center justify-center gap-1.5">
                        <span>View History</span>
                        <span class="material-symbols-outlined text-[15px]">history</span>
                    </a>
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-on-surface-variant text-[13px]">
            There are no inquiries in this category repository.
        </div>
        @endforelse
    </div>

    <!-- Desktop Table (Hidden on Mobile) -->
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full text-left border-collapse text-[14px]">
            <thead>
                <tr class="border-b border-surface-variant bg-surface-container-low text-on-surface-variant font-label-md text-[12px] uppercase tracking-wider">
                    <th class="py-3 px-6 font-semibold text-left" scope="col">ID</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Questioner</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Inquiry Details</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Madhab</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Date</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Status</th>
                    <th class="py-3 px-6 font-semibold text-right" scope="col">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant/50">
                @forelse($questions as $q)
                <tr class="hover:bg-surface-container-low/60 transition-colors">
                    <td class="py-3 px-6 font-mono text-[13px] font-bold text-primary">
                        {{ $q->question_id }}
                    </td>
                    <td class="py-3 px-6 whitespace-nowrap">
                        <div class="font-semibold text-on-surface text-[13px]">{{ $q->user->name }}</div>
                        <div class="text-[11px] text-on-surface-variant">{{ $q->user->country ?? '—' }}</div>
                    </td>
                    <td class="py-3 px-6 max-w-sm">
                        <div class="font-semibold text-on-surface text-[14px] line-clamp-1">
                            {{ $q->title ?: strip_tags(substr($q->body, 0, 70)) }}
                        </div>
                        <div class="text-[12px] text-on-surface-variant line-clamp-1 mt-0.5">
                            {{ strip_tags($q->body) }}
                        </div>
                    </td>
                    <td class="py-3 px-6 text-[12px] whitespace-nowrap">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-surface-container border border-outline-variant/60 text-secondary">
                            {{ $q->madhab?->name ?? 'General' }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-[12px] text-on-surface-variant whitespace-nowrap">
                        {{ $q->created_at ? $q->created_at->format('d M Y') : '—' }}
                    </td>
                    <td class="py-3 px-6 text-[12px] whitespace-nowrap">
                        @if($q->status === 'assigned')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Assigned
                            </span>
                        @elseif($q->status === 'answered')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                Answered
                            </span>
                        @elseif($q->status === 'published')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Published
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-stone-100 text-stone-700">
                                {{ ucfirst($q->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-6 text-right whitespace-nowrap">
                        @if($tab == 'assigned')
                            <a href="{{ route('mufti.questions.show', $q) }}" class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded bg-primary text-on-primary text-[12px] font-medium hover:bg-primary/90 transition-all shadow-sm">
                                <span>{{ ($q->answer || $q->draft_answer) ? 'Continue Draft' : 'Write Fatwa' }}</span>
                                <span class="material-symbols-outlined text-[14px]">edit_note</span>
                            </a>
                        @else
                            <a href="{{ route('mufti.questions.history', $q) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded bg-surface-container border border-outline-variant text-on-surface text-[12px] font-medium hover:bg-surface-container-high transition-all">
                                <span>History</span>
                                <span class="material-symbols-outlined text-[14px]">history</span>
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <!-- Empty State -->
                <tr>
                    <td class="py-16 px-6 text-center bg-surface-container-lowest" colspan="7">
                        <div class="max-w-sm mx-auto flex flex-col items-center justify-center">
                            <div class="w-14 h-14 rounded-full bg-surface-container flex items-center justify-center text-outline mb-3 border border-outline-variant/50">
                                <span class="material-symbols-outlined text-[28px]">inventory_2</span>
                            </div>
                            <h3 class="font-title-md text-[17px] text-primary mb-1 font-semibold">No Inquiries Found</h3>
                            <p class="font-body-sm text-[13px] text-on-surface-variant leading-relaxed">
                                There are no inquiries in this category repository.
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Minimal Table Footer & Pagination -->
    <div class="px-6 py-3 bg-surface-container-low/50 border-t border-surface-variant flex flex-wrap items-center justify-between gap-2 text-[12px] font-label-md text-outline">
        <div>
            Showing {{ $questions->firstItem() ?? 0 }} to {{ $questions->lastItem() ?? 0 }} of {{ $questions->total() }} entries
        </div>
        <div>
            {{ $questions->links() }}
        </div>
    </div>
</section>
@endsection
