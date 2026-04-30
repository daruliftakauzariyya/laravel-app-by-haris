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
        {{-- Primary Analytics --}}
        <div class="col-lg-8">
            <div class="glass-tile h-100 overflow-hidden">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-white">
                    <h6 class="fw-800 mb-0"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Knowledge Growth (Inquiries)</h6>
                    <span class="badge bg-light text-dark border">Last 6 Months</span>
                </div>
                <div class="p-4">
                    <canvas id="monthlyChart" height="110"></canvas>
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
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="ps-4"><span class="badge bg-light text-dark fw-bold border">ID-{{ $q->question_id }}</span></td>
                                <td>
                                    <div class="fw-bold text-dark">{{ Str::limit(strip_tags($q->title ?: $q->body), 45) }}</div>
                                    <div class="x-small text-muted">{{ $q->madhab?->name ?? 'Common Ground' }} · {{ $q->category?->name ?? 'General' }}</div>
                                </td>
                                <td>
                                    @if($q->assigned_mufti_id)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle small d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                            <span class="small fw-semibold">{{ $q->assignedMufti?->name ?? 'Scholar' }}</span>
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
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($monthlyLabels),
        datasets: [{
            label: 'Inquiries',
            data: @json($monthlyCounts),
            backgroundColor: '#1a7a4a',
            borderRadius: 8,
            barThickness: 30
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { 
            y: { grid: { display: false }, beginAtZero: true, ticks: { stepSize: 10 } },
            x: { grid: { display: false } }
        }
    }
});

async function fetchLiveUsers() {
    try {
        const response = await fetch('{{ route("admin.live-stats.api") }}');
        const data = await response.json();
        if (data) document.getElementById('live-users-count').innerText = data.byIp;
    } catch (error) {}
}
setInterval(fetchLiveUsers, 10000);
</script>
@endpush
@endsection
