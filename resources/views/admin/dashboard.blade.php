@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('topbar-title', 'Administrative Command Center')
@section('topbar-icon', 'bi-speedometer2')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
    <span class="text-dark fw-semibold">Command Center</span>
@endsection

@section('content')

{{-- 1. Top Modern Metric Cards (8 Core Signals) --}}
<div class="row g-3 mb-4">
    @php
        $metricConfig = [
            ['Total Questions', $stats['total'], 'bi-journals', '#EFF6FF', '#2563EB', '+12% ↑', '#ECFDF5', '#059669'],
            ['Critical Pending', $stats['pending'], 'bi-clock-history', '#FFFBEB', '#D97706', ($stats['pending'] > 0 ? 'Requires Action' : 'Cleared'), '#FFFBEB', '#D97706'],
            ['Assigned Tasks', $stats['assigned'], 'bi-person-check', '#EFF6FF', '#0284C7', 'In Scholar Queue', '#F0F9FF', '#0284C7'],
            ['Completed Answers', $stats['answered'], 'bi-chat-check-fill', '#FAF5FF', '#9333EA', 'Awaiting Verification', '#FAF5FF', '#9333EA'],
            ['Published Fatwas', $stats['published'], 'bi-journal-check', '#ECFDF5', '#059669', 'Public Knowledge', '#ECFDF5', '#059669'],
            ['Knowledge Views', $stats['total_reads'], 'bi-eye', '#FDF2F8', '#DB2777', 'Total Impressions', '#FDF2F8', '#DB2777'],
            ['Active Users', $stats['questioners'], 'bi-people', '#F0FDF4', '#16A34A', 'Registered Inquirers', '#F0FDF4', '#16A34A'],
            ['Verified Signals', $stats['live_by_identity'], 'bi-fingerprint', '#F8FAFC', '#475569', 'Live Telemetry', '#F8FAFC', '#475569'],
        ];
    @endphp

    @foreach($metricConfig as [$label, $val, $icon, $iconBg, $iconColor, $badgeTxt, $badgeBg, $badgeColor])
    <div class="col-6 col-md-3 col-xl">
        <div class="metric-card-tile d-flex align-items-center justify-content-between h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="metric-icon-square" style="background: {{ $iconBg }}; color: {{ $iconColor }};">
                    <i class="bi {{ $icon }}"></i>
                </div>
                <div>
                    <h3 class="metric-val-num">{{ number_format($val) }}</h3>
                    <div class="metric-label-txt text-truncate" style="max-width: 110px;">{{ $label }}</div>
                </div>
            </div>
            <span class="metric-trend-pill d-none d-sm-inline-flex" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-size: 0.68rem;">
                {{ $badgeTxt }}
            </span>
        </div>
    </div>
    @endforeach
</div>

{{-- 2. Main Analytics Row: Growth Chart & Live Signals --}}
<div class="row g-4 mb-4">
    {{-- Knowledge Growth & Fatwa Progression Chart --}}
    <div class="col-lg-8">
        <div class="modern-card h-100 overflow-hidden d-flex flex-column">
            <div class="p-3.5 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center bg-white gap-2">
                <div>
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-graph-up text-primary"></i> Knowledge Growth & Fatwa Progression
                    </h6>
                    <div class="text-muted small mt-0.5">Lifelong institutional trajectory across all inquiries</div>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; font-size: 11px; padding: 4px 10px; border-radius: 20px;">
                        <i class="bi bi-clock-history me-1"></i>Lifelong ({{ $lifelongSpan }})
                    </span>

                    <div class="btn-group btn-group-sm p-1 bg-light rounded-pill border" role="group">
                        <button type="button" class="btn btn-sm rounded-pill fw-bold chart-toggle-btn active bg-white text-dark shadow-xs" id="btnViewYearly" onclick="switchChartView('yearly')" style="font-size: 11px;">
                            Yearly
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill fw-bold chart-toggle-btn text-muted" id="btnViewMonthly" onclick="switchChartView('monthly')" style="font-size: 11px;">
                            All Months
                        </button>
                        <button type="button" class="btn btn-sm rounded-pill fw-bold chart-toggle-btn text-muted" id="btnViewCumulative" onclick="switchChartView('cumulative')" style="font-size: 11px;">
                            Cumulative
                        </button>
                    </div>
                </div>
            </div>

            @php
                $liveMuftis = collect($facultyMuftis ?? [])->filter(fn($m) => $m->isOnline(5));
            @endphp

            {{-- Live Scholars Banner Strip --}}
            @if($liveMuftis->count() > 0)
            <div class="px-4 py-2.5 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: #ECFDF5; border-color: #D1FAE5 !important;">
                <div class="d-flex align-items-center gap-2">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
                    <span class="fw-bold text-success text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                        Live in Scholar Dashboard ({{ $liveMuftis->count() }}):
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @foreach($liveMuftis as $lMufti)
                    <a href="{{ route('admin.muftis.history', $lMufti) }}" class="text-decoration-none">
                        <div class="d-flex align-items-center gap-2 py-1 px-2.5 bg-white border border-success border-opacity-30 rounded-pill shadow-xs">
                            <img src="{{ $lMufti->avatar ? asset($lMufti->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($lMufti->name).'&background=102B43&color=fff&size=40' }}" 
                                 alt="{{ $lMufti->name }}" class="rounded-circle border" width="24" height="24" style="object-fit: cover;">
                            <span class="fw-bold text-dark small">{{ $lMufti->name }}</span>
                            <span class="badge bg-success rounded-pill px-2 py-0.5 text-white" style="font-size: 10px;">LIVE</span>
                            <span class="badge bg-light text-dark rounded-pill px-2 py-0.5 dash-live-timer font-monospace" data-start-time="{{ ($lMufti->last_login_at ?? now())->timestamp * 1000 }}" style="font-size: 10px;">
                                00:00:00
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="p-4 flex-grow-1 d-flex flex-column bg-white">
                <div class="flex-grow-1 position-relative mb-3" style="min-height: 320px; width: 100%;">
                    <canvas id="lifelongChart"></canvas>
                </div>

                {{-- Chart Metric Ribbon --}}
                <div class="row g-2 pt-3 border-top">
                    <div class="col-sm-3 col-6">
                        <div class="p-2.5 rounded-3 bg-light border text-center">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Total Inquiries</div>
                            <div class="fw-bold text-dark fs-6 mt-0.5">{{ number_format($stats['total']) }}</div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-2.5 rounded-3 bg-light border text-center">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Published Fatwas</div>
                            <div class="fw-bold text-success fs-6 mt-0.5">{{ number_format($stats['published']) }}</div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-2.5 rounded-3 bg-light border text-center">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Lifelong Span</div>
                            <div class="fw-bold text-primary fs-6 mt-0.5">{{ $lifelongSpan }}</div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-2.5 rounded-3 bg-light border text-center">
                            <div class="text-muted text-uppercase fw-bold" style="font-size: 0.68rem;">Peak Period</div>
                            <div class="fw-bold text-warning fs-6 mt-0.5">{{ $peakYearLabel }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Column: Live Visitors & System Quick Actions --}}
    <div class="col-lg-4">
        {{-- Live Visitors Telemetry Card --}}
        <div class="modern-card p-4 mb-3 position-relative overflow-hidden" style="background: linear-gradient(135deg, #102B43, #1E3A8A); color: #fff;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="small fw-bold text-uppercase d-flex align-items-center gap-2" style="letter-spacing: 0.8px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #4ADE80; box-shadow: 0 0 8px #4ADE80;"></span>
                    Live Visitors Telemetry
                </div>
                <i class="bi bi-broadcast opacity-50 fs-5"></i>
            </div>
            <h1 class="fw-bold mb-0 text-white" id="live-users-count" style="font-size: 2.4rem;">
                {{ number_format($stats['live_by_ip']) }}
            </h1>
            <p class="small text-white-50 mb-0 mt-1">Real-time unique client IP signals</p>
        </div>

        {{-- Fatwa Dossier PDF Export Card --}}
        <div class="modern-card p-3.5 mb-3" style="border-left: 4px solid #10B981 !important;">
            <div class="d-flex justify-content-between align-items-center mb-1.5">
                <h6 class="fw-bold mb-0 text-dark small text-uppercase" style="letter-spacing: 0.5px;">
                    <i class="bi bi-file-earmark-pdf text-success me-1"></i> Fatwa Dossier PDF Export
                </h6>
                <span class="badge" style="background: #ECFDF5; color: #059669; font-size: 10px;">Google Noto</span>
            </div>
            <p class="text-muted small mb-3" style="font-size: 0.78rem;">Export inquiries to A4 printable PDF with auto multilingual rendering.</p>
            
            <div class="d-grid gap-2">
                <a href="{{ route('admin.fatwas.export.pdf', ['status' => 'pending,accepted']) }}" target="_blank" class="btn btn-modern-primary justify-content-between py-2">
                    <span class="d-flex align-items-center gap-2">
                        <i class="bi bi-cloud-arrow-down"></i>
                        <span>Download Pending & Accepted</span>
                    </span>
                    <i class="bi bi-box-arrow-up-right small"></i>
                </a>

                <button type="button" class="btn btn-modern-secondary justify-content-center py-2" data-bs-toggle="modal" data-bs-target="#exportPdfModal">
                    <i class="bi bi-sliders"></i> Custom PDF Export Filters
                </button>
            </div>
        </div>

        {{-- Quick Maintenance Actions --}}
        <div class="modern-card p-3.5">
            <h6 class="fw-bold mb-3 small text-uppercase text-muted" style="letter-spacing: 0.5px;">System Shortcuts</h6>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.maintenance.clear-cache') }}" class="btn btn-light border text-start d-flex align-items-center justify-content-between p-2.5 rounded-3">
                    <div class="d-flex align-items-center gap-2.5">
                        <i class="bi bi-stars text-warning fs-5"></i>
                        <div>
                            <div class="fw-bold text-dark small">Flush Cache</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Purge application view & route cache</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>

                <a href="{{ route('admin.settings.system') }}" class="btn btn-light border text-start d-flex align-items-center justify-content-between p-2.5 rounded-3">
                    <div class="d-flex align-items-center gap-2.5">
                        <i class="bi bi-gear text-primary fs-5"></i>
                        <div>
                            <div class="fw-bold text-dark small">System Configuration</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Manage portal & calendar parameters</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted small"></i>
                </a>

                <a href="/system-health" target="_blank" class="btn btn-light border text-start d-flex align-items-center justify-content-between p-2.5 rounded-3">
                    <div class="d-flex align-items-center gap-2.5">
                        <i class="bi bi-shield-check text-info fs-5"></i>
                        <div>
                            <div class="fw-bold text-dark small">Diagnostics Portal</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Server, DB & API health telemetry</div>
                        </div>
                    </div>
                    <i class="bi bi-box-arrow-up-right text-muted small"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- 3. Feeds Row: Recent Inquiries & Geographic Telemetry --}}
<div class="row g-4">
    {{-- Recent Inquiries Stream Table --}}
    <div class="col-xl-9">
        <div class="modern-card overflow-hidden mb-4">
            <div class="p-3.5 px-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-stack text-primary"></i> Stream of Recent Inquiries
                </h6>
                <a href="{{ route('admin.questions.index') }}" class="btn btn-sm btn-modern-secondary">
                    View All Inquiries
                </a>
            </div>
            <div class="table-responsive">
                <table class="modern-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Reference</th>
                            <th>Subject</th>
                            <th>Assigned Scholar</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentQuestions as $q)
                        @php
                            $textSample = ($q->title ?? '') . ' ' . strip_tags($q->body ?? '');
                            $isMalayalam = (bool) preg_match('/[\x{0D00}-\x{0D7F}]/u', $textSample) || strtolower($q->language ?? '') === 'malayalam';
                            $isArabic = (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $textSample) || in_array(strtolower($q->language ?? ''), ['arabic', 'ar']);
                            $isUrdu = strtolower($q->language ?? '') === 'urdu';
                            $itemFontClass = $isMalayalam ? 'font-malayalam' : ($isUrdu ? 'font-urdu' : ($isArabic ? 'font-arabic' : ''));
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <span class="badge" style="background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE; font-weight: 700; border-radius: 6px; padding: 4px 8px; font-size: 0.76rem;">
                                    {{ $q->question_id }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark {{ $itemFontClass }}" dir="auto" style="font-size: 0.86rem;">
                                    {{ Str::limit(strip_tags($q->title ?: $q->body), 55) }}
                                </div>
                                <div class="text-muted small" style="font-size: 0.74rem;">
                                    {{ $q->madhab?->name ?? 'Common Ground' }} · {{ $q->category?->name ?? 'General' }}
                                </div>
                            </td>
                            <td>
                                @if($q->assigned_mufti_id)
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-semibold small">
                                            {{ $q->assignedMufti?->name ?? 'Scholar' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-muted small italic">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2.5 py-1 text-xs fw-semibold" style="{{ $q->status === 'published' ? 'background: #ECFDF5; color: #059669;' : ($q->status === 'pending' ? 'background: #FFFBEB; color: #D97706;' : 'background: #EFF6FF; color: #2563EB;') }}">
                                    {{ ucfirst(str_replace('_', ' ', $q->status)) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.questions.show', $q) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.78rem;">
                                    Manage <i class="bi bi-chevron-right ms-0.5"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recently Published Fatwas Feed --}}
        <div class="modern-card overflow-hidden">
            <div class="p-3.5 px-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-journal-check text-success"></i> Recently Published Fatwas
                </h6>
                <a href="{{ route('admin.questions.index', ['status' => 'published']) }}" class="btn btn-sm btn-modern-secondary">
                    See All Published
                </a>
            </div>
            <div class="table-responsive">
                <table class="modern-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">Reference</th>
                            <th>Subject</th>
                            <th>Category / Madhab</th>
                            <th class="text-center">Views</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPublishedFatwas as $fatwa)
                        @php
                            $textSamplePub = ($fatwa->title ?? '') . ' ' . strip_tags($fatwa->body ?? '');
                            $isMalayalamPub = (bool) preg_match('/[\x{0D00}-\x{0D7F}]/u', $textSamplePub) || strtolower($fatwa->language ?? '') === 'malayalam';
                            $isArabicPub = (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $textSamplePub) || in_array(strtolower($fatwa->language ?? ''), ['arabic', 'ar']);
                            $isUrduPub = strtolower($fatwa->language ?? '') === 'urdu';
                            $pubFontClass = $isMalayalamPub ? 'font-malayalam' : ($isUrduPub ? 'font-urdu' : ($isArabicPub ? 'font-arabic' : ''));
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <span class="badge" style="background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE; font-weight: 700; border-radius: 6px; padding: 4px 8px; font-size: 0.76rem;">
                                    {{ $fatwa->question_id }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark {{ $pubFontClass }}" dir="auto" style="font-size: 0.86rem;">
                                    {{ Str::limit(strip_tags($fatwa->title ?: $fatwa->body), 55) }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark small">{{ $fatwa->category?->name ?? 'General' }}</div>
                                <div class="text-muted" style="font-size: 0.74rem;">{{ $fatwa->madhab?->name ?? 'Common Ground' }}</div>
                            </td>
                            <td class="text-center">
                                <span class="small fw-semibold text-muted">
                                    <i class="bi bi-eye me-1"></i>{{ number_format($fatwa->views) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="/fatwa-archive/{{ $fatwa->id }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-semibold" style="font-size: 0.78rem;">
                                    View Public <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted small">No published fatwas found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Geographic Signals Column --}}
    <div class="col-xl-3">
        <div class="modern-card p-4 h-100">
            <h6 class="fw-bold mb-3 small text-uppercase text-muted" style="letter-spacing: 0.5px;">Geographic Signals</h6>
            @forelse($geoReach as $reach)
            <div class="mb-3.5 pb-2.5 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="small fw-bold text-dark">{{ $reach['country'] }}</span>
                    <span class="small text-primary fw-bold">{{ number_format($reach['total_count']) }}</span>
                </div>
                <div class="progress" style="height: 6px; border-radius: 10px; background: #E2E8F0;">
                    <div class="progress-bar" style="background: var(--primary-blue); width: {{ $loop->first ? '100%' : rand(30, 85).'%' }}; border-radius: 10px;"></div>
                </div>
                <div class="mt-1.5 d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                    <span><i class="bi bi-geo-alt me-1"></i>{{ $reach['top_region'] }}</span>
                    <span>{{ number_format($reach['region_count']) }} hits</span>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-globe2 fs-1 text-slate-300"></i>
                <p class="mt-2 small">Awaiting global signals</p>
            </div>
            @endforelse
            <a href="{{ route('admin.visitor.insights') }}" class="btn btn-modern-secondary w-100 justify-content-center mt-3">
                Full Global Intel
            </a>
        </div>
    </div>
</div>

{{-- Custom PDF Export Configuration Modal --}}
<div class="modal fade" id="exportPdfModal" tabindex="-1" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <form action="{{ route('admin.fatwas.export.pdf') }}" method="GET" target="_blank">
                <div class="modal-header text-white" style="background: var(--brand-navy-dark); padding: 1.25rem 1.5rem;">
                    <div>
                        <h5 class="modal-title fw-bold mb-0 d-flex align-items-center gap-2" id="exportPdfModalLabel">
                            <i class="bi bi-file-earmark-pdf-fill text-warning"></i> Bulk PDF Dossier Export
                        </h5>
                        <p class="mb-0 text-white-50 small mt-1" style="font-size: 0.78rem;">Export high-resolution printable PDF with 1 Question Per Page & Google Noto Fonts.</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4 bg-light">
                    {{-- Status Selection --}}
                    <div class="card border-0 shadow-xs mb-3">
                        <div class="card-body p-3">
                            <label class="form-label fw-bold text-dark small mb-2 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-check2-square text-success me-1"></i> Select Inquiry Statuses:</span>
                                <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none small fw-bold" onclick="toggleAllStatuses(this)">Select All</button>
                            </label>
                            <div class="row g-2">
                                @foreach([
                                    ['pending', 'Pending', 'text-warning', true],
                                    ['accepted', 'Accepted', 'text-primary', true],
                                    ['assigned', 'Assigned', 'text-info', false],
                                    ['answered', 'Answered', 'text-secondary', false],
                                    ['published', 'Published', 'text-success', false],
                                    ['rejected', 'Rejected', 'text-danger', false],
                                ] as [$sVal, $sLbl, $sColor, $checked])
                                <div class="col-sm-4 col-6">
                                    <div class="form-check p-2 bg-white rounded border">
                                        <input class="form-check-input status-checkbox ms-1" type="checkbox" name="status[]" value="{{ $sVal }}" id="st_{{ $sVal }}" {{ $checked ? 'checked' : '' }}>
                                        <label class="form-check-label ms-2 small fw-semibold {{ $sColor }}" for="st_{{ $sVal }}">{{ $sLbl }}</label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Language Selection --}}
                        <div class="col-md-6">
                            <div class="card border-0 shadow-xs h-100">
                                <div class="card-body p-3">
                                    <label class="form-label fw-bold text-dark small mb-1">
                                        <i class="bi bi-translate text-primary me-1"></i> Language & Font Engine:
                                    </label>
                                    <select class="form-select modern-select" name="language">
                                        <option value="all" selected>All Languages (Auto Noto Font)</option>
                                        <option value="Malayalam">Malayalam (Noto Serif Malayalam)</option>
                                        <option value="Arabic">Arabic (Noto Naskh Arabic & Amiri)</option>
                                        <option value="Urdu">Urdu (Noto Nastaliq Urdu)</option>
                                        <option value="English">English / Latin (Noto Sans)</option>
                                    </select>
                                    <div class="text-muted mt-1" style="font-size: 0.72rem;">Font family will render cleanly according to language selection.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Madhab & Category Filter --}}
                        <div class="col-md-6">
                            <div class="card border-0 shadow-xs h-100">
                                <div class="card-body p-3">
                                    <div class="mb-2">
                                        <label class="form-label fw-bold text-dark small mb-1">
                                            <i class="bi bi-compass text-secondary me-1"></i> Madhab Filter:
                                        </label>
                                        <select class="form-select modern-select" name="madhab_id">
                                            <option value="">All Madhabs</option>
                                            @foreach($madhabs ?? [] as $m)
                                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label fw-bold text-dark small mb-1">
                                            <i class="bi bi-folder2-open text-warning me-1"></i> Category Filter:
                                        </label>
                                        <select class="form-select modern-select" name="category_id">
                                            <option value="">All Categories</option>
                                            @foreach($categories ?? [] as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Export Output Preferences --}}
                    <div class="card border-0 shadow-xs mt-3">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="auto_print" value="1" id="autoPrintSwitch" checked>
                                        <label class="form-check-label small fw-semibold" for="autoPrintSwitch">
                                            Auto-open Print / Save as PDF dialog
                                        </label>
                                    </div>
                                    <div class="text-muted ps-4" style="font-size: 0.72rem;">Opens printer dialog directly on page load</div>
                                </div>
                                <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                    <span class="badge" style="background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; padding: 4px 10px;">
                                        <i class="bi bi-file-break me-1"></i> 1 Question Per Page (Strict CSS Break)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-modern-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modern-primary">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Generate & Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function toggleAllStatuses(btn) {
    const checkboxes = document.querySelectorAll('.status-checkbox');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    btn.innerText = allChecked ? 'Select All' : 'Deselect All';
}

// ── Lifelong Analytics Multi-Mode Datasets ──
const lifelongData = {
    yearly: {
        labels: @json($lifelongYears),
        datasets: [
            {
                label: 'Total Inquiries',
                data: @json($lifelongTotalCounts),
                backgroundColor: 'rgba(37, 99, 235, 0.85)',
                borderColor: '#2563EB',
                borderWidth: 1.5,
                borderRadius: 8,
                barPercentage: 0.55
            },
            {
                label: 'Published Fatwas',
                data: @json($lifelongPublishedCounts),
                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                borderColor: '#10B981',
                borderWidth: 1.5,
                borderRadius: 8,
                barPercentage: 0.55
            }
        ],
        type: 'bar'
    },
    monthly: {
        labels: @json($lifelongMonthLabels),
        datasets: [
            {
                label: 'Monthly Inquiries',
                data: @json($lifelongMonthCounts),
                borderColor: '#2563EB',
                backgroundColor: 'rgba(37, 99, 235, 0.12)',
                borderWidth: 2,
                fill: true,
                tension: 0.35,
                pointRadius: 2,
                pointHoverRadius: 6
            }
        ],
        type: 'line'
    },
    cumulative: {
        labels: @json($lifelongMonthLabels),
        datasets: [
            {
                label: 'Cumulative Total Inquiries',
                data: @json($cumulativeCounts),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.18)',
                borderWidth: 2.5,
                fill: true,
                tension: 0.25,
                pointRadius: 1,
                pointHoverRadius: 6
            }
        ],
        type: 'line'
    }
};

const ctx = document.getElementById('lifelongChart').getContext('2d');
let currentChart = new Chart(ctx, {
    type: lifelongData.yearly.type,
    data: {
        labels: lifelongData.yearly.labels,
        datasets: lifelongData.yearly.datasets
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    boxWidth: 12,
                    boxHeight: 12,
                    usePointStyle: true,
                    font: { size: 11, weight: 'bold' }
                }
            },
            tooltip: {
                backgroundColor: '#0F172A',
                padding: 10,
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                grid: { color: '#F1F5F9' },
                beginAtZero: true
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

function switchChartView(mode) {
    document.querySelectorAll('.chart-toggle-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-white', 'text-dark', 'shadow-xs');
        btn.classList.add('text-muted');
    });

    const activeBtn = document.getElementById(mode === 'yearly' ? 'btnViewYearly' : (mode === 'monthly' ? 'btnViewMonthly' : 'btnViewCumulative'));
    if (activeBtn) {
        activeBtn.classList.add('active', 'bg-white', 'text-dark', 'shadow-xs');
        activeBtn.classList.remove('text-muted');
    }

    const cfg = lifelongData[mode];
    currentChart.config.type = cfg.type;
    currentChart.data.labels = cfg.labels;
    currentChart.data.datasets = cfg.datasets;
    currentChart.update();
}

async function fetchLiveUsers() {
    if (document.hidden) return;
    try {
        const response = await fetch('{{ route("admin.live-stats.api") }}');
        const data = await response.json();
        if (data && document.getElementById('live-users-count')) {
            const countEl = document.getElementById('live-users-count');
            if (countEl.innerText !== String(data.byIp)) {
                countEl.innerText = data.byIp;
            }
        }
    } catch (error) {}
}
fetchLiveUsers();
setInterval(fetchLiveUsers, 5000);

// Live Running Session Timers
function updateDashLiveTimers() {
    document.querySelectorAll('.dash-live-timer').forEach(elem => {
        const startTime = parseInt(elem.getAttribute('data-start-time'), 10);
        if (isNaN(startTime)) return;
        const now = Date.now();
        const diffSecs = Math.max(0, Math.floor((now - startTime) / 1000));
        
        const hours = Math.floor(diffSecs / 3600);
        const minutes = Math.floor((diffSecs % 3600) / 60);
        const seconds = diffSecs % 60;
        
        const formatted = 
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(seconds).padStart(2, '0');
        
        elem.innerHTML = formatted;
    });
}
updateDashLiveTimers();
setInterval(updateDashLiveTimers, 1000);
</script>
@endpush

@endsection
