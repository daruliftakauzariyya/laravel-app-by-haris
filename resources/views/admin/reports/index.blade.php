@extends('layouts.admin')
@section('title', 'Comprehensive Reports & Analytics')
@section('topbar-title', 'Reports & Intelligence Center')

@section('content')
<div class="container-fluid px-0">
    {{-- Page Header & Actions --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--brand-blue);">
                <i class="bi bi-file-earmark-bar-graph me-2 text-success"></i> Reports & Analytics Intelligence
            </h3>
            <p class="text-muted small mb-0">Cross-dimensional metrics, scholar throughput, jurisprudence distribution & readership analytics.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            {{-- Print Button --}}
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print Report
            </button>
            {{-- CSV Export --}}
            <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm fw-bold">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export to CSV
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-lg-4">
            <form method="GET" action="{{ route('admin.reports.index') }}" id="reportFilterForm" class="row g-3 align-items-end">
                <input type="hidden" name="tab" value="{{ $activeTab }}">

                {{-- Date Preset --}}
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Time Horizon</label>
                    <select name="date_range" class="form-select form-select-sm" id="dateRangeSelect" onchange="toggleCustomDates()">
                        <option value="all" {{ $dateRange === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ $dateRange === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="this_week" {{ $dateRange === 'this_week' ? 'selected' : '' }}>This Week</option>
                        <option value="this_month" {{ $dateRange === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_30_days" {{ $dateRange === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="this_year" {{ $dateRange === 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="custom" {{ $dateRange === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                    </select>
                </div>

                {{-- Custom Date Inputs --}}
                <div class="col-md-2 col-sm-6 {{ $dateRange === 'custom' ? '' : 'd-none' }}" id="customStartCol">
                    <label class="form-label small fw-semibold text-muted mb-1">Start Date</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>
                <div class="col-md-2 col-sm-6 {{ $dateRange === 'custom' ? '' : 'd-none' }}" id="customEndCol">
                    <label class="form-label small fw-semibold text-muted mb-1">End Date</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>

                {{-- Madhab Filter --}}
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Madhab</label>
                    <select name="madhab_id" class="form-select form-select-sm">
                        <option value="">All Madhabs</option>
                        @foreach($allMadhabs as $md)
                            <option value="{{ $md->id }}" {{ $selectedMadhab == $md->id ? 'selected' : '' }}>{{ $md->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Category Filter --}}
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Category</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($allCategories as $cat)
                            <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Buttons --}}
                <div class="col-md-2 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1 rounded-pill fw-bold">
                        <i class="bi bi-funnel me-1"></i> Apply
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-light btn-sm rounded-pill border" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- 4 Executive Metric Hero Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Queries --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold tracking-wider">Total Questions</span>
                        <h2 class="fw-bold my-1 text-dark">{{ number_format($totalQuestions) }}</h2>
                        <span class="small text-muted">Submitted to platform</span>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-question-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Published Fatwas --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold tracking-wider">Published Fatwas</span>
                        <h2 class="fw-bold my-1 text-success">{{ number_format($publishedFatwas) }}</h2>
                        <span class="badge bg-success-subtle text-success small fw-bold">{{ $publishRate }}% Completion Rate</span>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="bi bi-journal-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pending & In Review --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold tracking-wider">Pending / In Progress</span>
                        <h2 class="fw-bold my-1 text-warning">{{ number_format($pendingQuestions + $inProgressQuestions) }}</h2>
                        <span class="small text-muted">{{ $pendingQuestions }} pending · {{ $inProgressQuestions }} assigned</span>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Public Readership & Engagement --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold tracking-wider">Public Reads</span>
                        <h2 class="fw-bold my-1 text-info">{{ number_format($totalViews) }}</h2>
                        <span class="small text-danger fw-semibold"><i class="bi bi-heart-fill me-1"></i>{{ number_format($totalLikes) }} Appreciations</span>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="bi bi-eye-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Multi-Variant Tabs Navigation --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs card-header-tabs m-0 border-0 flex-nowrap overflow-x-auto text-nowrap" id="reportTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link px-4 py-3 fw-bold {{ $activeTab === 'overview' ? 'active text-primary border-bottom border-3 border-primary' : 'text-muted' }}" 
                       href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'overview'])) }}">
                        <i class="bi bi-speedometer2 me-1"></i> Executive Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-4 py-3 fw-bold {{ $activeTab === 'lifecycle' ? 'active text-primary border-bottom border-3 border-primary' : 'text-muted' }}" 
                       href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'lifecycle'])) }}">
                        <i class="bi bi-arrow-repeat me-1"></i> Fatwa Lifecycle & Trends
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-4 py-3 fw-bold {{ $activeTab === 'scholars' ? 'active text-primary border-bottom border-3 border-primary' : 'text-muted' }}" 
                       href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'scholars'])) }}">
                        <i class="bi bi-person-badge me-1"></i> Scholar Performance Matrix
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-4 py-3 fw-bold {{ $activeTab === 'madhabs' ? 'active text-primary border-bottom border-3 border-primary' : 'text-muted' }}" 
                       href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'madhabs'])) }}">
                        <i class="bi bi-book-half me-1"></i> Jurisprudence & Madhabs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-4 py-3 fw-bold {{ $activeTab === 'categories' ? 'active text-primary border-bottom border-3 border-primary' : 'text-muted' }}" 
                       href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'categories'])) }}">
                        <i class="bi bi-tags me-1"></i> Categories & Top Fatwas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-4 py-3 fw-bold {{ $activeTab === 'demographics' ? 'active text-primary border-bottom border-3 border-primary' : 'text-muted' }}" 
                       href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'demographics'])) }}">
                        <i class="bi bi-globe me-1"></i> Geography & Demographics
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            {{-- TAB 1: EXECUTIVE OVERVIEW --}}
            @if($activeTab === 'overview')
                <div class="row g-4">
                    {{-- Status Distribution Progress Cards --}}
                    <div class="col-lg-5">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Status Distribution</h5>
                        <div class="d-flex flex-column gap-3">
                            @foreach($statusBreakdown as $sb)
                            @php $pct = $totalQuestions > 0 ? round(($sb['count'] / $totalQuestions) * 100, 1) : 0; @endphp
                            <div class="p-3 rounded-3 border" style="background: {{ $sb['bg'] }};">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark"><i class="bi {{ $sb['icon'] }} me-2" style="color: {{ $sb['color'] }};"></i>{{ $sb['label'] }}</span>
                                    <span class="fw-bold fs-5" style="color: {{ $sb['color'] }};">{{ number_format($sb['count']) }} <small class="text-muted fs-6">({{ $pct }}%)</small></span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $pct }}%; background-color: {{ $sb['color'] }};"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Quick Summary Highlights --}}
                    <div class="col-lg-7">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Platform Health Summary</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded-4 border h-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-success text-white p-2 rounded-3"><i class="bi bi-person-check fs-4"></i></div>
                                        <div>
                                            <div class="fw-bold fs-5 text-dark">{{ $activeMuftisCount }} Scholars</div>
                                            <div class="small text-muted">Active Muftis & Checkers</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded-4 border h-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary text-white p-2 rounded-3"><i class="bi bi-people fs-4"></i></div>
                                        <div>
                                            <div class="fw-bold fs-5 text-dark">{{ number_format($registeredQuestionersCount) }} Questioners</div>
                                            <div class="small text-muted">Registered Public Users</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded-4 border h-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-warning text-dark p-2 rounded-3"><i class="bi bi-translate fs-4"></i></div>
                                        <div>
                                            <div class="fw-bold fs-5 text-dark">{{ count($languageStats) }} Languages</div>
                                            <div class="small text-muted">Multilingual Portals</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded-4 border h-100">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-info text-white p-2 rounded-3"><i class="bi bi-tags fs-4"></i></div>
                                        <div>
                                            <div class="fw-bold fs-5 text-dark">{{ count($categoryStats) }} Active Topics</div>
                                            <div class="small text-muted">Jurisprudence Categories</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Callout --}}
                        <div class="card border-0 bg-success bg-opacity-10 p-3 rounded-4 mt-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check text-success fs-4"></i>
                                <div>
                                    <strong class="text-success">Verified Fatwa Archive Operation</strong>
                                    <div class="small text-muted">All published rulings undergo verification and Madhab classification before appearing in public archives.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- TAB 2: FATWA LIFECYCLE & MONTHLY TRENDS --}}
            @elseif($activeTab === 'lifecycle')
                <div>
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-calendar3 me-2 text-primary"></i>12-Month Publication & Submission Trend</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Month / Year</th>
                                    <th>Total Questions Inflow</th>
                                    <th>Fatwas Published</th>
                                    <th>Publication Ratio</th>
                                    <th>Progress Bar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyTrend as $mt)
                                @php $rate = $mt->total > 0 ? round(($mt->published / $mt->total) * 100, 1) : 0; @endphp
                                <tr>
                                    <td class="fw-bold text-dark">{{ $mt->month_year }}</td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1 fs-6">{{ number_format($mt->total) }}</span></td>
                                    <td><span class="badge bg-success-subtle text-success border border-success px-2 py-1 fs-6">{{ number_format($mt->published) }}</span></td>
                                    <td class="fw-bold {{ $rate >= 70 ? 'text-success' : ($rate >= 40 ? 'text-warning' : 'text-danger') }}">{{ $rate }}%</td>
                                    <td style="width: 25%;">
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: {{ $rate }}%;"></div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted">No monthly trend records found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            {{-- TAB 3: SCHOLAR / MUFTI WORKLOAD MATRIX --}}
            @elseif($activeTab === 'scholars')
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge me-2 text-success"></i>Mufti Performance & Workload Matrix</h5>
                        <span class="badge bg-light text-muted border">{{ count($muftis) }} Scholars Registered</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Scholar</th>
                                    <th>Madhab</th>
                                    <th>Assigned Questions</th>
                                    <th>Drafted / Answered</th>
                                    <th>Published</th>
                                    <th>Throughput</th>
                                    <th>Public Reads</th>
                                    <th>Likes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($muftis as $m)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($m->avatar)
                                                <img src="{{ asset($m->avatar) }}" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;">
                                            @else
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                                    {{ substr($m->name, 0, 2) }}
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('admin.muftis.activity', $m) }}" class="fw-bold text-dark text-decoration-none hover-primary">{{ $m->name }}</a>
                                                <div class="small text-muted">{{ $m->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $m->madhab?->name ?? 'General' }}</span></td>
                                    <td><span class="badge bg-primary-subtle text-primary fw-bold">{{ $m->stat_assigned }}</span></td>
                                    <td><span class="badge bg-info-subtle text-info fw-bold">{{ $m->stat_answered }}</span></td>
                                    <td><span class="badge bg-success-subtle text-success fw-bold">{{ $m->stat_published }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; width: 70px;">
                                                <div class="progress-bar bg-success" style="width: {{ $m->completion_rate }}%;"></div>
                                            </div>
                                            <span class="small fw-bold">{{ $m->completion_rate }}%</span>
                                        </div>
                                    </td>
                                    <td><i class="bi bi-eye text-muted me-1"></i>{{ number_format($m->stat_views) }}</td>
                                    <td><i class="bi bi-heart-fill text-danger me-1"></i>{{ number_format($m->stat_likes) }}</td>
                                    <td>
                                        <a href="{{ route('admin.muftis.activity', $m) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2">
                                            Activity
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="9" class="text-center py-4 text-muted">No mufti activity records found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            {{-- TAB 4: JURISPRUDENCE & MADHABS --}}
            @elseif($activeTab === 'madhabs')
                <div>
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-book-half me-2 text-primary"></i>Islamic Jurisprudence & Madhab Distribution</h5>
                    <div class="row g-4">
                        @foreach($madhabStats as $md)
                        @php $mdPct = $totalQuestions > 0 ? round(($md->total / $totalQuestions) * 100, 1) : 0; @endphp
                        <div class="col-md-6 col-lg-3">
                            <div class="card border rounded-4 p-3 bg-light h-100 shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="fw-bold text-dark mb-0">{{ $md->name }}</h5>
                                    <span class="badge bg-primary text-white rounded-pill">{{ $mdPct }}%</span>
                                </div>
                                <div class="display-6 fw-bold text-dark my-2">{{ number_format($md->total) }}</div>
                                <div class="small text-muted mb-3">Total Inquiries</div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top small text-muted">
                                    <span><i class="bi bi-check-circle-fill text-success me-1"></i>{{ number_format($md->published) }} Published</span>
                                    <span><i class="bi bi-eye text-info me-1"></i>{{ number_format($md->views) }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            {{-- TAB 5: CATEGORIES & TOP FATWAS --}}
            @elseif($activeTab === 'categories')
                <div class="row g-4">
                    {{-- Top Categories --}}
                    <div class="col-lg-5">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-tags me-2 text-primary"></i>Top Inquiry Categories</h5>
                        <div class="list-group list-group-flush border rounded-4 overflow-hidden">
                            @forelse($categoryStats as $cat)
                            <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                <span class="fw-bold text-dark">{{ $cat->name }}</span>
                                <span class="badge bg-primary-subtle text-primary fs-6 px-3 rounded-pill">{{ number_format($cat->questions_count) }} fatwas</span>
                            </div>
                            @empty
                            <div class="p-3 text-muted text-center">No categories found.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Top 10 Most Read Fatwas --}}
                    <div class="col-lg-7">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-trophy-fill me-2 text-warning"></i>Top 10 Most-Read Published Fatwas</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>ID & Title</th>
                                        <th>Category</th>
                                        <th>Madhab</th>
                                        <th>Views</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topFatwas as $idx => $tf)
                                    <tr>
                                        <td class="fw-bold text-muted">{{ $idx + 1 }}</td>
                                        <td>
                                            <a href="{{ route('fatwa.show', $tf) }}" target="_blank" class="fw-bold text-dark text-decoration-none hover-primary line-clamp-1">
                                                <span class="badge bg-success-subtle text-success me-1">{{ $tf->question_id }}</span>
                                                {{ Str::limit($tf->title ?: strip_tags($tf->body), 45) }}
                                            </a>
                                        </td>
                                        <td><span class="badge bg-light text-muted border">{{ $tf->category?->name ?? 'General' }}</span></td>
                                        <td><span class="badge bg-light text-primary border">{{ $tf->madhab?->name ?? 'General' }}</span></td>
                                        <td><span class="fw-bold text-info"><i class="bi bi-eye me-1"></i>{{ number_format($tf->views) }}</span></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-3 text-muted">No published fatwas found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            {{-- TAB 6: DEMOGRAPHICS, GEOGRAPHY & LANGUAGE --}}
            @elseif($activeTab === 'demographics')
                <div class="row g-4">
                    {{-- Languages --}}
                    <div class="col-md-4">
                        <div class="card border rounded-4 p-3 bg-light h-100 shadow-sm">
                            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-translate me-2 text-primary"></i>Language Distribution</h5>
                            <div class="d-flex flex-column gap-2">
                                @forelse($languageStats as $lang)
                                <div class="d-flex justify-content-between align-items-center p-2 bg-white rounded-3 border">
                                    <span class="fw-bold text-dark">{{ $lang->language ?: 'English' }}</span>
                                    <span class="badge bg-primary rounded-pill px-3">{{ number_format($lang->count) }}</span>
                                </div>
                                @empty
                                <div class="text-muted small">No language data available.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Countries --}}
                    <div class="col-md-4">
                        <div class="card border rounded-4 p-3 bg-light h-100 shadow-sm">
                            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-geo-alt-fill me-2 text-danger"></i>Top Questioner Countries</h5>
                            <div class="d-flex flex-column gap-2">
                                @forelse($countryStats as $c)
                                <div class="d-flex justify-content-between align-items-center p-2 bg-white rounded-3 border">
                                    <span class="fw-bold text-dark">{{ $c->country }}</span>
                                    <span class="badge bg-success rounded-pill px-3">{{ number_format($c->count) }}</span>
                                </div>
                                @empty
                                <div class="text-muted small">No country data available.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Gender --}}
                    <div class="col-md-4">
                        <div class="card border rounded-4 p-3 bg-light h-100 shadow-sm">
                            <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-gender-ambiguous me-2 text-info"></i>User Gender Breakdown</h5>
                            <div class="d-flex flex-column gap-2">
                                @forelse($genderStats as $g)
                                <div class="d-flex justify-content-between align-items-center p-2 bg-white rounded-3 border">
                                    <span class="fw-bold text-dark">{{ ucfirst($g->gender) }}</span>
                                    <span class="badge bg-info text-white rounded-pill px-3">{{ number_format($g->count) }}</span>
                                </div>
                                @empty
                                <div class="text-muted small">No gender data specified.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleCustomDates() {
    const val = document.getElementById('dateRangeSelect').value;
    const startCol = document.getElementById('customStartCol');
    const endCol = document.getElementById('customEndCol');
    if (val === 'custom') {
        startCol.classList.remove('d-none');
        endCol.classList.remove('d-none');
    } else {
        startCol.classList.add('d-none');
        endCol.classList.add('d-none');
    }
}
</script>
@endsection
