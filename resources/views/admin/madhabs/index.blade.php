@extends('layouts.admin')
@section('title', 'Madhab Management')
@section('topbar-title', 'Madhab & Jurisprudence Schools')

@section('content')
<style>
    /* ── High-Fidelity Visual Styles for Madhab Hub ── */
    .madhab-hub {
        background: #f8fafc;
        margin: -1.5rem;
        padding: 1.75rem;
        min-height: calc(100vh - 70px);
        
    }

    /* ── Bento Stats ── */
    .bento-stat-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }
    .bento-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
        border-color: rgba(203, 213, 225, 0.9);
    }
    .bento-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    /* ── Madhab School Cards ── */
    .madhab-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 20px;
        padding: 1.5rem;
        position: relative;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.03);
    }
    .madhab-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.08);
        border-color: var(--brand-blue, #052961);
    }
    .madhab-emblem {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
        letter-spacing: -0.5px;
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    }

    /* Distinct Card Theme Gradients based on Fiqh Schools */
    .theme-hanafi {
        background: linear-gradient(135deg, rgba(5, 41, 97, 0.04) 0%, rgba(201, 168, 76, 0.08) 100%);
        border-left: 4px solid #052961;
    }
    .theme-shafii {
        background: linear-gradient(135deg, rgba(13, 148, 136, 0.04) 0%, rgba(16, 185, 129, 0.08) 100%);
        border-left: 4px solid #0d9488;
    }
    .theme-maliki {
        background: linear-gradient(135deg, rgba(217, 119, 6, 0.04) 0%, rgba(245, 158, 11, 0.08) 100%);
        border-left: 4px solid #d97706;
    }
    .theme-hanbali {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.04) 0%, rgba(99, 102, 241, 0.08) 100%);
        border-left: 4px solid #4f46e5;
    }
    .theme-general {
        background: linear-gradient(135deg, rgba(100, 116, 139, 0.04) 0%, rgba(148, 163, 184, 0.08) 100%);
        border-left: 4px solid #64748b;
    }

    /* ── Form Card ── */
    .studio-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.03);
    }

    /* ── Live Preview Pill ── */
    .live-preview-box {
        background: #f1f5f9;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        padding: 1rem;
    }

    /* Preset Chips */
    .preset-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: 0.2s;
    }
    .preset-chip:hover {
        background: #e2e8f0;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    /* Metric Mini-Pills */
    .metric-pill {
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 12px;
        padding: 0.6rem 0.85rem;
        flex: 1;
        text-align: center;
    }
</style>

<div class="madhab-hub">
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge px-2.5 py-1 rounded-pill" style="background: rgba(201, 168, 76, 0.15); color: #8c6e18; font-weight: 700; font-size: 0.75rem;">
                    <i class="bi bi-shield-check me-1"></i> Fiqh Governance
                </span>
                <span class="text-muted small">• Canonical Schools of Thought</span>
            </div>
            <h4 class="fw-800 text-dark mb-0 d-flex align-items-center gap-2">
                Jurisprudence Schools Management
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('fatwa.madhab') }}" target="_blank" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-pill fw-semibold">
                <i class="bi bi-box-arrow-up-right me-1"></i> Public Archive View
            </a>
        </div>
    </div>

    {{-- Bento Statistics Bar --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="bento-stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase tracking-wider">Active Schools</div>
                        <h3 class="fw-800 text-dark mb-0 mt-1">{{ number_format($stats['total_madhabs']) }}</h3>
                        <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>Canonical Fiqh</small>
                    </div>
                    <div class="bento-icon-box" style="background: rgba(5, 41, 97, 0.08); color: #052961;">
                        <i class="bi bi-book-half"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="bento-stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase tracking-wider">Inquiries Bound</div>
                        <h3 class="fw-800 text-dark mb-0 mt-1">{{ number_format($stats['total_questions']) }}</h3>
                        <small class="text-primary fw-semibold"><i class="bi bi-journals me-1"></i>Classified Questions</small>
                    </div>
                    <div class="bento-icon-box" style="background: rgba(14, 165, 233, 0.08); color: #0ea5e9;">
                        <i class="bi bi-question-diamond"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="bento-stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase tracking-wider">Published Fatwas</div>
                        <h3 class="fw-800 text-dark mb-0 mt-1">{{ number_format($stats['total_published']) }}</h3>
                        <small class="text-success fw-semibold"><i class="bi bi-patch-check-fill me-1"></i>Live in Archive</small>
                    </div>
                    <div class="bento-icon-box" style="background: rgba(16, 185, 129, 0.08); color: #10b981;">
                        <i class="bi bi-award"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="bento-stat-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-bold text-uppercase tracking-wider">Affiliated Muftis</div>
                        <h3 class="fw-800 text-dark mb-0 mt-1">{{ number_format($stats['total_muftis']) }}</h3>
                        <small class="text-info fw-semibold"><i class="bi bi-person-badge-fill me-1"></i>Scholarly Faculty</small>
                    </div>
                    <div class="bento-icon-box" style="background: rgba(99, 102, 241, 0.08); color: #6366f1;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Working Area (Full Width) --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-3">
        <div class="position-relative flex-grow-1" style="max-width: 420px;">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" id="school-search" class="form-control rounded-pill ps-5 py-2.5 border-0 shadow-sm" placeholder="Filter schools in real time..." oninput="filterMadhabs()">
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small fw-semibold" id="count-indicator">{{ $madhabs->count() }} Schools Registered</span>
            <div class="btn-group btn-group-sm rounded-pill p-0.5 bg-white border shadow-sm">
                <button type="button" class="btn btn-sm btn-primary active rounded-pill px-3 py-1.5 fw-bold" id="view-cards-btn" onclick="toggleView('cards')">
                    <i class="bi bi-grid-fill me-1"></i> Cards
                </button>
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3 py-1.5 fw-bold" id="view-table-btn" onclick="toggleView('table')">
                    <i class="bi bi-table me-1"></i> Table
                </button>
            </div>
        </div>
    </div>

    {{-- 1. Cards Showcase Grid (Full Width 3 or 4 Columns) --}}
    <div id="madhab-cards-container" class="row g-4">
        @forelse($madhabs as $madhab)
            @php
                $slug = strtolower($madhab->slug ?: Str::slug($madhab->name));
                $themeClass = 'theme-general';
                $arabicName = '';
                $emblemLetter = strtoupper(substr($madhab->name, 0, 1));
                
                if (str_contains($slug, 'hanafi')) {
                    $themeClass = 'theme-hanafi';
                    $arabicName = 'المذهب الحنفي';
                } elseif (str_contains($slug, 'shafi')) {
                    $themeClass = 'theme-shafii';
                    $arabicName = 'المذهب الشافعي';
                } elseif (str_contains($slug, 'maliki')) {
                    $themeClass = 'theme-maliki';
                    $arabicName = 'المذهب المالكي';
                } elseif (str_contains($slug, 'hanbali')) {
                    $themeClass = 'theme-hanbali';
                    $arabicName = 'المذهب الحنبلي';
                }
                
                $totalQ = $stats['total_questions'] > 0 ? $stats['total_questions'] : 1;
                $pct = round(($madhab->questions_count / $totalQ) * 100);
            @endphp
            <div class="col-12 col-md-6 col-xl-3 madhab-item" data-name="{{ strtolower($madhab->name) }}" data-slug="{{ $slug }}">
                <div class="madhab-card {{ $themeClass }} h-100 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="madhab-emblem bg-white text-dark">
                                    {{ $emblemLetter }}
                                </div>
                                <div>
                                    <h5 class="fw-800 text-dark mb-0">{{ $madhab->name }}</h5>
                                    @if($arabicName)
                                        <div class="small fw-semibold text-muted" style="font-family: 'Amiri', serif; direction: rtl;">{{ $arabicName }}</div>
                                    @else
                                        <div class="small text-muted">ID #{{ $madhab->id }} • {{ $madhab->slug }}</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Action Dropdown / Buttons --}}
                            <div class="d-flex align-items-center gap-1">
                                <button class="btn btn-sm btn-white border shadow-sm rounded-circle p-2" title="Edit School" onclick="openEditModal({{ $madhab->id }}, '{{ addslashes($madhab->name) }}')">
                                    <i class="bi bi-pencil text-primary"></i>
                                </button>
                                <form action="{{ route('admin.madhabs.destroy', $madhab) }}" method="POST" onsubmit="return confirm('Delete this Madhab ({{ addslashes($madhab->name) }})? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-white border shadow-sm rounded-circle p-2 text-danger" title="Delete School">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Metrics Grid --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="metric-pill">
                                    <div class="small text-muted" style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase;">Inquiries</div>
                                    <div class="fw-800 text-dark fs-6">{{ number_format($madhab->questions_count) }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-pill">
                                    <div class="small text-muted" style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase;">Published</div>
                                    <div class="fw-800 text-success fs-6">{{ number_format($madhab->published_questions_count) }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-pill">
                                    <div class="small text-muted" style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase;">Muftis</div>
                                    <div class="fw-800 text-info fs-6">{{ number_format($madhab->muftis_count) }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric-pill">
                                    <div class="small text-muted" style="font-size: 0.68rem; font-weight: 700; text-transform: uppercase;">Followers</div>
                                    <div class="fw-800 text-secondary fs-6">{{ number_format($madhab->users_count) }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Inquiries Volume Progress --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small text-muted mb-1" style="font-size: 0.75rem;">
                                <span>Archive Share</span>
                                <span class="fw-bold">{{ $pct }}% of all queries</span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 9999px; background: rgba(0,0,0,0.06);">
                                <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ max(4, $pct) }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                        <span class="badge bg-white text-dark border small rounded-pill px-2.5 py-1">
                            <i class="bi bi-tag-fill me-1 text-muted"></i> {{ $madhab->slug }}
                        </span>
                        <a href="{{ route('fatwa.archive', ['madhab_id' => $madhab->id]) }}" target="_blank" class="small text-primary fw-bold text-decoration-none">
                            Explore Archive <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded-4 border">
                    <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
                    <h6 class="fw-bold text-dark">No Jurisprudence Schools Registered</h6>
                </div>
            </div>
        @endforelse
    </div>

    {{-- 2. Tabular Data View --}}
    <div id="madhab-table-container" class="card border-0 shadow-sm rounded-4 overflow-hidden d-none">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 border-0 small text-uppercase text-muted">ID & School</th>
                            <th class="border-0 small text-uppercase text-muted">Slug</th>
                            <th class="border-0 small text-uppercase text-muted text-center">Inquiries</th>
                            <th class="border-0 small text-uppercase text-muted text-center">Published</th>
                            <th class="border-0 small text-uppercase text-muted text-center">Faculty</th>
                            <th class="text-end pe-4 border-0 small text-uppercase text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($madhabs as $madhab)
                            <tr class="madhab-row" data-name="{{ strtolower($madhab->name) }}" data-slug="{{ strtolower($madhab->slug) }}">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="badge bg-light text-dark border rounded-circle p-2 fw-bold" style="width: 34px; height: 34px; display:flex; align-items:center; justify-content:center;">
                                            {{ strtoupper(substr($madhab->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $madhab->name }}</div>
                                            <div class="text-muted small" style="font-size: 0.72rem;">ID #{{ $madhab->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code>{{ $madhab->slug }}</code>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill fw-bold">
                                        {{ $madhab->questions_count }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success text-white px-2.5 py-1.5 rounded-pill fw-bold">
                                        {{ $madhab->published_questions_count }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-white px-2.5 py-1.5 rounded-pill fw-bold">
                                        {{ $madhab->muftis_count }} Muftis
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1.5">
                                        <button class="btn btn-sm btn-light border rounded-3 p-1.5" onclick="openEditModal({{ $madhab->id }}, '{{ addslashes($madhab->name) }}')">
                                            <i class="bi bi-pencil-square text-primary"></i>
                                        </button>
                                        <form action="{{ route('admin.madhabs.destroy', $madhab) }}" method="POST" onsubmit="return confirm('Delete this Madhab?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border rounded-3 p-1.5 text-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Edit Madhab Modal --}}
<div class="modal fade" id="editMadhabModal" tabindex="-1" aria-labelledby="editMadhabModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-0 py-3">
                <h6 class="modal-title fw-800 text-dark" id="editMadhabModalLabel">
                    <i class="bi bi-pencil-square text-primary me-1.5"></i> Edit Jurisprudence School
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-madhab-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">School Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="modal-field-name" class="form-control rounded-3 py-2.5" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Update School</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openEditModal(id, name) {
        const form = document.getElementById('edit-madhab-form');
        form.action = `/admin/madhabs/${id}`;
        document.getElementById('modal-field-name').value = name;
        const modal = new bootstrap.Modal(document.getElementById('editMadhabModal'));
        modal.show();
    }

    function filterMadhabs() {
        const query = document.getElementById('school-search').value.toLowerCase().trim();
        const items = document.querySelectorAll('.madhab-item');
        const rows = document.querySelectorAll('.madhab-row');
        let count = 0;

        items.forEach(el => {
            const name = el.getAttribute('data-name') || '';
            const slug = el.getAttribute('data-slug') || '';
            if (name.includes(query) || slug.includes(query)) {
                el.classList.remove('d-none');
                count++;
            } else {
                el.classList.add('d-none');
            }
        });

        rows.forEach(el => {
            const name = el.getAttribute('data-name') || '';
            const slug = el.getAttribute('data-slug') || '';
            if (name.includes(query) || slug.includes(query)) {
                el.classList.remove('d-none');
            } else {
                el.classList.add('d-none');
            }
        });

        document.getElementById('count-indicator').innerText = count + ' Schools Shown';
    }

    function toggleView(view) {
        const cards = document.getElementById('madhab-cards-container');
        const table = document.getElementById('madhab-table-container');
        const cardsBtn = document.getElementById('view-cards-btn');
        const tableBtn = document.getElementById('view-table-btn');

        if (view === 'table') {
            cards.classList.add('d-none');
            table.classList.remove('d-none');
            cardsBtn.classList.remove('active', 'btn-primary');
            cardsBtn.classList.add('btn-light');
            tableBtn.classList.remove('btn-light');
            tableBtn.classList.add('active', 'btn-primary');
        } else {
            table.classList.add('d-none');
            cards.classList.remove('d-none');
            tableBtn.classList.remove('active', 'btn-primary');
            tableBtn.classList.add('btn-light');
            cardsBtn.classList.remove('btn-light');
            cardsBtn.classList.add('active', 'btn-primary');
        }
    }
</script>
@endpush
@endsection
