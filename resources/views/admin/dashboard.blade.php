@extends('layouts.admin')
@section('title', 'Admin Insight')
@section('topbar-title', 'Administrative Command Center')

@section('content')
<style>
    /* ── High-Fidelity Dash Aesthetics ── */
    .command-hub { background: #f0f4f8; min-height: 100vh; margin: -1.5rem; padding: 1.5rem; }
    
    .glass-tile {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.05);
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .glass-tile:hover { transform: translateY(-4px); box-shadow: 0 15px 45px rgba(0,0,0,0.08); }

    .glow-stat {
        padding: 24px;
        color: #fff;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .glow-stat::after {
        content: ''; position: absolute; top: -50%; right: -50%; width: 200%; height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
        pointer-events: none;
    }

    /* ── Live Signal ── */
    .live-card { background: #4f46e5; border-radius: 20px; color: #fff; overflow: hidden; position: relative; }
    .pulse-dot {
        display: inline-block; width: 10px; height: 10px;
        background: #4ade80; border-radius: 50%;
        box-shadow: 0 0 0 rgba(74, 222, 128, 0.4);
        animation: pulse-signal 2s infinite;
    }
    @keyframes pulse-signal {
        0% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.4); }
        70% { box-shadow: 0 0 0 12px rgba(74, 222, 128, 0); }
        100% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
    }

    /* ── Quick Actions ── */
    .utility-pill {
        display: flex; align-items: center; gap: 12px;
        background: #fff; border: 1px solid #e2e8f0;
        padding: 12px 20px; border-radius: 14px;
        color: #475569; font-weight: 700; font-size: 0.85rem;
        transition: 0.2s; text-decoration: none;
    }
    .utility-pill:hover { background: #f8fafc; border-color: var(--brand-blue); color: var(--brand-blue); }
</style>

<div class="command-hub">
    {{-- Top Stats: Radiant Style --}}
    <div class="row g-3 mb-4">
        @foreach([
            ['Total Questions', $stats['total'], 'bi-journals', 'linear-gradient(135deg, #1e293b, #0f172a)'],
            ['Critical Pending', $stats['pending'], 'bi-exclamation-octagon', 'linear-gradient(135deg, #ef4444, #b91c1c)'],
            ['Assigned Tasks', $stats['assigned'], 'bi-person-gear', 'linear-gradient(135deg, #f59e0b, #d97706)'],
            ['Completed Answers', $stats['answered'], 'bi-check2-all', 'linear-gradient(135deg, #3b82f6, #2563eb)'],
            ['Published Portals', $stats['published'], 'bi-patch-check-fill', 'linear-gradient(135deg, #059669, #065f46)'],
            ['Knowledge Views', $stats['total_reads'], 'bi-activity', 'linear-gradient(135deg, #8b5cf6, #5b21b6)'],
            ['Active Users', $stats['questioners'], 'bi-people-fill', 'linear-gradient(135deg, #ec4899, #be185d)'],
            ['Verified Signals', $stats['live_by_identity'], 'bi-fingerprint', 'linear-gradient(135deg, #64748b, #334155)'],
        ] as [$label, $val, $icon, $bg])
        <div class="col-6 col-xl-3">
            <div class="glow-stat shadow-sm h-100" style="background:{{ $bg }};">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="opacity-70 x-small fw-bold uppercase tracking-widest mb-1">{{ $label }}</div>
                        <h2 class="fw-800 mb-0">{{ number_format($val) }}</h2>
                    </div>
                    <div class="opacity-20 fs-1"><i class="bi {{ $icon }}"></i></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        {{-- Primary Lifelong Analytics --}}
        <div class="col-lg-8">
            <div class="glass-tile h-100 overflow-hidden d-flex flex-column">
                <div class="p-4 border-bottom d-flex flex-wrap justify-content-between align-items-center bg-white gap-2">
                    <div>
                        <h6 class="fw-800 mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-graph-up-arrow text-primary"></i>Knowledge Growth & Fatwa Progression
                        </h6>
                        <div class="x-small text-muted mt-1">Lifelong institutional trajectory across all inquiries</div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill fw-bold" style="font-size: 11px;">
                            <i class="bi bi-infinity me-1"></i>Lifelong ({{ $lifelongSpan }})
                        </span>

                        <div class="btn-group btn-group-sm p-1 bg-light rounded-pill border" role="group" aria-label="Chart View Mode">
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

                {{-- Live Muftis Alert / Prominent Card Strip (Appears When Muftis Are Live) --}}
                @if($liveMuftis->count() > 0)
                <div class="px-4 py-3 bg-success bg-opacity-10 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(90deg, rgba(16, 185, 129, 0.12), rgba(16, 185, 129, 0.04)) !important; border-bottom: 1.5px solid rgba(16, 185, 129, 0.25) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="pulse-dot" style="width: 11px; height: 11px; background: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.25);"></span>
                        <span class="fw-800 text-success text-uppercase tracking-wider" style="font-size: 0.88rem; letter-spacing: 0.6px;">
                            <i class="bi bi-broadcast me-1"></i>Live in Mufti Dashboard ({{ $liveMuftis->count() }}):
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @foreach($liveMuftis as $lMufti)
                        <a href="{{ route('admin.muftis.history', $lMufti) }}" class="text-decoration-none" title="Live Now · Click to view Work History & Live Session Details">
                            <div class="d-flex align-items-center gap-3 py-2 px-3 bg-white border border-success border-opacity-35 rounded-pill shadow-sm hover-scale" style="transition: all 0.2s ease;">
                                <div class="position-relative">
                                    <img src="{{ $lMufti->avatar ? asset($lMufti->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($lMufti->name).'&background=1a7a4a&color=fff&size=96' }}" 
                                         alt="{{ $lMufti->name }}" class="rounded-circle border border-2 border-success" width="34" height="34" style="object-fit: cover;">
                                    <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle" style="width: 10px; height: 10px;"></span>
                                </div>
                                
                                <span class="fw-800 text-dark" style="font-size: 0.98rem; letter-spacing: -0.2px;">{{ $lMufti->name }}</span>
                                
                                <span class="badge bg-success bg-opacity-20 text-success rounded-pill px-2.5 py-1 fw-800 d-inline-flex align-items-center gap-1" style="font-size: 11px; border: 1px solid rgba(16, 185, 129, 0.35);">
                                    <span class="live-pulse-dot" style="width: 6px; height: 6px;"></span>LIVE
                                </span>

                                {{-- Running Timer Pill --}}
                                <span class="badge bg-success text-white rounded-pill px-3 py-1 font-monospace dash-live-timer shadow-xs d-inline-flex align-items-center" 
                                      data-start-time="{{ ($lMufti->last_login_at ?? now())->timestamp * 1000 }}" 
                                      style="font-size: 12px; font-weight: 800; letter-spacing: 0.5px;">
                                    <i class="bi bi-stopwatch me-1"></i>00:00:00
                                </span>

                                <span class="badge bg-warning bg-opacity-20 text-dark border border-warning border-opacity-30 rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1" style="font-size: 11px;" title="Total Dashboard Logins">
                                    <i class="bi bi-box-arrow-in-right text-warning fw-bold"></i> {{ number_format($lMufti->login_count ?? 0) }} logins
                                </span>

                                <i class="bi bi-chevron-right text-muted small ms-1" style="font-size: 11px;"></i>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="p-4 flex-grow-1 d-flex flex-column">
                    <div class="flex-grow-1 position-relative mb-2" style="min-height: 330px; width: 100%;">
                        <canvas id="lifelongChart"></canvas>
                    </div>

                    {{-- Lifelong Quick Metric Ribbon --}}
                    <div class="row g-2 mt-auto pt-3 border-top">
                        <div class="col-sm-3 col-6">
                            <div class="p-2 rounded bg-light border text-center">
                                <div class="x-small text-muted text-uppercase fw-bold" style="font-size: 9px;">Total Inquiries</div>
                                <div class="fw-800 text-dark" style="font-size: 1.05rem;">{{ number_format($stats['total']) }}</div>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="p-2 rounded bg-light border text-center">
                                <div class="x-small text-muted text-uppercase fw-bold" style="font-size: 9px;">Published Fatwas</div>
                                <div class="fw-800 text-success" style="font-size: 1.05rem;">{{ number_format($stats['published']) }}</div>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="p-2 rounded bg-light border text-center">
                                <div class="x-small text-muted text-uppercase fw-bold" style="font-size: 9px;">Lifelong Span</div>
                                <div class="fw-800 text-primary" style="font-size: 1.05rem;">{{ $lifelongSpan }}</div>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="p-2 rounded bg-light border text-center">
                                <div class="x-small text-muted text-uppercase fw-bold" style="font-size: 9px;">Peak Inquiries</div>
                                <div class="fw-800 text-warning" style="font-size: 1.05rem;">{{ $peakYearLabel }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dynamic Utilities & Context --}}
        <div class="col-lg-4">
            {{-- Live Heartbeat --}}
            <div class="live-card p-4 shadow-lg mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="small fw-bold uppercase tracking-widest"><span class="pulse-dot me-2"></span>Live Visitors</div>
                    <i class="bi bi-broadcast opacity-30 fs-4"></i>
                </div>
                <h1 class="fw-800 mb-0" id="live-users-count">{{ number_format($stats['live_by_ip']) }}</h1>
                <p class="x-small opacity-70 mb-0 mt-1">Real-time unique IP signal processing</p>
            </div>

            {{-- PDF Export Hub Card --}}
            <div class="glass-tile p-4 mb-4" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-800 mb-0 text-success small uppercase tracking-wider">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i>Fatwa Dossier PDF Export
                    </h6>
                    <span class="badge bg-success bg-opacity-10 text-success border-0" style="font-size: 10px;">Google Noto</span>
                </div>
                <p class="x-small text-muted mb-3">Export questions to high-resolution A4 printable PDF (1 inquiry per page with auto font).</p>
                
                <div class="d-grid gap-2">
                    {{-- 1-Click Pending & Accepted Export --}}
                    <a href="{{ route('admin.fatwas.export.pdf', ['status' => 'pending,accepted']) }}" target="_blank" class="btn btn-success d-flex align-items-center justify-content-between py-2 px-3 rounded-3 shadow-xs text-white" style="background-color: #1e4d2b; border-color: #1e4d2b;">
                        <span class="d-flex align-items-center gap-2 fw-bold text-start" style="font-size: 0.85rem;">
                            <i class="bi bi-cloud-arrow-down-fill fs-5"></i>
                            <div>
                                <div>Download Pending & Accepted</div>
                                <div class="opacity-75" style="font-size: 0.72rem; font-weight: normal;">{{ ($stats['pending'] ?? 0) }} Pending · 1 Question/Page</div>
                            </div>
                        </span>
                        <i class="bi bi-box-arrow-up-right small"></i>
                    </a>

                    {{-- Custom PDF Export Modal Trigger --}}
                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 bg-white" data-bs-toggle="modal" data-bs-target="#exportPdfModal" style="font-size: 0.85rem; font-weight: 600;">
                        <i class="bi bi-sliders"></i>Custom PDF Export (All Statuses)
                    </button>
                </div>
            </div>

            {{-- Quick System Actions --}}
            <div class="glass-tile p-4">
                <h6 class="fw-800 mb-3 small uppercase tracking-wider text-muted">Maintenance Control</h6>
                <div class="row g-2">
                    <div class="col-12">
                        <a href="{{ route('admin.maintenance.clear-cache') }}" class="utility-pill w-100">
                            <i class="bi bi-stars text-warning fs-5"></i>
                            <div>
                                <div class="mb-0">Flush System Cache</div>
                                <div class="x-small text-muted fw-normal">Refresh all views & configs</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-12">
                        <a href="{{ route('admin.settings.system') }}" class="utility-pill w-100">
                            <i class="bi bi-sliders text-primary fs-5"></i>
                            <div>
                                <div class="mb-0">System Configuration</div>
                                <div class="x-small text-muted fw-normal">Calendar & Global Rules</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Recent Feed --}}
        <div class="col-xl-9">
            <div class="glass-tile overflow-hidden">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                    <h6 class="fw-800 mb-0"><i class="bi bi-stack me-2 text-brand"></i>Stream of Recent Inquiries</h6>
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-dark btn-sm rounded-pill px-4">See All Records</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="x-small fw-800 text-muted uppercase">
                                <th class="ps-4">Reference</th>
                                <th>Subject</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentQuestions as $q)
                            @php
                                $textSample = ($q->title ?? '') . ' ' . strip_tags($q->body ?? '');
                                $isMalayalam = (bool) preg_match('/[\x{0D00}-\x{0D7F}]/u', $textSample) || strtolower($q->language ?? '') === 'malayalam';
                                $isArabic = (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $textSample) || in_array(strtolower($q->language ?? ''), ['arabic', 'ar']);
                                $isUrdu = strtolower($q->language ?? '') === 'urdu';
                                $itemFontClass = $isMalayalam ? 'font-malayalam' : ($isUrdu ? 'font-urdu' : ($isArabic ? 'font-arabic' : 'font-eng'));
                            @endphp
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="ps-4"><span class="badge bg-light text-dark fw-bold border">ID-{{ $q->question_id }}</span></td>
                                <td>
                                    <div class="fw-bold text-dark {{ $itemFontClass }}" style="{{ $isMalayalam ? 'line-height: 1.45;' : '' }}">{{ Str::limit(strip_tags($q->title ?: $q->body), 50) }}</div>
                                    <div class="x-small text-muted">{{ $q->madhab?->name ?? 'Common Ground' }} · {{ $q->category?->name ?? 'General' }}</div>
                                </td>
                                <td>
                                    @if($q->assigned_mufti_id)
                                        @php
                                            $isAssignedOnline = $q->assignedMufti?->isOnline(5);
                                        @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="position-relative">
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle small d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                                    <i class="bi bi-person-fill"></i>
                                                </div>
                                                @if($isAssignedOnline)
                                                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" style="width: 8px; height: 8px;" title="Live Online in Mufti Dashboard"></span>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="small fw-semibold d-flex align-items-center gap-1">
                                                    {{ $q->assignedMufti?->name ?? 'Scholar' }}
                                                    @if($isAssignedOnline)
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-1 py-0" style="font-size: 8px;">LIVE</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted italic x-small">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $q->status == 'pending' ? 'warning' : ($q->status == 'published' ? 'success' : 'info') }} bg-opacity-10 text-{{ $q->status == 'pending' ? 'warning' : ($q->status == 'published' ? 'success' : 'info') }} border-0 px-3">
                                        {{ ucfirst($q->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.questions.show', $q) }}" class="btn btn-link btn-sm text-brand p-0 fw-bold">Manage <i class="bi bi-arrow-right ms-1"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Published Fatwas Feed --}}
            <div class="glass-tile overflow-hidden mt-4">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                    <h6 class="fw-800 mb-0"><i class="bi bi-patch-check-fill me-2 text-success"></i>Recently Published Fatwas</h6>
                    <a href="{{ route('admin.questions.index', ['status' => 'published']) }}" class="btn btn-success btn-sm rounded-pill px-4">See All Published</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="x-small fw-800 text-muted uppercase">
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
                                $pubFontClass = $isMalayalamPub ? 'font-malayalam' : ($isUrduPub ? 'font-urdu' : ($isArabicPub ? 'font-arabic' : 'font-eng'));
                            @endphp
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark fw-bold border">ID-{{ $fatwa->question_id }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark {{ $pubFontClass }}" style="{{ $isMalayalamPub ? 'line-height: 1.45;' : '' }}">{{ Str::limit(strip_tags($fatwa->title ?: $fatwa->body), 50) }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark small">{{ $fatwa->category?->name ?? 'General' }}</div>
                                    <div class="x-small text-muted">{{ $fatwa->madhab?->name ?? 'Common Ground' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="small fw-semibold text-muted">
                                        <i class="bi bi-eye me-1"></i>{{ number_format($fatwa->views) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('fatwa.show', $fatwa) }}" target="_blank" class="btn btn-link btn-sm text-success p-0 fw-bold">View Public <i class="bi bi-box-arrow-up-right ms-1"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted small">
                                    <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                    No published fatwas found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Geo-Context Widget --}}
        <div class="col-xl-3">
            <div class="glass-tile p-4 h-100">
                <h6 class="fw-800 mb-4 small uppercase tracking-wider text-muted">Geographic Signals</h6>
                @forelse($geoReach as $reach)
                <div class="mb-4 pb-3" style="border-bottom: 1px solid #f1f5f9;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-800">{{ $reach['country'] }}</span>
                        <span class="x-small text-primary fw-bold">{{ number_format($reach['total_count']) }}</span>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 10px; background: #eaedf0;">
                        <div class="progress-bar bg-primary" style="width: {{ $loop->first ? '100%' : rand(40, 90).'%' }}"></div>
                    </div>
                    <div class="mt-2 d-flex justify-content-between x-small text-muted">
                        <span><i class="bi bi-geo-alt me-1"></i>{{ $reach['top_region'] }}</span>
                        <span>{{ number_format($reach['region_count']) }} hits</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 opacity-20">
                    <i class="bi bi-globe2 fs-1"></i>
                    <p class="mt-2 small">Awaiting global signals</p>
                </div>
                @endforelse
                <a href="{{ route('admin.visitor.insights') }}" class="btn btn-outline-dark btn-sm w-100 rounded-pill mt-3">Full Global Intel</a>
            </div>
        </div>
    </div>

    {{-- Custom PDF Export Configuration Modal --}}
    <div class="modal fade" id="exportPdfModal" tabindex="-1" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <form action="{{ route('admin.fatwas.export.pdf') }}" method="GET" target="_blank">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e4d2b 0%, #15381f 100%); padding: 1.25rem 1.5rem;">
                        <div>
                            <h5 class="modal-title fw-800 mb-0 d-flex align-items-center gap-2" id="exportPdfModalLabel">
                                <i class="bi bi-file-earmark-pdf-fill text-warning"></i> Bulk PDF Dossier Export
                            </h5>
                            <p class="mb-0 text-white-50 x-small mt-1">Export high-resolution printable PDF with 1 Question Per Page & Google Noto Fonts.</p>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body p-4 bg-light">
                        {{-- Status Selection --}}
                        <div class="card border-0 shadow-xs mb-3">
                            <div class="card-body p-3">
                                <label class="form-label fw-bold text-dark small mb-2 d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-check2-square text-success me-1"></i> Select Inquiry Statuses:</span>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none x-small fw-bold" onclick="toggleAllStatuses(this)">Select All</button>
                                </label>
                                <div class="row g-2">
                                    <div class="col-sm-4 col-6">
                                        <div class="form-check p-2 bg-white rounded border">
                                            <input class="form-check-input status-checkbox ms-1" type="checkbox" name="status[]" value="pending" id="st_pending" checked>
                                            <label class="form-check-label ms-2 small fw-semibold text-warning" for="st_pending">Pending</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6">
                                        <div class="form-check p-2 bg-white rounded border">
                                            <input class="form-check-input status-checkbox ms-1" type="checkbox" name="status[]" value="accepted" id="st_accepted" checked>
                                            <label class="form-check-label ms-2 small fw-semibold text-primary" for="st_accepted">Accepted</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6">
                                        <div class="form-check p-2 bg-white rounded border">
                                            <input class="form-check-input status-checkbox ms-1" type="checkbox" name="status[]" value="assigned" id="st_assigned">
                                            <label class="form-check-label ms-2 small fw-semibold text-info" for="st_assigned">Assigned</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6">
                                        <div class="form-check p-2 bg-white rounded border">
                                            <input class="form-check-input status-checkbox ms-1" type="checkbox" name="status[]" value="answered" id="st_answered">
                                            <label class="form-check-label ms-2 small fw-semibold text-secondary" for="st_answered">Answered</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6">
                                        <div class="form-check p-2 bg-white rounded border">
                                            <input class="form-check-input status-checkbox ms-1" type="checkbox" name="status[]" value="approved" id="st_approved">
                                            <label class="form-check-label ms-2 small fw-semibold text-dark" for="st_approved">Approved</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6">
                                        <div class="form-check p-2 bg-white rounded border">
                                            <input class="form-check-input status-checkbox ms-1" type="checkbox" name="status[]" value="published" id="st_published">
                                            <label class="form-check-label ms-2 small fw-semibold text-success" for="st_published">Published</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6">
                                        <div class="form-check p-2 bg-white rounded border">
                                            <input class="form-check-input status-checkbox ms-1" type="checkbox" name="status[]" value="rejected" id="st_rejected">
                                            <label class="form-check-label ms-2 small fw-semibold text-danger" for="st_rejected">Rejected</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            {{-- Language Selection with Google Noto Fonts info --}}
                            <div class="col-md-6">
                                <div class="card border-0 shadow-xs h-100">
                                    <div class="card-body p-3">
                                        <label class="form-label fw-bold text-dark small mb-1">
                                            <i class="bi bi-translate text-primary me-1"></i> Language & Font Engine:
                                        </label>
                                        <select class="form-select form-select-sm rounded-3" name="language">
                                            <option value="all" selected>All Languages (Auto Noto Font)</option>
                                            <option value="Malayalam">Malayalam (Noto Serif Malayalam)</option>
                                            <option value="Arabic">Arabic (Noto Naskh Arabic & Amiri)</option>
                                            <option value="Urdu">Urdu (Noto Nastaliq Urdu)</option>
                                            <option value="English">English / Latin (Noto Sans)</option>
                                        </select>
                                        <div class="x-small text-muted mt-1">Font family will render cleanly according to language selection.</div>
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
                                            <select class="form-select form-select-sm rounded-3" name="madhab_id">
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
                                            <select class="form-select form-select-sm rounded-3" name="category_id">
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
                                        <div class="x-small text-muted ps-4">Opens printer dialog directly on page load</div>
                                    </div>
                                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                            <i class="bi bi-file-break me-1"></i> 1 Question Per Page (Strict CSS Break)
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-white border-top p-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2" style="background-color: #1e4d2b; border-color: #1e4d2b;">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Generate & Download PDF
                        </button>
                    </div>
                </form>
            </div>
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
                backgroundColor: 'rgba(30, 77, 43, 0.85)',
                borderColor: '#1e4d2b',
                borderWidth: 1.5,
                borderRadius: 8,
                barPercentage: 0.55
            },
            {
                label: 'Published Fatwas',
                data: @json($lifelongPublishedCounts),
                backgroundColor: 'rgba(201, 168, 76, 0.9)',
                borderColor: '#c9a84c',
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
                borderColor: '#1e4d2b',
                backgroundColor: 'rgba(30, 77, 43, 0.12)',
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
                borderColor: '#10b981',
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
                backgroundColor: '#0f172a',
                padding: 10,
                cornerRadius: 8
            }
        },
        scales: {
            y: {
                grid: { color: '#f1f5f9' },
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
        
        elem.innerHTML = '<i class="bi bi-stopwatch me-1"></i>' + formatted;
    });
}
updateDashLiveTimers();
setInterval(updateDashLiveTimers, 1000);
</script>
@endpush
@endsection
