@extends('layouts.admin')
@section('title', 'Dashboard')
@section('topbar-title', 'Dashboard Overview')
@section('content')

{{-- Primary Stat Cards --}}
<div class="row g-3 mb-4">
    @foreach([
        ['Total Questions', $stats['total'], 'bi-question-circle', 'linear-gradient(135deg,#1a7a4a,#145e38)'],
        ['Pending', $stats['pending'], 'bi-clock-history', 'linear-gradient(135deg,#fd7e14,#e8610a)'],
        ['Assigned', $stats['assigned'], 'bi-person-check', 'linear-gradient(135deg,#0dcaf0,#0aa5c5)'],
        ['Answered', $stats['answered'], 'bi-chat-check', 'linear-gradient(135deg,#0d6efd,#0a58ca)'],
        ['Published Fatwas', $stats['published'], 'bi-patch-check', 'linear-gradient(135deg,#c9a84c,#a8883a)'],
        ['Total Reads', $stats['total_reads'], 'bi-eye', 'linear-gradient(135deg,#6f42c1,#5a349a)'],
        ['Questioners', $stats['questioners'], 'bi-people', 'linear-gradient(135deg,#20c997,#12b886)'],
    ] as [$label, $val, $icon, $bg])
    <div class="col-6 col-xl">
        <div class="stat-card shadow-sm" style="background:{{ $bg }};">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="opacity-75 small mb-1">{{ $label }}</div>
                    <div class="fs-3 fw-bold">{{ number_format($val) }}</div>
                </div>
                <i class="bi {{ $icon }}" style="font-size:2rem; opacity:.4;"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Analytics Row --}}
<div class="row g-4 mb-4">
    {{-- Monthly Chart --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-bar-chart me-2"></i>Questions Submitted (Last 6 Months)</h6>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="90"></canvas>
            </div>
        </div>
    </div>

    {{-- Top Countries + Category --}}
    <div class="col-lg-4">
        {{-- Most Popular Category --}}
        @if($topCategory)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-trophy me-2 text-warning"></i>Most Queried Category</h6>
            </div>
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px;height:48px;background:#fff8e1;color:#c9a84c;font-size:1.5rem;">
                        <i class="bi bi-bookmark-star"></i>
                    </div>
                    <div>
                        <div class="fw-bold">{{ $topCategory->name }}</div>
                        <small class="text-muted">{{ $topCategory->q_count }} published question(s)</small>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Top Countries --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-globe2 me-2 text-info"></i>Top Countries</h6>
            </div>
            <div class="card-body py-2">
                @forelse($topCountries as $c)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small fw-semibold">
                        @if($c->country_code) <span class="text-uppercase text-muted me-1">{{ $c->country_code }}</span> @endif
                        {{ $c->country }}
                    </span>
                    <span class="badge bg-primary rounded-pill">{{ $c->count }}</span>
                </div>
                @empty
                <p class="text-muted small py-2 mb-0">Waiting for visitors... (Make sure to run migrations!)</p>
                @endforelse
            </div>
        </div>

        {{-- Top States/Regions --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-geo-alt me-2 text-danger"></i>Top States / Regions</h6>
            </div>
            <div class="card-body py-2">
                @forelse($topStates as $s)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small fw-semibold">{{ $s->region }} <span class="text-muted small">({{ $s->country }})</span></span>
                    <span class="badge bg-secondary rounded-pill">{{ $s->count }}</span>
                </div>
                @empty
                <p class="text-muted small py-2 mb-0">No regional data yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Moon Sighting & Hijri Manager --}}
<section class="mb-4 mt-2">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-moon-stars me-2 text-success"></i>Moon Sighting & Hijri Manager</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <p class="text-muted small mb-1">Current Calculated Date (Adjusted):</p>
                            <h4 class="fw-bold text-success mb-0">{{ $currentHijri }}</h4>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">Active</span>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.update') }}" method="POST" class="border-top pt-3">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-muted">Manual Date Adjustment (Days)</label>
                                <select name="hijri_adjustment" class="form-select border-success-subtle">
                                    <option value="-2" {{ $hijriAdjustment == -2 ? 'selected' : '' }}>-2 Days (Earlier)</option>
                                    <option value="-1" {{ $hijriAdjustment == -1 ? 'selected' : '' }}>-1 Day (Earlier)</option>
                                    <option value="0"  {{ $hijriAdjustment == 0  ? 'selected' : '' }}>0 (Standard Calculation)</option>
                                    <option value="1"  {{ $hijriAdjustment == 1  ? 'selected' : '' }}>+1 Day (Later)</option>
                                    <option value="2"  {{ $hijriAdjustment == 2  ? 'selected' : '' }}>+2 Days (Later)</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-calendar-check me-1"></i>Apply Shift
                                </button>
                            </div>
                        </div>
                        <p class="mt-3 x-small text-muted">
                            <i class="bi bi-info-circle me-1"></i> This setting aligns the portal's calendar with local moon sightings for Ramzan and Eid announcements.
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-eye me-2 text-primary"></i>Public Display Control</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="text-muted small">Determine if the Hijri date should be visible to all visitors on the public header.</p>
                        
                        <form action="{{ route('admin.settings.update') }}" method="POST" id="hijriToggleForm">
                            @csrf
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="show_hijri_public" value="1" id="hijriSwitch" 
                                       {{ \App\Models\SiteSetting::get('show_hijri_public', true) ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <label class="form-check-label fw-bold" for="hijriSwitch">Enable Arabic Calendar for Public</label>
                            </div>
                        </form>
                    </div>

                    <div class="bg-light p-3 rounded text-center border">
                        <span class="text-muted x-small d-block mb-1">Live Preview:</span>
                        <div class="badge bg-success p-2 px-3 shadow-sm rounded-pill"><i class="bi bi-moon-stars me-2"></i>{{ $currentHijri }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($monthlyLabels),
        datasets: [{
            label: 'Questions Submitted',
            data: @json($monthlyCounts),
            backgroundColor: 'rgba(26,122,74,0.8)',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
@endpush

{{-- Recent Questions --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Recent Questions</h6>
        <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-success btn-sm">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>ID</th><th>Questioner</th><th>Question</th><th>Madhab</th><th>Status</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($recentQuestions as $q)
                <tr>
                    <td><span class="badge bg-secondary">{{ $q->question_id }}</span></td>
                    <td>{{ $q->user->name }}</td>
                    <td>{{ Str::limit(strip_tags($q->body), 60) }}</td>
                    <td>{{ $q->madhab == 'Shafii' ? "Shafi'i" : $q->madhab }}</td>
                    <td><span class="badge badge-status-{{ $q->status }}">{{ ucfirst($q->status) }}</span></td>
                    <td><small class="text-muted">{{ $q->created_at->format('d M') }}</small></td>
                    <td><a href="{{ route('admin.questions.show', $q) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No questions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
