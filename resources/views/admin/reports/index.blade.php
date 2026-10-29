@extends('layouts.admin')
@section('title', 'Reports & Analytics Intelligence')
@section('topbar-title', 'Reports & Intelligence Center')

@section('content')
<div class="container-fluid px-0">
    {{-- Page Header & Actions --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: var(--brand-blue);">
                <i class="bi bi-file-earmark-bar-graph me-2 text-success"></i> Reports & Analytics Intelligence
            </h3>
            <p class="text-muted small mb-0">Date-filtered reporting, scholar throughput, public visitor intelligence & operational monitoring.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            {{-- Download PDF Report Button (Primary Requirement) --}}
            <a href="{{ route('admin.reports.pdf', request()->query()) }}" target="_blank" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold">
                <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF Report
            </a>
            {{-- CSV Export --}}
            <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm fw-bold">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export to CSV
            </a>
            {{-- Print Button --}}
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-lg-4">
            <form method="GET" action="{{ route('admin.reports.index') }}" id="reportFilterForm" class="row g-3 align-items-end">
                <input type="hidden" name="tab" value="{{ $activeTab }}">

                {{-- Date Preset Selector --}}
                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Reporting Period</label>
                    <select name="date_range" class="form-select form-select-sm" id="dateRangeSelect" onchange="toggleCustomDates()">
                        <option value="this_month" {{ $dateRange === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="last_30_days" {{ $dateRange === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="last_3_weeks" {{ $dateRange === 'last_3_weeks' ? 'selected' : '' }}>Last 3 Weeks</option>
                        <option value="last_2_weeks" {{ $dateRange === 'last_2_weeks' ? 'selected' : '' }}>Last 2 Weeks</option>
                        <option value="last_1_week" {{ $dateRange === 'last_1_week' ? 'selected' : '' }}>Last 1 Week</option>
                        <option value="today" {{ $dateRange === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="this_year" {{ $dateRange === 'this_year' ? 'selected' : '' }}>This Year</option>
                        <option value="all" {{ $dateRange === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="custom" {{ $dateRange === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                    </select>
                </div>

                {{-- Custom Date Inputs --}}
                <div class="col-md-2 col-sm-6 {{ $dateRange === 'custom' ? '' : 'd-none' }}" id="customStartCol">
                    <label class="form-label small fw-semibold text-muted mb-1">Start Date</label>
                    <input type="date" name="start_date" id="startDateInput" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>
                <div class="col-md-2 col-sm-6 {{ $dateRange === 'custom' ? '' : 'd-none' }}" id="customEndCol">
                    <label class="form-label small fw-semibold text-muted mb-1">End Date</label>
                    <input type="date" name="end_date" id="endDateInput" class="form-control form-control-sm" value="{{ $endDate }}">
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
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Category</label>
                    <select name="category_id" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($allCategories as $cat)
                            <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Scholar / Mufti Filter --}}
                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Scholar</label>
                    <select name="mufti_id" class="form-select form-select-sm">
                        <option value="">All Scholars</option>
                        @foreach($allMuftis as $mf)
                            <option value="{{ $mf->id }}" {{ $selectedMufti == $mf->id ? 'selected' : '' }}>{{ $mf->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="col-md-1 col-sm-12 d-flex gap-1">
                    <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill fw-bold" title="Apply Filters">
                        <i class="bi bi-funnel me-1"></i> Apply
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-light btn-sm rounded-pill border" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>

            {{-- Active Filter Period Badge --}}
            <div class="mt-3 pt-2 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="small text-muted">
                    <i class="bi bi-calendar-event me-1 text-primary"></i>
                    Active Period: <strong class="text-dark">{{ $periodLabel }}</strong>
                    @if($rangeData['start'] && $rangeData['end'])
                        ({{ $rangeData['start']->format('d M Y') }} – {{ $rangeData['end']->format('d M Y') }})
                    @endif
                    <span class="badge bg-light text-muted border ms-2">{{ $rangeData['timezone'] }}</span>
                </div>
                <div class="small text-muted">
                    <i class="bi bi-info-circle me-1 text-success"></i>
                    Fatwas Published are classified by actual publication timestamp, independently of submission date.
                </div>
            </div>
        </div>
    </div>

    {{-- 12 Executive Metric Summary Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Questions Submitted --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Questions Submitted</span>
                        <h2 class="fw-bold my-1 text-dark">{{ number_format($metrics['total_questions']) }}</h2>
                        @if($metrics['submission_change']['has_change'])
                            <span class="badge {{ $metrics['submission_change']['direction'] === 'up' ? 'bg-success-subtle text-success' : ($metrics['submission_change']['direction'] === 'down' ? 'bg-danger-subtle text-danger' : 'bg-light text-muted') }} small">
                                <i class="bi bi-arrow-{{ $metrics['submission_change']['direction'] === 'up' ? 'up' : 'down' }}-short"></i>
                                {{ $metrics['submission_change']['label'] }}
                            </span>
                        @else
                            <span class="small text-muted">Submitted in period</span>
                        @endif
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-question-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Fatwas Published During Selected Period --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Fatwas Published</span>
                        <h2 class="fw-bold my-1 text-success">{{ number_format($metrics['published_fatwas']) }}</h2>
                        <span class="badge bg-success-subtle text-success small fw-bold">
                            {{ $metrics['public_published_fatwas'] }} Public Archive Records
                        </span>
                        @if($metrics['publication_change']['has_change'])
                            <div class="small mt-1 text-muted">
                                {{ $metrics['publication_change']['label'] }}
                            </div>
                        @endif
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="bi bi-journal-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Pending Review --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Pending Review</span>
                        <h2 class="fw-bold my-1 text-warning">{{ number_format($metrics['pending_questions']) }}</h2>
                        <span class="small text-muted">Awaiting triage & scholar assignment</span>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: In Progress --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">In Progress</span>
                        <h2 class="fw-bold my-1 text-info">{{ number_format($metrics['in_progress_questions']) }}</h2>
                        <span class="small text-muted">Assigned & actively being drafted</span>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="bi bi-pencil-square fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 5: Answered Questions --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4" style="border-left-color: #0284c7 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Answered Questions</span>
                        <h2 class="fw-bold my-1" style="color: #0284c7;">{{ number_format($metrics['answered_questions']) }}</h2>
                        <span class="small text-muted">Draft completed / in verification</span>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                        <i class="bi bi-chat-left-dots-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 6: Rejected Questions --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-danger">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Rejected Questions</span>
                        <h2 class="fw-bold my-1 text-danger">{{ number_format($metrics['rejected_questions']) }}</h2>
                        <span class="small text-muted">Ineligible / non-jurisprudential</span>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle">
                        <i class="bi bi-x-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 7: Total Public Visitors / Reads --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Public Fatwa Reads</span>
                        <h2 class="fw-bold my-1 text-dark">{{ number_format($metrics['total_views']) }}</h2>
                        @if($metrics['views_change']['has_change'])
                            <span class="badge {{ $metrics['views_change']['direction'] === 'up' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} small">
                                <i class="bi bi-arrow-{{ $metrics['views_change']['direction'] === 'up' ? 'up' : 'down' }}-short"></i>
                                {{ $metrics['views_change']['label'] }}
                            </span>
                        @else
                            <span class="small text-muted">Fatwa archive page views</span>
                        @endif
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="bi bi-eye-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 8: Unique Public Visitors --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4" style="border-left-color: #059669 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Unique Public Visitors</span>
                        <h2 class="fw-bold my-1" style="color: #059669;">{{ number_format($metrics['unique_visitors']) }}</h2>
                        <span class="small text-muted">Distinct reader sessions</span>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(5, 150, 105, 0.1); color: #059669;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 9: Active Muftis --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4" style="border-left-color: #8b5cf6 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Active Muftis</span>
                        <h2 class="fw-bold my-1" style="color: #8b5cf6;">{{ $metrics['active_muftis'] }}</h2>
                        <span class="small text-muted">Scholars on verification board</span>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <i class="bi bi-person-check-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 10: Total Work Sessions --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4" style="border-left-color: #6366f1 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Work Sessions</span>
                        <h2 class="fw-bold my-1" style="color: #6366f1;">{{ number_format($metrics['total_work_sessions']) }}</h2>
                        <span class="small text-muted">Scholar authenticated sessions</span>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                        <i class="bi bi-laptop fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 11: Total Working Time --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4" style="border-left-color: #ec4899 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Total Working Time</span>
                        <h2 class="fw-bold my-1 fs-3" style="color: #ec4899;">{{ $metrics['working_time_formatted'] }}</h2>
                        <span class="small text-muted">
                            @if($metrics['working_time_available'])
                                Attributable active hours
                            @else
                                Realtime heartbeat tracking active
                            @endif
                        </span>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(236, 72, 153, 0.1); color: #ec4899;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 12: Average Question Processing Time --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4" style="border-left-color: #0f766e !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Avg Processing Time</span>
                        <h2 class="fw-bold my-1 fs-3" style="color: #0f766e;">{{ $metrics['avg_processing_label'] }}</h2>
                        <span class="small text-muted">Turnaround for completed rulings</span>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(15, 118, 110, 0.1); color: #0f766e;">
                        <i class="bi bi-stopwatch fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
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
                    <a class="nav-link px-4 py-3 fw-bold {{ $activeTab === 'scholars' ? 'active text-primary border-bottom border-3 border-primary' : 'text-muted' }}" 
                       href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'scholars'])) }}">
                        <i class="bi bi-person-badge me-1"></i> Scholar Performance Matrix
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-4 py-3 fw-bold {{ $activeTab === 'visitors' ? 'active text-primary border-bottom border-3 border-primary' : 'text-muted' }}" 
                       href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'visitors'])) }}">
                        <i class="bi bi-people me-1"></i> Public Visitor Analytics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-4 py-3 fw-bold {{ $activeTab === 'lifecycle' ? 'active text-primary border-bottom border-3 border-primary' : 'text-muted' }}" 
                       href="{{ route('admin.reports.index', array_merge(request()->query(), ['tab' => 'lifecycle'])) }}">
                        <i class="bi bi-arrow-repeat me-1"></i> Fatwa Lifecycle & Activity Trends
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
                        <i class="bi bi-tags me-1"></i> Topic Categories & Demographics
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            {{-- TAB 1: EXECUTIVE OVERVIEW --}}
            @if($activeTab === 'overview')
                <div class="row g-4">
                    {{-- Status Distribution Progress Cards & Chart --}}
                    <div class="col-lg-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-pie-chart-fill me-2 text-primary"></i>Question Status Distribution
                            </h5>
                            <span class="badge bg-light text-muted border">
                                Cohort: {{ number_format($statusDistribution['total_cohort']) }} Inquiries
                            </span>
                        </div>

                        {{-- Donut Chart Visual --}}
                        <div class="p-3 bg-light rounded-4 mb-3 border text-center" style="position: relative; height: 220px;">
                            <canvas id="statusDonutChart"></canvas>
                        </div>

                        {{-- Detailed Category Breakdown --}}
                        <div class="d-flex flex-column gap-2">
                            @foreach($statusBreakdown as $sb)
                            <div class="p-3 rounded-3 border" style="background: {{ $sb['bg'] }};">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark">
                                        <i class="bi {{ $sb['icon'] }} me-2" style="color: {{ $sb['color'] }};"></i>{{ $sb['label'] }}
                                    </span>
                                    <span class="fw-bold fs-6" style="color: {{ $sb['color'] }};">
                                        {{ number_format($sb['count']) }} <small class="text-muted">({{ $sb['pct'] }}%)</small>
                                    </span>
                                </div>
                                <div class="progress" style="height: 7px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $sb['pct'] }}%; background-color: {{ $sb['color'] }};"></div>
                                </div>
                                <div class="small text-muted mt-1" style="font-size: 11px;">{{ $sb['description'] }}</div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Explanatory Note on Classification Rules --}}
                        <div class="card border-0 bg-light p-3 rounded-4 mt-3 border">
                            <div class="small text-muted">
                                <strong class="text-dark"><i class="bi bi-shield-check me-1 text-success"></i>Date-Filtering Rule:</strong>
                                This distribution reflects the current state of questions submitted in <strong>{{ $periodLabel }}</strong>. Fatwas published during the same period (<strong>{{ number_format($metrics['published_fatwas']) }}</strong>) are tracked by their actual publication date regardless of when originally submitted.
                            </div>
                        </div>
                    </div>

                    {{-- Activity Trend & Scholar Overview --}}
                    <div class="col-lg-6">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-graph-up me-2 text-success"></i>Activity & Publication Flow
                            </h5>
                            <span class="badge bg-success-subtle text-success border border-success fw-bold">
                                {{ $metrics['published_fatwas'] }} Published in Period
                            </span>
                        </div>

                        <div class="p-3 bg-light rounded-4 mb-3 border" style="height: 220px;">
                            <canvas id="overviewTrendChart"></canvas>
                        </div>

                        {{-- Top Performing Scholars Widget --}}
                        <h6 class="fw-bold text-dark mt-4 mb-2"><i class="bi bi-award me-1 text-warning"></i>Leading Scholars in this Period</h6>
                        <div class="list-group list-group-flush border rounded-4 overflow-hidden mb-3">
                            @forelse(array_slice($muftis, 0, 4) as $m)
                            <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                        {{ mb_substr($m->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $m->name }}</div>
                                        <div class="small text-muted">{{ $m->madhab_name }} · {{ $m->login_count }} logins · {{ $m->fatwas_published }} published</div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge-pill px-2 py-1" style="background-color: {{ $m->score_color }}20; color: {{ $m->score_color }}; font-weight: 800; font-size: 12px; border-radius: 9999px;">
                                        Score: {{ $m->performance_score }}
                                    </span>
                                    <div class="small text-muted mt-1">{{ $m->score_tier }}</div>
                                </div>
                            </div>
                            @empty
                            <div class="p-3 text-muted text-center">No scholar records found.</div>
                            @endforelse
                        </div>

                        {{-- Quick Platform Health Callout --}}
                        <div class="card border-0 bg-success bg-opacity-10 p-3 rounded-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                <div>
                                    <strong class="text-success">Verified Islamic Jurisprudence Governance</strong>
                                    <div class="small text-muted">All rulings comply with authenticated Fiqh methodology and undergo multi-scholar review before public archive indexing.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            {{-- TAB 2: SCHOLAR / MUFTI PERFORMANCE MATRIX --}}
            @elseif($activeTab === 'scholars')
                <div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-person-badge me-2 text-success"></i>Mufti Performance & Workload Matrix
                            </h5>
                            <span class="small text-muted">Individual scholar contributions, throughput, processing durations, and transparent scores for {{ $periodLabel }}.</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" id="muftiTableSearch" class="form-control form-control-sm rounded-pill" placeholder="Search Scholar..." onkeyup="filterMuftiTable()" style="max-width: 220px;">
                            <span class="badge bg-light text-muted border px-2 py-1">{{ count($muftis) }} Scholars</span>
                        </div>
                    </div>

                    {{-- Scoring Methodology Banner --}}
                    <div class="alert alert-light border rounded-4 p-3 mb-3 small d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle-fill text-primary fs-5 mt-1"></i>
                        <div>
                            <strong>Transparent Performance Score Methodology:</strong>
                            Score is an objective 100-point activity and productivity index calculated from:
                            <strong>Fatwas Published (35 pts)</strong> + 
                            <strong>Answers Submitted (30 pts)</strong> + 
                            <strong>Workload Completion Rate (20 pts)</strong> + 
                            <strong>Platform Logins & Session Engagement (15 pts)</strong>.
                            It does not penalize scholars for cases awaiting admin approval.
                        </div>
                    </div>

                    <div class="table-responsive rounded-4 border">
                        <table class="table table-hover align-middle mb-0" id="muftiTable">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Mufti Name</th>
                                    <th>Madhab</th>
                                    <th class="text-center">Logins</th>
                                    <th>Last Login</th>
                                    <th class="text-center">Assigned</th>
                                    <th class="text-center">Worked On</th>
                                    <th class="text-center">Answers</th>
                                    <th class="text-center">Published</th>
                                    <th class="text-center">Pending Work</th>
                                    <th class="text-center">Working Time</th>
                                    <th class="text-center">Avg Turnaround</th>
                                    <th class="text-center">Score</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($muftis as $idx => $m)
                                <tr>
                                    <td class="text-muted fw-bold">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($m->avatar)
                                                <img src="{{ asset($m->avatar) }}" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;">
                                            @else
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                                    {{ mb_substr($m->name, 0, 2) }}
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('admin.muftis.activity', $m->id) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                                    {{ $m->name }}
                                                </a>
                                                <div class="small text-muted">{{ $m->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $m->madhab_name }}</span></td>
                                    <td class="text-center"><span class="badge bg-light text-dark border fw-bold">{{ $m->login_count }}</span></td>
                                    <td class="small text-muted">{{ $m->last_login }}</td>
                                    <td class="text-center"><span class="badge bg-primary-subtle text-primary fw-bold">{{ $m->questions_assigned }}</span></td>
                                    <td class="text-center"><span class="badge bg-secondary-subtle text-secondary fw-bold">{{ $m->questions_worked }}</span></td>
                                    <td class="text-center"><span class="badge bg-info-subtle text-info fw-bold">{{ $m->answers_submitted }}</span></td>
                                    <td class="text-center"><span class="badge bg-success-subtle text-success fw-bold">{{ $m->fatwas_published }}</span></td>
                                    <td class="text-center">
                                        @if($m->pending_work > 0)
                                            <span class="badge bg-warning-subtle text-warning fw-bold">{{ $m->pending_work }}</span>
                                        @else
                                            <span class="badge bg-light text-muted border">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center small">
                                        @if($m->working_time !== 'Not Available')
                                            <span class="badge bg-light text-dark border">{{ $m->working_time }}</span>
                                        @else
                                            <span class="text-muted" title="Realtime session duration telemetry active">Not Available</span>
                                        @endif
                                    </td>
                                    <td class="text-center small fw-semibold text-muted">{{ $m->avg_processing }}</td>
                                    <td class="text-center">
                                        <span class="badge px-2 py-1 fs-6" style="background-color: {{ $m->score_color }}20; color: {{ $m->score_color }}; font-weight: 800; border-radius: 9999px;">
                                            {{ $m->performance_score }}
                                        </span>
                                        <div class="small text-muted" style="font-size: 10px;">{{ $m->score_tier }}</div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.muftis.activity', $m->id) }}" class="btn btn-xs btn-outline-primary rounded-pill px-2" title="View Question-level Activity Log">
                                            <i class="bi bi-clock-history me-1"></i> Activity
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="14" class="text-center py-4 text-muted">No scholar records found matching filters.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            {{-- TAB 3: PUBLIC VISITOR ANALYTICS --}}
            @elseif($activeTab === 'visitors')
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-people me-2 text-primary"></i>Public Visitor Analytics & Engagement
                            </h5>
                            <span class="small text-muted">Readership trends, unique visitors, top read fatwas and audience geography for {{ $periodLabel }}.</span>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success fw-bold px-3 py-2 fs-6">
                            {{ number_format($visitorAnalytics['total_views']) }} Total Reads
                        </span>
                    </div>

                    {{-- Visitor Headline Cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary text-white p-3 rounded-3"><i class="bi bi-eye-fill fs-3"></i></div>
                                    <div>
                                        <div class="small text-muted text-uppercase fw-bold">Total Public Reads</div>
                                        <h3 class="fw-bold text-dark my-1">{{ number_format($visitorAnalytics['total_views']) }}</h3>
                                        <span class="small text-muted">Fatwa archive page views</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success text-white p-3 rounded-3"><i class="bi bi-person-check-fill fs-3"></i></div>
                                    <div>
                                        <div class="small text-muted text-uppercase fw-bold">Unique Public Visitors</div>
                                        <h3 class="fw-bold text-success my-1">{{ number_format($visitorAnalytics['unique_visitors']) }}</h3>
                                        <span class="small text-muted">Privacy-anonymized sessions</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-4 border h-100">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-info text-white p-3 rounded-3"><i class="bi bi-globe2 fs-3"></i></div>
                                    <div>
                                        <div class="small text-muted text-uppercase fw-bold">Global Jurisdictions</div>
                                        <h3 class="fw-bold text-info my-1">{{ count($visitorAnalytics['top_countries']) }}</h3>
                                        <span class="small text-muted">Active reader countries</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Daily Visitor Trend Chart --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4 border">
                        <div class="card-header bg-white border-bottom p-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-graph-up me-2 text-primary"></i>Daily Visitor & Readership Trend</h6>
                        </div>
                        <div class="card-body p-3" style="height: 260px;">
                            <canvas id="visitorTrendChart"></canvas>
                        </div>
                    </div>

                    {{-- Top Read Fatwas & Country Distribution --}}
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-trophy-fill me-2 text-warning"></i>Most-Viewed Published Fatwas During Period</h6>
                            <div class="table-responsive border rounded-4">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>ID & Ruling Title</th>
                                            <th>Category</th>
                                            <th>Madhab</th>
                                            <th class="text-end">Reads in Period</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($visitorAnalytics['top_fatwas'] as $idx => $tf)
                                        <tr>
                                            <td class="fw-bold text-muted">{{ $idx + 1 }}</td>
                                            <td>
                                                <a href="{{ route('fatwa.show', $tf->id) }}" target="_blank" class="fw-bold text-dark text-decoration-none hover-primary">
                                                    <span class="badge bg-success-subtle text-success me-1 font-monospace">{{ $tf->question_id }}</span>
                                                    {{ Str::limit($tf->title ?: strip_tags($tf->body), 50) }}
                                                </a>
                                            </td>
                                            <td><span class="badge bg-light text-muted border">{{ $tf->category?->name ?? 'General' }}</span></td>
                                            <td><span class="badge bg-light text-primary border">{{ $tf->madhab?->name ?? 'General' }}</span></td>
                                            <td class="text-end fw-bold text-info"><i class="bi bi-eye me-1"></i>{{ number_format($tf->period_views ?? $tf->views) }}</td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center py-3 text-muted">No published fatwas read in this period.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-geo-alt-fill me-2 text-danger"></i>Top Reader Countries</h6>
                            <div class="list-group list-group-flush border rounded-4 overflow-hidden">
                                @forelse($visitorAnalytics['top_countries'] as $cName => $cViews)
                                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <span class="fw-bold text-dark"><i class="bi bi-geo-alt text-danger me-2"></i>{{ $cName }}</span>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ number_format($cViews) }}</span>
                                </div>
                                @empty
                                <div class="p-3 text-muted text-center">No country view logs found.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            {{-- TAB 4: FATWA LIFECYCLE & ACTIVITY TRENDS --}}
            @elseif($activeTab === 'lifecycle')
                <div>
                    <h5 class="fw-bold mb-3 text-dark">
                        <i class="bi bi-calendar3 me-2 text-primary"></i>Publication & Submission Flow
                    </h5>

                    <div class="card border-0 shadow-sm rounded-4 mb-4 border">
                        <div class="card-header bg-white border-bottom p-3">
                            <h6 class="fw-bold mb-0 text-dark">Questions Inflow vs Fatwas Published Over Time</h6>
                        </div>
                        <div class="card-body p-3" style="height: 280px;">
                            <canvas id="lifecycleChart"></canvas>
                        </div>
                    </div>

                    @if(!empty($lifecycleTrends['monthly_table']))
                    <div class="table-responsive rounded-4 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Month / Year</th>
                                    <th>Questions Inflow</th>
                                    <th>Fatwas Published</th>
                                    <th>Publication Ratio</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lifecycleTrends['monthly_table'] as $mt)
                                @php $rate = $mt->total > 0 ? round(($mt->published / $mt->total) * 100, 1) : 0; @endphp
                                <tr>
                                    <td class="fw-bold text-dark">{{ $mt->month_year }}</td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1 fs-6">{{ number_format($mt->total) }}</span></td>
                                    <td><span class="badge bg-success-subtle text-success border border-success px-2 py-1 fs-6">{{ number_format($mt->published) }}</span></td>
                                    <td class="fw-bold {{ $rate >= 60 ? 'text-success' : ($rate >= 30 ? 'text-warning' : 'text-danger') }}">{{ $rate }}%</td>
                                    <td style="width: 25%;">
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: {{ min(100, $rate) }}%;"></div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

            {{-- TAB 5: JURISPRUDENCE & MADHABS --}}
            @elseif($activeTab === 'madhabs')
                <div>
                    <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-book-half me-2 text-primary"></i>Islamic Jurisprudence & Madhab Distribution</h5>
                    <div class="row g-4">
                        @foreach($madhabStats as $md)
                        @php $mdPct = $metrics['total_questions'] > 0 ? round(($md->total / $metrics['total_questions']) * 100, 1) : 0; @endphp
                        <div class="col-md-6 col-lg-3">
                            <div class="card border rounded-4 p-3 bg-light h-100 shadow-sm">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="fw-bold text-dark mb-0">{{ $md->name }}</h5>
                                    <span class="badge bg-primary text-white rounded-pill">{{ $mdPct }}%</span>
                                </div>
                                <div class="display-6 fw-bold text-dark my-2">{{ number_format($md->total) }}</div>
                                <div class="small text-muted mb-3">Inquiries Submitted</div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top small text-muted">
                                    <span><i class="bi bi-check-circle-fill text-success me-1"></i>{{ number_format($md->published) }} Published</span>
                                    <span><i class="bi bi-eye text-info me-1"></i>{{ number_format($md->views) }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            {{-- TAB 6: CATEGORIES & DEMOGRAPHICS --}}
            @elseif($activeTab === 'categories')
                <div class="row g-4">
                    {{-- Top Categories --}}
                    <div class="col-lg-6">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-tags me-2 text-primary"></i>Top Inquiry Categories</h5>
                        <div class="list-group list-group-flush border rounded-4 overflow-hidden">
                            @forelse($categoryStats as $cat)
                            <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                <span class="fw-bold text-dark">{{ $cat->name }}</span>
                                <span class="badge bg-primary-subtle text-primary fs-6 px-3 rounded-pill">{{ number_format($cat->questions_count) }} Inquiries</span>
                            </div>
                            @empty
                            <div class="p-3 text-muted text-center">No categories found.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Demographics & Geography --}}
                    <div class="col-lg-6">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-globe me-2 text-primary"></i>Language & Registered Users</h5>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="card border rounded-4 p-3 bg-light h-100 shadow-sm">
                                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-translate me-2 text-warning"></i>Inquiry Languages</h6>
                                    <div class="d-flex flex-column gap-2">
                                        @forelse($languageStats as $lang)
                                        <div class="d-flex justify-content-between align-items-center p-2 bg-white rounded-3 border">
                                            <span class="fw-semibold text-dark">{{ $lang->language ?: 'English' }}</span>
                                            <span class="badge bg-primary rounded-pill">{{ number_format($lang->count) }}</span>
                                        </div>
                                        @empty
                                        <div class="text-muted small">No language data recorded.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card border rounded-4 p-3 bg-light h-100 shadow-sm">
                                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-geo-alt-fill me-2 text-danger"></i>User Countries</h6>
                                    <div class="d-flex flex-column gap-2">
                                        @forelse($countryStats as $c)
                                        <div class="d-flex justify-content-between align-items-center p-2 bg-white rounded-3 border">
                                            <span class="fw-semibold text-dark">{{ $c->country }}</span>
                                            <span class="badge bg-success rounded-pill">{{ number_format($c->count) }}</span>
                                        </div>
                                        @empty
                                        <div class="text-muted small">No country data recorded.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Scripts for dynamic date ranges, charts, and table filter --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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

function filterMuftiTable() {
    const input = document.getElementById('muftiTableSearch');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('muftiTable');
    if (!table) return;
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        const text = tr[i].innerText.toLowerCase();
        tr[i].style.display = text.includes(filter) ? '' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // 1. Status Donut Chart (Overview Tab)
    const donutEl = document.getElementById('statusDonutChart');
    if (donutEl) {
        new Chart(donutEl.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: @json(array_column($statusBreakdown, 'label')),
                datasets: [{
                    data: @json(array_column($statusBreakdown, 'count')),
                    backgroundColor: @json(array_column($statusBreakdown, 'color')),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    }
                },
                cutout: '65%'
            }
        });
    }

    // 2. Overview Lifecycle Trend Chart (Overview Tab)
    const overviewEl = document.getElementById('overviewTrendChart');
    if (overviewEl) {
        new Chart(overviewEl.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($lifecycleTrends['labels']),
                datasets: [
                    {
                        label: 'Submitted',
                        data: @json($lifecycleTrends['submitted_counts']),
                        backgroundColor: 'rgba(37, 99, 235, 0.75)',
                        borderRadius: 4
                    },
                    {
                        label: 'Published',
                        data: @json($lifecycleTrends['published_counts']),
                        backgroundColor: 'rgba(22, 163, 74, 0.85)',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    // 3. Visitor Trend Chart (Visitors Tab)
    const visitorEl = document.getElementById('visitorTrendChart');
    if (visitorEl) {
        const vLabels = @json(array_column($visitorAnalytics['daily_trends'], 'label'));
        const vCounts = @json(array_column($visitorAnalytics['daily_trends'], 'views'));
        new Chart(visitorEl.getContext('2d'), {
            type: 'line',
            data: {
                labels: vLabels,
                datasets: [{
                    label: 'Daily Public Reads',
                    data: vCounts,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.12)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointRadius: 2,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    // 4. Lifecycle Chart (Lifecycle Tab)
    const lifecycleEl = document.getElementById('lifecycleChart');
    if (lifecycleEl) {
        new Chart(lifecycleEl.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($lifecycleTrends['labels']),
                datasets: [
                    {
                        label: 'Questions Submitted',
                        data: @json($lifecycleTrends['submitted_counts']),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2
                    },
                    {
                        label: 'Fatwas Published',
                        data: @json($lifecycleTrends['published_counts']),
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.1)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
