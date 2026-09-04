@extends('layouts.mufti')
@section('title', 'Global Fatwa Search & Archive')

@section('content')
<!-- Page Header Sub-bar -->
<section class="border-b border-outline-variant/60 pb-3 flex flex-wrap items-baseline justify-between gap-2">
    <div>
        <h1 class="font-headline-lg text-[28px] text-primary tracking-tight font-medium">Global Fatwa Search</h1>
        <p class="font-body-sm text-[13px] text-on-surface-variant mt-0.5">Comprehensive search across all jurisprudence records, classical references, and scholar inquiries.</p>
    </div>
    <div class="flex items-center gap-1.5 text-label-sm text-[11px] text-secondary uppercase tracking-wider font-semibold">
        <span class="material-symbols-outlined text-[16px] text-primary">manage_search</span>
        <span>Central Repository Index</span>
    </div>
</section>

<!-- Filter & Search Toolbar Card -->
<section class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg p-5 shadow-sm">
    <form method="GET" action="{{ route('mufti.search') }}" class="space-y-4">
        <!-- Main Search Query -->
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3.5 top-3 text-[22px] text-outline">search</span>
            <input type="text" name="q" value="{{ $query }}" placeholder="Search by Question ID (e.g. A0001), keywords, question text, rulings, or Arabic kitab references..."
                   class="w-full pl-11 pr-28 py-2.5 text-[15px] bg-white border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-on-surface font-medium shadow-xs" autofocus>
            <button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 px-4 rounded-md bg-primary text-on-primary text-[13px] font-semibold hover:bg-primary/90 transition-all flex items-center gap-1">
                <span>Search</span>
            </button>
        </div>

        <!-- Filter Selects Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-1">
            <!-- Scope Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1">Search Scope</label>
                <select name="scope" class="w-full py-2 px-3 text-[13px] bg-surface-container-low border border-outline-variant rounded-md focus:outline-none focus:ring-1 focus:ring-primary focus:bg-white text-on-surface">
                    <option value="all" {{ ($scope ?? 'all') == 'all' ? 'selected' : '' }}>Entire Platform (All Fatwas)</option>
                    <option value="my" {{ ($scope ?? '') == 'my' ? 'selected' : '' }}>My Inquiries & Fatwas Only</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="w-full py-2 px-3 text-[13px] bg-surface-container-low border border-outline-variant rounded-md focus:outline-none focus:ring-1 focus:ring-primary focus:bg-white text-on-surface">
                    <option value="all" {{ ($status ?? 'all') == 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="published" {{ ($status ?? '') == 'published' ? 'selected' : '' }}>Published Only</option>
                    <option value="answered" {{ ($status ?? '') == 'answered' ? 'selected' : '' }}>Answered</option>
                    <option value="assigned" {{ ($status ?? '') == 'assigned' ? 'selected' : '' }}>Assigned / Pending</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1">Category</label>
                <select name="category_id" class="w-full py-2 px-3 text-[13px] bg-surface-container-low border border-outline-variant rounded-md focus:outline-none focus:ring-1 focus:ring-primary focus:bg-white text-on-surface">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Madhab Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider mb-1">Madhab</label>
                <select name="madhab_id" class="w-full py-2 px-3 text-[13px] bg-surface-container-low border border-outline-variant rounded-md focus:outline-none focus:ring-1 focus:ring-primary focus:bg-white text-on-surface">
                    <option value="">All Madhabs</option>
                    @foreach($madhabs as $m)
                        <option value="{{ $m->id }}" {{ ($madhabId ?? '') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between pt-1 border-t border-surface-variant/50 text-[12px]">
            <span class="text-on-surface-variant">
                Found <strong class="text-primary font-bold">{{ number_format($totalFound) }}</strong> records
                @if(!empty($query)) for "<span class="text-primary font-semibold">{{ $query }}</span>"@endif
            </span>
            <a href="{{ route('mufti.search') }}" class="text-outline hover:text-primary transition-colors inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">refresh</span>
                <span>Reset Filters</span>
            </a>
        </div>
    </form>
</section>

<!-- Search Results Table / Cards -->
<section class="bg-surface-container-lowest border border-outline-variant/80 rounded-lg overflow-hidden shadow-sm">
    <!-- Desktop Table View -->
    <div class="hidden md:block w-full overflow-x-auto">
        <table class="w-full text-left border-collapse text-[14px]">
            <thead>
                <tr class="border-b border-surface-variant bg-surface-container-low text-on-surface-variant font-label-md text-[12px] uppercase tracking-wider">
                    <th class="py-3 px-6 font-semibold text-left" scope="col">QU-ID</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Inquiry / Fatwa Details</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Category & Madhab</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Scholar</th>
                    <th class="py-3 px-6 font-semibold text-left" scope="col">Status</th>
                    <th class="py-3 px-6 font-semibold text-right" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-variant/50">
                @forelse($questions as $q)
                <tr class="hover:bg-surface-container-low/60 transition-colors">
                    <td class="py-3.5 px-6 font-mono text-[13px] font-bold text-primary whitespace-nowrap">
                        <a href="{{ route('mufti.fatwa.read', $q) }}" class="text-primary hover:underline inline-flex items-center gap-1" title="Read Fatwa">
                            <span>{{ $q->question_id }}</span>
                            <span class="material-symbols-outlined text-[14px] text-outline">article</span>
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
                        <div class="font-medium text-on-surface">{{ $q->category->name ?? 'General' }}</div>
                        <span class="inline-block px-2 py-0.2 rounded-full text-[10px] font-semibold bg-surface-container border border-outline-variant/60 text-secondary mt-0.5">
                            {{ $q->madhab?->name ?? 'General' }}
                        </span>
                    </td>
                    <td class="py-3.5 px-6 text-[12px] text-on-surface-variant whitespace-nowrap">
                        <div class="font-medium text-on-surface">{{ $q->assignedMufti?->name ?? 'Unassigned' }}</div>
                        <div class="text-[11px] text-outline mt-0.5">{{ $q->created_at ? $q->created_at->format('d M Y') : '—' }}</div>
                    </td>
                    <td class="py-3.5 px-6 text-[12px] whitespace-nowrap">
                        @if($q->status === 'published')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Published
                            </span>
                        @elseif($q->status === 'answered')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-800 border border-indigo-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                Answered
                            </span>
                        @elseif($q->status === 'assigned')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                Assigned
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-stone-100 text-stone-700">
                                {{ ucfirst($q->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="py-3.5 px-6 text-right whitespace-nowrap">
                        <div class="inline-flex items-center gap-1.5">
                            <a href="{{ route('mufti.fatwa.read', $q) }}" class="px-2.5 py-1.5 rounded bg-primary text-on-primary text-[12px] font-medium hover:bg-primary/90 transition-all shadow-xs inline-flex items-center gap-1" title="Read in Mufti Reader">
                                <span class="material-symbols-outlined text-[14px]">auto_stories</span>
                                <span>Read</span>
                            </a>
                            <a href="{{ route('mufti.questions.show', $q) }}" class="px-2.5 py-1.5 rounded bg-surface-container border border-outline-variant text-on-surface text-[12px] font-medium hover:bg-surface-container-high transition-all shadow-xs inline-flex items-center gap-1" title="Scholarly Workspace">
                                <span class="material-symbols-outlined text-[14px]">edit_note</span>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="py-16 px-6 text-center bg-surface-container-lowest" colspan="6">
                        <div class="max-w-sm mx-auto flex flex-col items-center justify-center">
                            <div class="w-14 h-14 rounded-full bg-surface-container flex items-center justify-center text-outline mb-3 border border-outline-variant/50">
                                <span class="material-symbols-outlined text-[28px]">search_off</span>
                            </div>
                            <h3 class="font-title-md text-[17px] text-primary mb-1 font-semibold">No Fatwas Found</h3>
                            <p class="font-body-sm text-[13px] text-on-surface-variant leading-relaxed">
                                No jurisprudence records or inquiries match your search parameters. Try broadening your keywords or resetting filters.
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards View -->
    <div class="block md:hidden divide-y divide-surface-variant/60">
        @forelse($questions as $q)
        <div class="p-4 flex flex-col gap-2">
            <div class="flex items-center justify-between">
                <span class="font-mono text-[12px] font-bold text-primary bg-surface-container px-2 py-0.5 rounded border border-outline-variant">
                    {{ $q->question_id }}
                </span>
                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-surface-container border border-outline-variant/60 text-secondary">
                    {{ $q->madhab?->name ?? 'General' }}
                </span>
            </div>

            <div>
                <a href="{{ route('mufti.fatwa.read', $q) }}" class="font-semibold text-on-surface text-[14px] line-clamp-2 block">
                    {{ $q->title ?: strip_tags(substr($q->body, 0, 70)) }}
                </a>
                <p class="text-[12px] text-on-surface-variant line-clamp-2 mb-0 mt-1">
                    {{ strip_tags($q->answer ?: $q->body) }}
                </p>
            </div>

            <div class="flex items-center justify-between text-[11px] text-on-surface-variant pt-1">
                <span>By {{ $q->assignedMufti?->name ?? 'Scholar' }}</span>
                <span>{{ $q->created_at ? $q->created_at->format('d M Y') : '—' }}</span>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <a href="{{ route('mufti.fatwa.read', $q) }}" class="py-1.5 rounded bg-primary text-on-primary text-[12px] font-semibold text-center flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">auto_stories</span>
                    <span>Read Fatwa</span>
                </a>
                <a href="{{ route('mufti.questions.show', $q) }}" class="py-1.5 rounded bg-surface-container border border-outline-variant text-on-surface text-[12px] font-semibold text-center flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[15px]">edit_note</span>
                    <span>Workspace</span>
                </a>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-on-surface-variant text-[13px]">
            No fatwas found matching your query.
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($questions->hasPages())
    <div class="px-6 py-3 bg-surface-container-low/50 border-t border-surface-variant flex items-center justify-center text-[12px]">
        {{ $questions->links() }}
    </div>
    @endif
</section>
@endsection
