@extends('layouts.mufti')
@section('title', 'Published Fatwas')

@section('content')
<!-- Page Header Sub-bar -->
<section class="border-b border-outline-variant/60 pb-3 flex flex-wrap items-baseline justify-between gap-2">
    <div>
        <h1 class="font-headline-lg text-[28px] text-primary tracking-tight font-medium">Published Fatwas</h1>
        <p class="font-body-sm text-[13px] text-on-surface-variant mt-0.5">Authoritative Islamic rulings resolved by you and actively published for the global community.</p>
    </div>
    <div class="flex items-center gap-1.5 text-label-sm text-[11px] text-secondary uppercase tracking-wider font-semibold">
        <span class="material-symbols-outlined text-[16px] text-emerald-700">menu_book</span>
        <span>Public Archive Live</span>
    </div>
</section>

<!-- KPI Metrics Row -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Card 1: TOTAL PUBLISHED -->
    <article class="bg-surface-container-lowest border border-outline-variant/70 rounded-lg p-5 transition-all duration-150 relative overflow-hidden shadow-sm">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-emerald-600"></div>
        <div class="flex items-center justify-between text-outline mb-2">
            <span class="font-label-sm text-[11px] tracking-widest text-on-surface-variant font-semibold uppercase">TOTAL PUBLISHED</span>
            <span class="material-symbols-outlined text-[20px] text-emerald-700">verified</span>
        </div>
        <div class="flex items-baseline gap-2 my-1">
            <span class="font-headline-lg text-[32px] text-primary font-medium">{{ number_format($totalPublished) }}</span>
        </div>
        <p class="font-body-sm text-[12px] text-on-surface-variant">
            Live jurisprudence records on portal
        </p>
    </article>

    <!-- Card 2: GLOBAL READERSHIP -->
    <article class="bg-surface-container-lowest border border-outline-variant/70 rounded-lg p-5 transition-all duration-150 relative overflow-hidden shadow-sm">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-sky-600"></div>
        <div class="flex items-center justify-between text-outline mb-2">
            <span class="font-label-sm text-[11px] tracking-widest text-on-surface-variant font-semibold uppercase">GLOBAL READERSHIP</span>
            <span class="material-symbols-outlined text-[20px] text-sky-700">visibility</span>
        </div>
        <div class="flex items-baseline gap-2 my-1">
            <span class="font-headline-lg text-[32px] text-primary font-medium">{{ number_format($totalViews) }}</span>
        </div>
        <p class="font-body-sm text-[12px] text-on-surface-variant">
            Accumulated public views & reads
        </p>
    </article>

    <!-- Card 3: READER FAVORITES -->
    <article class="bg-surface-container-lowest border border-outline-variant/70 rounded-lg p-5 transition-all duration-150 relative overflow-hidden shadow-sm">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-amber-600"></div>
        <div class="flex items-center justify-between text-outline mb-2">
            <span class="font-label-sm text-[11px] tracking-widest text-on-surface-variant font-semibold uppercase">READER FAVORITES</span>
            <span class="material-symbols-outlined text-[20px] text-amber-700">favorite</span>
        </div>
        <div class="flex items-baseline gap-2 my-1">
            <span class="font-headline-lg text-[32px] text-primary font-medium">{{ number_format($totalLikes) }}</span>
        </div>
        <p class="font-body-sm text-[12px] text-on-surface-variant">
            Scholarly bookmarks & reader appreciations
        </p>
    </article>
</section>

<!-- Filter & Search Toolbar -->
<section class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-4 shadow-sm">
    <form method="GET" action="{{ route('mufti.questions.published') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        <!-- Search Keyword -->
        <div class="md:col-span-5">
            <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1">Search Rulings</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-outline">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Question ID, title, or keywords..."
                       class="w-full pl-9 pr-3 py-2 text-[13px] bg-surface-container-low border border-outline-variant rounded-md focus:outline-none focus:ring-1 focus:ring-primary focus:bg-white text-on-surface">
            </div>
        </div>

        <!-- Category Filter -->
        <div class="md:col-span-3">
            <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1">Category</label>
            <select name="category_id" class="w-full py-2 px-3 text-[13px] bg-surface-container-low border border-outline-variant rounded-md focus:outline-none focus:ring-1 focus:ring-primary focus:bg-white text-on-surface">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Madhab Filter -->
        <div class="md:col-span-2">
            <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1">Madhab</label>
            <select name="madhab_id" class="w-full py-2 px-3 text-[13px] bg-surface-container-low border border-outline-variant rounded-md focus:outline-none focus:ring-1 focus:ring-primary focus:bg-white text-on-surface">
                <option value="">All Madhabs</option>
                @foreach($madhabs as $m)
                    <option value="{{ $m->id }}" {{ request('madhab_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="md:col-span-2 flex items-center gap-2">
            <button type="submit" class="flex-1 py-2 px-3 bg-primary text-on-primary text-[12px] font-semibold rounded-md hover:bg-primary/90 transition-all flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                <span>Filter</span>
            </button>
            @if(request('search') || request('category_id') || request('madhab_id'))
            <a href="{{ route('mufti.questions.published') }}" class="py-2 px-2.5 bg-surface-container border border-outline-variant text-secondary text-[12px] font-semibold rounded-md hover:bg-surface-container-high transition-all" title="Reset Filters">
                <span class="material-symbols-outlined text-[16px]">restart_alt</span>
            </a>
            @endif
        </div>
    </form>
</section>

<!-- Published Fatwas Table Container -->
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
                <p class="text-[12px] text-on-surface-variant line-clamp-2 mb-0">
                    {{ strip_tags($q->answer ?: $q->body) }}
                </p>
            </div>

            <div class="flex items-center justify-between pt-1 text-[11px] text-on-surface-variant">
                <span>Published: <strong>{{ $q->published_at ? $q->published_at->format('d M Y') : ($q->created_at ? $q->created_at->format('d M Y') : '—') }}</strong></span>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-0.5 text-sky-800">
                        <span class="material-symbols-outlined text-[13px]">visibility</span>
                        {{ number_format($q->views) }}
                    </span>
                    <span class="inline-flex items-center gap-0.5 text-rose-800">
                        <span class="material-symbols-outlined text-[13px]">favorite</span>
                        {{ number_format($q->likes) }}
                    </span>
                </div>
            </div>

            <div class="pt-1 grid grid-cols-2 gap-2">
                <a href="{{ route('mufti.fatwa.read', $q) }}" class="py-1.5 px-2 rounded bg-primary text-on-primary text-[12px] font-semibold hover:bg-primary/90 transition-all flex items-center justify-center gap-1 text-center">
                    <span class="material-symbols-outlined text-[14px]">auto_stories</span>
                    <span>Read Fatwa</span>
                </a>
                <a href="{{ route('mufti.questions.show', $q) }}" class="py-1.5 px-2 rounded bg-surface-container border border-outline-variant text-on-surface text-[12px] font-semibold hover:bg-surface-container-high transition-all flex items-center justify-center gap-1 text-center">
                    <span class="material-symbols-outlined text-[14px]">edit_note</span>
                    <span>Workspace</span>
                </a>
            </div>
        </div>
        @empty
        <div class="p-12 text-center text-on-surface-variant text-[13px]">
            <div class="w-12 h-12 rounded-full bg-surface-container text-outline flex items-center justify-center mx-auto mb-2 border border-outline-variant/60">
                <span class="material-symbols-outlined text-[24px]">menu_book</span>
            </div>
            <h3 class="font-title-md text-[16px] text-primary font-semibold">No Published Fatwas Found</h3>
            <p class="mt-1 text-[12px] text-on-surface-variant">No published scholarly rulings match your current filter parameters.</p>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table (Visible on Desktop) -->
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full text-left border-collapse text-[14px]">
            <thead>
                <tr class="border-b border-surface-variant bg-surface-container-low text-on-surface-variant font-label-md text-[12px] uppercase tracking-wider">
                    <th class="py-3 px-6 font-semibold text-left" scope="col">QU-ID</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Fatwa Subject & Ruling Excerpt</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Category & Madhab</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Published On</th>
                    <th class="py-3 px-6 font-semibold text-center" scope="col">Engagement</th>
                    <th class="py-3 px-6 font-semibold text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant/50">
                @forelse($questions as $q)
                <tr class="hover:bg-surface-container-low/60 transition-colors">
                    <td class="py-3.5 px-6 font-mono text-[13px] font-bold text-primary whitespace-nowrap">
                        <a href="{{ route('mufti.fatwa.read', $q) }}" class="text-primary hover:underline inline-flex items-center gap-1" title="Read Fatwa in Mufti Portal">
                            <span>{{ $q->question_id }}</span>
                            <span class="material-symbols-outlined text-[13px] text-outline">auto_stories</span>
                        </a>
                    </td>
                    <td class="py-3.5 px-6 max-w-md">
                        <a href="{{ route('mufti.fatwa.read', $q) }}" class="font-semibold text-on-surface hover:text-primary text-[14px] line-clamp-1 block">
                            {{ $q->title ?: strip_tags(substr($q->body, 0, 75)) }}
                        </a>
                        <div class="text-[12px] text-on-surface-variant line-clamp-1 mt-0.5">
                            {{ strip_tags($q->answer ?: $q->body) }}
                        </div>
                    </td>
                    <td class="py-3.5 px-6 text-[12px] whitespace-nowrap">
                        <div class="font-medium text-on-surface">{{ $q->category->name ?? 'Uncategorized' }}</div>
                        <span class="inline-block px-2 py-0.2 rounded-full text-[10px] font-semibold bg-surface-container border border-outline-variant/60 text-secondary mt-0.5">
                            {{ $q->madhab?->name ?? 'General' }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-[12px] text-on-surface-variant whitespace-nowrap">
                        <div class="font-medium text-on-surface">{{ $q->published_at ? $q->published_at->format('d M Y') : ($q->created_at ? $q->created_at->format('d M Y') : '—') }}</div>
                        <div class="text-[11px] text-outline mt-0.5">{{ $q->published_at ? $q->published_at->format('h:i A') : '' }}</div>
                    </td>
                    <td class="py-3.5 px-6 text-center whitespace-nowrap">
                        <div class="inline-flex items-center gap-2 bg-surface-container px-2.5 py-1 rounded-full border border-outline-variant/60 text-[11px] font-semibold text-on-surface-variant">
                            <span class="inline-flex items-center gap-0.5 text-sky-800" title="Public Views">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                <span>{{ number_format($q->views) }}</span>
                            </span>
                            <span class="text-outline-variant">•</span>
                            <span class="inline-flex items-center gap-0.5 text-rose-800" title="Favorites">
                                <span class="material-symbols-outlined text-[14px]">favorite</span>
                                <span>{{ number_format($q->likes) }}</span>
                            </span>
                        </div>
                    </td>
                    <td class="py-3.5 px-6 text-right whitespace-nowrap">
                        <div class="inline-flex items-center gap-1.5">
                            <a href="{{ route('mufti.fatwa.read', $q) }}" class="px-2.5 py-1.5 rounded bg-primary text-on-primary text-[12px] font-medium hover:bg-primary/90 transition-all shadow-xs inline-flex items-center gap-1" title="Read Fatwa">
                                <span class="material-symbols-outlined text-[14px]">auto_stories</span>
                                <span>Read</span>
                            </a>
                            <a href="{{ route('mufti.questions.show', $q) }}" class="px-2.5 py-1.5 rounded bg-surface-container border border-outline-variant text-on-surface text-[12px] font-medium hover:bg-surface-container-high transition-all shadow-xs inline-flex items-center gap-1" title="Open Workspace">
                                <span class="material-symbols-outlined text-[13px]">edit_note</span>
                            </a>
                            <a href="{{ route('fatwa.show', $q) }}" target="_blank" class="px-2 py-1.5 rounded bg-surface-container border border-outline-variant text-secondary text-[12px] font-medium hover:bg-surface-container-high transition-all shadow-xs inline-flex items-center gap-1" title="Public Website View">
                                <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="py-16 px-6 text-center bg-surface-container-lowest" colspan="6">
                        <div class="max-w-sm mx-auto flex flex-col items-center justify-center">
                            <div class="w-14 h-14 rounded-full bg-surface-container flex items-center justify-center text-outline mb-3 border border-outline-variant/50">
                                <span class="material-symbols-outlined text-[28px]">menu_book</span>
                            </div>
                            <h3 class="font-title-md text-[17px] text-primary mb-1 font-semibold">No Published Fatwas Found</h3>
                            <p class="font-body-sm text-[13px] text-on-surface-variant leading-relaxed">
                                No published rulings match your selection.
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($questions->hasPages())
    <div class="px-6 py-3 bg-surface-container-low/50 border-t border-surface-variant flex items-center justify-center text-[12px]">
        {{ $questions->links() }}
    </div>
    @endif
</section>
@endsection
