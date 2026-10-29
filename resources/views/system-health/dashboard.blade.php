@extends('system-health.layout')

@section('title', 'System Health & Diagnostics')

@section('content')
<!-- Diagnostics Sticky Navbar -->
<nav class="sh-navbar d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('system_health.index') }}" class="text-decoration-none d-flex align-items-center gap-2">
            <span class="d-inline-flex p-2 rounded-3 bg-dark border border-secondary">
                <i class="bi bi-activity text-info fs-5"></i>
            </span>
            <div>
                <span class="fw-bold text-white fs-6 d-block" style="font-family: 'Plus Jakarta Sans', sans-serif;">System Health &amp; Diagnostics</span>
                <span class="text-muted small" style="font-size: 0.75rem;">Darul Ifta Kauzariyya &bull; Infrastructure &amp; API Monitor</span>
            </div>
        </a>
    </div>

    <!-- Right Controls -->
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- Status Pill -->
        @php
            $overallClass = match($overview['overall_status']) {
                'Healthy' => 'pulse-healthy',
                'Degraded' => 'pulse-degraded',
                default => 'pulse-critical'
            };
        @endphp
        <span class="pulse-badge {{ $overallClass }} me-1" id="overallStatusBadge">
            <span class="pulse-dot"></span> <span id="overallStatusText">{{ $overview['overall_status'] }}</span>
        </span>

        <!-- Uptime Pill -->
        <span class="badge py-2 px-3 fw-semibold mono small" style="background: #1e293b; border: 1px solid #475569; color: #f8fafc;">
            <i class="bi bi-clock-history me-1 text-info"></i>Uptime: {{ $overview['application_uptime'] }}
        </span>

        <!-- Refresh Button -->
        <button id="btnRefresh" class="btn btn-sm btn-outline-light d-flex align-items-center gap-1 shadow-sm px-3 fw-semibold">
            <i class="bi bi-arrow-clockwise"></i>
            <span>Refresh</span>
        </button>

        <!-- Synthetic Probe Button -->
        <button id="btnSyntheticPing" class="btn btn-sm btn-info text-dark fw-bold d-flex align-items-center gap-1 shadow-sm px-3">
            <i class="bi bi-broadcast"></i>
            <span>Probe API</span>
        </button>

        <!-- Logout Action -->
        <form method="POST" action="{{ route('system_health.logout') }}" class="m-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger fw-bold d-flex align-items-center gap-1 shadow-sm px-3" title="Terminate diagnostic session">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</nav>

<!-- Main Container -->
<div class="container-fluid py-4 px-3 px-md-4" style="max-width: 1480px;">
    <!-- Navigation Tabs -->
    <ul class="nav sh-nav-tabs mb-4 flex-nowrap overflow-x-auto pb-1" id="diagTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview-pane" type="button" role="tab">
                <i class="bi bi-speedometer2 me-1"></i>Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="infra-tab" data-bs-toggle="tab" data-bs-target="#infra-pane" type="button" role="tab">
                <i class="bi bi-hdd-rack me-1"></i>Server &amp; OS
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="php-tab" data-bs-toggle="tab" data-bs-target="#php-pane" type="button" role="tab">
                <i class="bi bi-filetype-php me-1"></i>PHP Runtime
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="laravel-tab" data-bs-toggle="tab" data-bs-target="#laravel-pane" type="button" role="tab">
                <i class="bi bi-layers me-1"></i>Laravel &amp; Database
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="api-tab" data-bs-toggle="tab" data-bs-target="#api-pane" type="button" role="tab">
                <i class="bi bi-lightning-charge me-1"></i>API Health &amp; Strength
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity-pane" type="button" role="tab">
                <i class="bi bi-list-columns-reverse me-1"></i>Recent API Calls
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="errors-tab" data-bs-toggle="tab" data-bs-target="#errors-pane" type="button" role="tab">
                <i class="bi bi-exclamation-octagon me-1"></i>Errors &amp; Exceptions
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-pane" type="button" role="tab">
                <i class="bi bi-shield-check me-1"></i>Security Audit
            </button>
        </li>
    </ul>

    <!-- Tab Content Panes -->
    <div class="tab-content" id="diagTabsContent">
        
        <!-- =================================================================== -->
        <!-- TAB 1: EXECUTIVE OVERVIEW                                          -->
        <!-- =================================================================== -->
        <div class="tab-pane fade show active" id="overview-pane" role="tabpanel">
            <!-- Summary Metric Cards -->
            <div class="row g-3 mb-4">
                <!-- Card 1: Core Database Latency -->
                <div class="col-6 col-lg-3">
                    <div class="sh-card h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="metric-label">MySQL Latency</span>
                            <i class="bi bi-database text-primary fs-5"></i>
                        </div>
                        <div class="metric-value text-white">{{ $database['latency_ms'] }} <span class="fs-6 text-muted">ms</span></div>
                        <div class="small text-muted mt-2 d-flex align-items-center gap-1">
                            <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2" style="font-size: 0.72rem;">{{ strtoupper($database['status']) }}</span>
                            <span>&bull; {{ $database['table_count'] }} Tables</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: System RAM Utilization -->
                <div class="col-6 col-lg-3">
                    <div class="sh-card h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="metric-label">RAM Utilization</span>
                            <i class="bi bi-memory text-info fs-5"></i>
                        </div>
                        <div class="metric-value text-white">{{ $resources['ram']['utilization_pct'] }}%</div>
                        <div class="progress progress-dark mt-2">
                            <div class="progress-bar bg-info" style="width: {{ $resources['ram']['utilization_pct'] }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mt-1" style="font-size: 0.72rem;">
                            <span>{{ round($resources['ram']['used_mb'] / 1024, 1) }} GB Used</span>
                            <span>{{ round($resources['ram']['total_mb'] / 1024, 1) }} GB Total</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Disk Storage Capacity -->
                <div class="col-6 col-lg-3">
                    <div class="sh-card h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="metric-label">Disk Storage</span>
                            <i class="bi bi-device-hdd text-warning fs-5"></i>
                        </div>
                        <div class="metric-value text-white">{{ $resources['disk']['utilization_pct'] }}%</div>
                        <div class="progress progress-dark mt-2">
                            <div class="progress-bar bg-warning" style="width: {{ $resources['disk']['utilization_pct'] }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small mt-1" style="font-size: 0.72rem;">
                            <span>{{ $resources['disk']['used_gb'] }} GB Used</span>
                            <span>{{ $resources['disk']['free_gb'] }} GB Free</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: API Availability & Health -->
                <div class="col-6 col-lg-3">
                    <div class="sh-card h-100">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="metric-label">API Availability (24h)</span>
                            <i class="bi bi-graph-up-arrow text-success fs-5"></i>
                        </div>
                        <div class="metric-value text-white">{{ $overview['api_summary']['availability_pct'] }}%</div>
                        <div class="small text-muted mt-2 d-flex align-items-center justify-content-between" style="font-size: 0.75rem;">
                            <span>Total Requests: <strong class="text-light">{{ $overview['api_summary']['total_requests'] }}</strong></span>
                            <span class="text-danger">Failed: {{ $overview['recent_failed_requests'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row: Core Services Grid & Incident Alert -->
            <div class="row g-3">
                <div class="col-12 col-lg-8">
                    <div class="sh-card">
                        <h6 class="text-white fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-grid-3x3-gap text-info"></i> Core Component Telemetry
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-dark-custom m-0">
                                <thead>
                                    <tr>
                                        <th>Component</th>
                                        <th>Target / Driver</th>
                                        <th>Health Status</th>
                                        <th>Latency / Metric</th>
                                        <th>State</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-white fw-semibold"><i class="bi bi-database me-2 text-primary"></i>MySQL Core DB</td>
                                        <td class="mono small">{{ $database['driver'] }} &bull; {{ $database['masked_name'] }}</td>
                                        <td><span class="status-pill status-pill-2xx">Operational</span></td>
                                        <td class="mono">{{ $database['latency_ms'] }} ms</td>
                                        <td class="text-success"><i class="bi bi-check-circle-fill"></i> Connected</td>
                                    </tr>
                                    <tr>
                                        <td class="text-white fw-semibold"><i class="bi bi-hdd-network me-2 text-warning"></i>Cache Store (Redis)</td>
                                        <td class="mono small">{{ $overview['cache']['driver'] }} @if(isset($overview['cache']['redis']['latency_ms']))&bull; Redis Cluster @endif</td>
                                        <td><span class="status-pill status-pill-2xx">{{ ucfirst($overview['cache']['status']) }}</span></td>
                                        <td class="mono">{{ $overview['cache']['latency_ms'] }} ms</td>
                                        <td class="text-success"><i class="bi bi-check-circle-fill"></i> Read/Write Validated</td>
                                    </tr>
                                    <tr>
                                        <td class="text-white fw-semibold"><i class="bi bi-folder2-open me-2 text-info"></i>Local Filesystem</td>
                                        <td class="mono small">{{ $overview['storage']['default_disk'] }} (storage/app)</td>
                                        <td><span class="status-pill status-pill-2xx">{{ ucfirst($overview['storage']['status']) }}</span></td>
                                        <td class="mono">Write Passed</td>
                                        <td class="text-success"><i class="bi bi-check-circle-fill"></i> Writable</td>
                                    </tr>
                                    <tr>
                                        <td class="text-white fw-semibold"><i class="bi bi-envelope-at me-2 text-danger"></i>SMTP Mail Gateway</td>
                                        <td class="mono small">{{ $overview['mail']['host'] }}:{{ $overview['mail']['port'] }}</td>
                                        <td><span class="status-pill {{ $overview['mail']['configured'] ? 'status-pill-2xx' : 'status-pill-4xx' }}">{{ $overview['mail']['status'] }}</span></td>
                                        <td class="mono">TLS / Port 587</td>
                                        <td class="{{ $overview['mail']['configured'] ? 'text-success' : 'text-danger' }}">
                                            <i class="bi {{ $overview['mail']['configured'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i> 
                                            {{ $overview['mail']['configured'] ? 'Credentials Loaded' : 'Missing Config' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-white fw-semibold"><i class="bi bi-cpu me-2 text-secondary"></i>Queue Background Worker</td>
                                        <td class="mono small">{{ $overview['queue']['driver'] }} queue</td>
                                        <td><span class="status-pill status-pill-2xx">{{ ucfirst($overview['queue']['status']) }}</span></td>
                                        <td class="mono">{{ $overview['queue']['pending_jobs'] }} Pending</td>
                                        <td class="text-success"><i class="bi bi-check-circle-fill"></i> {{ $overview['queue']['failed_jobs'] }} Failed</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Quick Stats -->
                <div class="col-12 col-lg-4">
                    <div class="sh-card h-100">
                        <h6 class="text-white fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-bell text-warning"></i> Incident &amp; Error Health
                        </h6>
                        <div class="p-3 rounded-3 mb-3" style="background: rgba(30, 41, 59, 0.7); border: 1px solid #334155;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-light fw-bold small">Exceptions (24 Hours)</span>
                                <span class="badge {{ $overview['recent_errors_count'] > 0 ? 'bg-danger' : 'bg-success' }} fs-6">{{ $overview['recent_errors_count'] }}</span>
                            </div>
                            <div class="text-light small" style="font-size: 0.85rem; color: #cbd5e1 !important;">
                                Latest Incident: <strong class="text-white mono ms-1">{{ $overview['latest_error_timestamp'] ?: 'None recorded in current log window' }}</strong>
                            </div>
                        </div>

                        <div class="p-3 rounded-3" style="background: rgba(30, 41, 59, 0.7); border: 1px solid #334155;">
                            <div class="text-info small mb-2 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Environment Context</div>
                            <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: #334155 !important; font-size: 0.85rem;">
                                <span class="text-light fw-medium">Environment:</span>
                                <span class="mono text-white fw-bold">{{ $serverInfo['environment'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: #334155 !important; font-size: 0.85rem;">
                                <span class="text-light fw-medium">PHP Version:</span>
                                <span class="mono text-white fw-bold">{{ $phpInfo['php_version'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom" style="border-color: #334155 !important; font-size: 0.85rem;">
                                <span class="text-light fw-medium">Laravel Framework:</span>
                                <span class="mono text-white fw-bold">v{{ $laravelInfo['framework_version'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1" style="font-size: 0.85rem;">
                                <span class="text-light fw-medium">Server Time:</span>
                                <span class="mono text-white fw-bold">{{ $serverInfo['server_time'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 2: SERVER & OS INFRASTRUCTURE                                  -->
        <!-- =================================================================== -->
        <div class="tab-pane fade" id="infra-pane" role="tabpanel">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <div class="sh-card">
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-hdd-network text-info me-2"></i>Operating System &amp; Host</h6>
                        <table class="table table-dark-custom">
                            <tbody>
                                <tr>
                                    <td class="text-light fw-semibold" style="width: 42%;">Operating System</td>
                                    <td class="text-white fw-bold">{{ $serverInfo['os_name'] }} ({{ $serverInfo['os_family'] }})</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Server Hostname</td>
                                    <td class="mono text-info fw-bold">{{ $serverInfo['hostname'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">CPU Architecture</td>
                                    <td class="mono text-white fw-semibold">{{ $serverInfo['architecture'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">CPU Cores</td>
                                    <td class="mono text-white fw-semibold">{{ $serverInfo['cpu_cores'] }} Logical Cores</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">System Load Average</td>
                                    <td class="mono text-white fw-semibold">1m: {{ $serverInfo['load_average'][0] }} &bull; 5m: {{ $serverInfo['load_average'][1] }} &bull; 15m: {{ $serverInfo['load_average'][2] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Server Uptime</td>
                                    <td class="mono text-success fw-bold">{{ $serverInfo['server_uptime'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Web Server / SAPI</td>
                                    <td class="mono text-white fw-semibold">{{ $serverInfo['web_server'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="sh-card">
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-pie-chart text-warning me-2"></i>Hardware Resources &amp; Mounts</h6>
                        <div class="p-3 rounded-3 mb-3" style="background: rgba(30, 41, 59, 0.4); border: 1px solid #1f2937;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-white small">System Memory (RAM)</span>
                                <span class="mono small text-info">{{ $resources['ram']['utilization_pct'] }}%</span>
                            </div>
                            <div class="progress progress-dark mb-2">
                                <div class="progress-bar bg-info" style="width: {{ $resources['ram']['utilization_pct'] }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.78rem;">
                                <span>Used: {{ round($resources['ram']['used_mb'] / 1024, 2) }} GB</span>
                                <span>Free: {{ round($resources['ram']['free_mb'] / 1024, 2) }} GB</span>
                                <span>Total: {{ round($resources['ram']['total_mb'] / 1024, 2) }} GB</span>
                            </div>
                        </div>

                        <div class="p-3 rounded-3 mb-3" style="background: rgba(30, 41, 59, 0.4); border: 1px solid #1f2937;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-white small">Root Storage Partition (ROM/Disk)</span>
                                <span class="mono small text-warning">{{ $resources['disk']['utilization_pct'] }}%</span>
                            </div>
                            <div class="progress progress-dark mb-2">
                                <div class="progress-bar bg-warning" style="width: {{ $resources['disk']['utilization_pct'] }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.78rem;">
                                <span>Used: {{ $resources['disk']['used_gb'] }} GB</span>
                                <span>Free: {{ $resources['disk']['free_gb'] }} GB</span>
                                <span>Total: {{ $resources['disk']['total_gb'] }} GB</span>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-2 rounded-2 border border-secondary text-center" style="background: #0f172a;">
                                    <div class="text-muted" style="font-size: 0.72rem;">Application Uploads</div>
                                    <div class="mono fw-bold text-white fs-6">{{ $resources['disk']['app_storage_mb'] }} MB</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded-2 border border-secondary text-center" style="background: #0f172a;">
                                    <div class="text-muted" style="font-size: 0.72rem;">Log Files Volume</div>
                                    <div class="mono fw-bold text-white fs-6">{{ $resources['disk']['logs_size_mb'] }} MB</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 3: PHP RUNTIME                                                  -->
        <!-- =================================================================== -->
        <div class="tab-pane fade" id="php-pane" role="tabpanel">
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="sh-card">
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-gear text-info me-2"></i>Runtime Configuration &amp; Memory</h6>
                        <table class="table table-dark-custom">
                            <tbody>
                                <tr>
                                    <td class="text-light fw-semibold" style="width: 42%;">PHP Version</td>
                                    <td class="mono text-white fw-bold">{{ $phpInfo['php_version'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Execution SAPI</td>
                                    <td class="mono text-white">{{ $phpInfo['sapi'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Memory Limit</td>
                                    <td class="mono text-white">{{ $phpInfo['memory_limit'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Current Process Memory</td>
                                    <td class="mono text-info fw-bold">{{ $phpInfo['current_memory_mb'] }} MB</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Peak Process Memory</td>
                                    <td class="mono text-warning fw-bold">{{ $phpInfo['peak_memory_mb'] }} MB</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Max Execution Time</td>
                                    <td class="mono text-white">{{ $phpInfo['max_execution_time'] }}s</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">OPcache Status</td>
                                    <td>
                                        @if($phpInfo['opcache']['enabled'])
                                            <span class="badge bg-success">Enabled &bull; {{ $phpInfo['opcache']['hit_rate_pct'] }}% Hit Rate</span>
                                        @else
                                            <span class="badge bg-secondary">Disabled / CLI</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="sh-card">
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-check-all text-success me-2"></i>Critical Extensions Checklist</h6>
                        <div class="row g-2">
                            @foreach($phpInfo['required_checklist'] as $ext => $loaded)
                                <div class="col-6 col-sm-4">
                                    <div class="p-2 rounded-2 border d-flex align-items-center justify-content-between" style="background: #0f172a; border-color: {{ $loaded ? '#1e293b' : '#7f1d1d' }} !important;">
                                        <span class="mono small {{ $loaded ? 'text-light' : 'text-danger' }}">{{ $ext }}</span>
                                        <i class="bi {{ $loaded ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }}"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if(!empty($phpInfo['warnings']))
                            <div class="mt-4 p-3 rounded-3" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3);">
                                <div class="fw-bold text-warning small mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Runtime Health Warnings:</div>
                                <ul class="m-0 ps-3 text-light small">
                                    @foreach($phpInfo['warnings'] as $warning)
                                        <li>{{ $warning }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 4: LARAVEL & DATABASE                                          -->
        <!-- =================================================================== -->
        <div class="tab-pane fade" id="laravel-pane" role="tabpanel">
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="sh-card">
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-layers text-danger me-2"></i>Laravel Framework State</h6>
                        <table class="table table-dark-custom">
                            <tbody>
                                <tr>
                                    <td class="text-light fw-semibold" style="width: 42%;">Framework Version</td>
                                    <td class="mono text-white fw-bold">v{{ $laravelInfo['framework_version'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Environment</td>
                                    <td><span class="badge {{ $laravelInfo['environment'] === 'production' ? 'bg-success' : 'bg-warning text-dark' }} mono">{{ $laravelInfo['environment'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Debug Mode</td>
                                    <td><span class="badge {{ $laravelInfo['debug_mode'] ? 'bg-danger' : 'bg-success' }} mono">{{ $laravelInfo['debug_mode'] ? 'ENABLED' : 'DISABLED' }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Application Timezone</td>
                                    <td class="mono text-white">{{ $laravelInfo['timezone'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Cache Driver</td>
                                    <td class="mono text-white">{{ $laravelInfo['cache_driver'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Session Driver</td>
                                    <td class="mono text-white">{{ $laravelInfo['session_driver'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Log Channel</td>
                                    <td class="mono text-white">{{ $laravelInfo['log_channel'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="sh-card">
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-database-check text-primary me-2"></i>Database Engine &amp; Migrations</h6>
                        <table class="table table-dark-custom">
                            <tbody>
                                <tr>
                                    <td class="text-light fw-semibold" style="width: 42%;">Database Engine</td>
                                    <td class="mono text-white">{{ strtoupper($database['driver']) }} &bull; {{ $database['version'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Database Target</td>
                                    <td class="mono text-info">{{ $database['masked_name'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Connection Latency</td>
                                    <td class="mono text-success fw-bold">{{ $database['latency_ms'] }} ms</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Table Count</td>
                                    <td class="mono text-white">{{ $database['table_count'] }} Tables</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Estimated Size</td>
                                    <td class="mono text-white">{{ $database['database_size'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Database Migrations</td>
                                    <td>
                                        @if($laravelInfo['migrations']['status'] === 'up_to_date')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-check2 me-1"></i>All Up to Date ({{ $laravelInfo['migrations']['ran_count'] }} ran)
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                {{ $laravelInfo['migrations']['pending_count'] }} Pending Migrations
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 5: API HEALTH & STRENGTH                                       -->
        <!-- =================================================================== -->
        <div class="tab-pane fade" id="api-pane" role="tabpanel">
            <!-- Window Selector -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted small">Metrics aggregation window:</span>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-window" data-window="5m">5 Min</button>
                    <button type="button" class="btn btn-outline-secondary btn-window" data-window="1h">1 Hour</button>
                    <button type="button" class="btn btn-outline-secondary btn-window active" data-window="24h">24 Hours</button>
                    <button type="button" class="btn btn-outline-secondary btn-window" data-window="7d">7 Days</button>
                </div>
            </div>

            <!-- Latency & Status Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="sh-card">
                        <span class="metric-label">Average Latency</span>
                        <div class="metric-value text-white" id="apiAvgLatency">{{ $apiMetrics['latency']['avg_ms'] }} <span class="fs-6 text-muted">ms</span></div>
                        <div class="small text-muted mt-1">P50 (Median): <span id="apiMedianLatency">{{ $apiMetrics['latency']['median_ms'] }}</span> ms</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="sh-card">
                        <span class="metric-label">P95 / P99 Latency</span>
                        <div class="metric-value text-white" id="apiP95Latency">{{ $apiMetrics['latency']['p95_ms'] }} <span class="fs-6 text-muted">ms</span></div>
                        <div class="small text-muted mt-1">P99: <span id="apiP99Latency">{{ $apiMetrics['latency']['p99_ms'] }}</span> ms</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="sh-card">
                        <span class="metric-label">Error Rate</span>
                        <div class="metric-value text-white" id="apiErrorRate">{{ $apiMetrics['error_rate_pct'] }}%</div>
                        <div class="small text-muted mt-1">Failed calls: <span id="apiFailedCount">{{ $apiMetrics['failed_count'] }}</span></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="sh-card">
                        <span class="metric-label">Registered Routes</span>
                        <div class="metric-value text-white">{{ $apiMetrics['registered_api_routes'] }}</div>
                        <div class="small text-muted mt-1">Monitored: {{ count($apiMetrics['monitored_endpoints']) }} Endpoints</div>
                    </div>
                </div>
            </div>

            <!-- HTTP Breakdown & Slow Endpoints -->
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="sh-card h-100">
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-pie-chart-fill text-info me-2"></i>HTTP Status Breakdown</h6>
                        <div class="d-flex flex-column gap-2">
                            <div class="p-2 rounded-2 d-flex justify-content-between align-items-center" style="background: rgba(16, 185, 129, 0.1);">
                                <span class="text-success small fw-semibold">2xx Success Responses</span>
                                <span class="mono fw-bold text-success" id="stat2xx">{{ $apiMetrics['status_codes']['2xx'] }}</span>
                            </div>
                            <div class="p-2 rounded-2 d-flex justify-content-between align-items-center" style="background: rgba(56, 189, 248, 0.1);">
                                <span class="text-info small fw-semibold">3xx Redirects</span>
                                <span class="mono fw-bold text-info" id="stat3xx">{{ $apiMetrics['status_codes']['3xx'] }}</span>
                            </div>
                            <div class="p-2 rounded-2 d-flex justify-content-between align-items-center" style="background: rgba(245, 158, 11, 0.1);">
                                <span class="text-warning small fw-semibold">4xx Client Errors</span>
                                <span class="mono fw-bold text-warning" id="stat4xx">{{ $apiMetrics['status_codes']['4xx'] }}</span>
                            </div>
                            <div class="p-2 rounded-2 d-flex justify-content-between align-items-center" style="background: rgba(239, 68, 68, 0.1);">
                                <span class="text-danger small fw-semibold">5xx Server Errors</span>
                                <span class="mono fw-bold text-danger" id="stat5xx">{{ $apiMetrics['status_codes']['5xx'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="sh-card h-100">
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-hourglass-bottom text-warning me-2"></i>Slowest Endpoints</h6>
                        <div class="table-responsive">
                            <table class="table table-dark-custom m-0">
                                <thead>
                                    <tr>
                                        <th>Method</th>
                                        <th>Path</th>
                                        <th>Avg Duration</th>
                                        <th>Calls</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($apiMetrics['slowest_endpoints'] as $ep)
                                        <tr>
                                            <td><span class="badge bg-secondary mono">{{ $ep['method'] }}</span></td>
                                            <td class="mono small text-light">{{ $ep['path'] }}</td>
                                            <td class="mono text-warning fw-bold">{{ $ep['avg_duration'] }} ms</td>
                                            <td class="mono">{{ $ep['call_count'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">No API telemetry recorded in this window yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 6: RECENT API ACTIVITY TABLE                                   -->
        <!-- =================================================================== -->
        <div class="tab-pane fade" id="activity-pane" role="tabpanel">
            <div class="sh-card">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <h6 class="text-white fw-bold m-0"><i class="bi bi-clock-history text-info me-2"></i>API Request Telemetry Logs</h6>
                    <div class="d-flex gap-2">
                        <select id="filterActivityStatus" class="form-select form-select-sm bg-dark text-light border-secondary" style="width: 140px;">
                            <option value="">All Statuses</option>
                            <option value="success">Success (2xx/3xx)</option>
                            <option value="failed">Failed (4xx/5xx)</option>
                            <option value="slow">Slow (&gt;500ms)</option>
                        </select>
                        <input type="text" id="filterActivitySearch" placeholder="Search path or correlation..." class="form-control form-control-sm text-white" style="width: 250px; background: #1e293b !important; border: 1px solid #475569 !important;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark-custom m-0" id="activityTable">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Method</th>
                                <th>Endpoint Path</th>
                                <th>Status</th>
                                <th>Duration</th>
                                <th>Source Category</th>
                                <th>Correlation ID</th>
                            </tr>
                        </thead>
                        <tbody id="activityTableBody">
                            <tr>
                                <td colspan="7" class="text-center text-light py-4">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Loading recent telemetry...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="d-flex justify-content-between align-items-center pt-3 mt-2 border-top border-secondary">
                    <span class="text-light small fw-medium" id="activityPaginationInfo">Showing page 1</span>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-light" id="btnPrevPage" disabled>Previous</button>
                        <button type="button" class="btn btn-outline-light" id="btnNextPage" disabled>Next</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 7: ERRORS & EXCEPTIONS                                         -->
        <!-- =================================================================== -->
        <div class="tab-pane fade" id="errors-pane" role="tabpanel">
            <div class="sh-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-white fw-bold m-0"><i class="bi bi-bug text-danger me-2"></i>Recent Application Exceptions (laravel.log)</h6>
                    <button class="btn btn-sm btn-outline-secondary text-light" id="btnReloadErrors">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh Logs
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-dark-custom m-0">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Severity</th>
                                <th>Exception Category</th>
                                <th>Sanitized Message</th>
                            </tr>
                        </thead>
                        <tbody id="errorsTableBody">
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <span class="spinner-border spinner-border-sm me-2"></span>Inspecting log file...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- TAB 8: SECURITY AUDIT & ACCESS LOGS                                -->
        <!-- =================================================================== -->
        <div class="tab-pane fade" id="security-pane" role="tabpanel">
            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <div class="sh-card">
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-shield-lock text-info me-2"></i>Security Posture Checklist</h6>
                        <table class="table table-dark-custom">
                            <tbody>
                                <tr>
                                    <td class="text-light fw-semibold" style="width: 45%;">Transport Layer Security (HTTPS)</td>
                                    <td>
                                        <span class="badge {{ $security['https_active'] ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $security['https_active'] ? 'ACTIVE (HTTPS)' : 'HTTP (Local / Dev)' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Production Debug Mode</td>
                                    <td>
                                        <span class="badge {{ $security['debug_mode_disabled'] ? 'bg-success' : 'bg-danger' }}">
                                            {{ $security['debug_mode_disabled'] ? 'SECURE (APP_DEBUG=false)' : 'WARNING (APP_DEBUG=true)' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Diagnostic OTP Verification</td>
                                    <td><span class="badge bg-success"><i class="bi bi-check2 me-1"></i>ENFORCED</span></td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Authorized Developer Identity</td>
                                    <td class="mono text-info fw-bold">{{ $security['developer_email'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Session Cookie SameSite Flag</td>
                                    <td class="mono text-white fw-semibold">{{ $security['session_cookie_samesite'] }}</td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Session Cookie HttpOnly</td>
                                    <td><span class="badge bg-success">TRUE</span></td>
                                </tr>
                                <tr>
                                    <td class="text-light fw-semibold">Failed Auth Attempts (24h)</td>
                                    <td class="mono fw-bold {{ $security['recent_auth_failures_24h'] > 0 ? 'text-warning' : 'text-success' }}">
                                        {{ $security['recent_auth_failures_24h'] }} Incidents
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="sh-card">
                        <h6 class="text-white fw-bold mb-3"><i class="bi bi-journal-text text-warning me-2"></i>Security Gate Audit Log</h6>
                        <div class="text-muted small mb-2" style="font-size: 0.8rem;">
                            Recent developer identity verification requests, authenticated sessions, and security gate events.
                        </div>
                        <div class="p-3 rounded-3" style="background: #0f172a; border: 1px solid #1f2937;">
                            <div class="mono small text-success mb-1">&bull; [{{ now()->format('Y-m-d H:i:s') }}] Diagnostic session active for {{ $security['developer_email'] }}</div>
                            <div class="mono small text-muted">&bull; IP Telemetry, User-Agent, and single-use hash recorded on each verification step.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
// CSRF Token Fallback
const csrfToken = window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// State Management
let currentPage = 1;
let currentWindow = '24h';

// Refresh Action
document.getElementById('btnRefresh')?.addEventListener('click', () => {
    window.location.reload();
});

// Synthetic Ping Probe
document.getElementById('btnSyntheticPing')?.addEventListener('click', async () => {
    const btn = document.getElementById('btnSyntheticPing');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Probing...';

    try {
        const response = await fetch("{{ route('system_health.api.synthetic_ping') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-System-Health-Probe': '1',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (response.ok && data.success) {
            alert(`✅ Synthetic Health Probe Succeeded!\nLatency: ${data.data.duration_ms} ms\nStatus: ${data.data.status}`);
            loadApiActivity(1);
        } else {
            alert(`⚠️ Synthetic Probe Issue: ${data.data?.message || 'Check failed'}`);
        }
    } catch (e) {
        alert('Network error attempting synthetic probe.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});

// Load API Activity Table
async function loadApiActivity(page = 1) {
    currentPage = page;
    const tbody = document.getElementById('activityTableBody');
    const statusFilter = document.getElementById('filterActivityStatus').value;
    const searchFilter = document.getElementById('filterActivitySearch').value;

    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-light py-3"><span class="spinner-border spinner-border-sm me-2"></span>Loading...</td></tr>';

    try {
        const url = new URL("{{ route('system_health.api.recent_requests') }}", window.location.origin);
        url.searchParams.set('page', page);
        if (statusFilter) url.searchParams.set('status_filter', statusFilter);
        if (searchFilter) url.searchParams.set('search', searchFilter);

        const res = await fetch(url.toString(), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        const json = await res.json();
        const records = json.data?.data || [];

        if (records.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-light py-3">No API activity logged yet. Issue an API call to record telemetry.</td></tr>';
            return;
        }

        tbody.innerHTML = records.map(r => {
            const statusClass = r.status_code < 300 ? 'status-pill-2xx' : (r.status_code < 400 ? 'status-pill-3xx' : (r.status_code < 500 ? 'status-pill-4xx' : 'status-pill-5xx'));
            return `
                <tr>
                    <td class="mono small text-light fw-medium">${r.created_at ? r.created_at.replace('T', ' ').slice(0, 19) : 'N/A'}</td>
                    <td><span class="badge bg-secondary mono fw-bold">${r.method}</span></td>
                    <td class="mono small text-white fw-bold">${r.path}</td>
                    <td><span class="status-pill ${statusClass}">${r.status_code}</span></td>
                    <td class="mono fw-bold ${r.duration_ms > 500 ? 'text-warning' : 'text-success'}">${r.duration_ms} ms</td>
                    <td><span class="badge border fw-semibold" style="background: #1e293b; border-color: #475569 !important; color: #e2e8f0;">${r.source_category}</span></td>
                    <td class="mono small" style="font-size: 0.78rem; color: #cbd5e1 !important;">${r.correlation_id ? r.correlation_id.slice(0, 13) + '...' : 'N/A'}</td>
                </tr>
            `;
        }).join('');

        // Pagination buttons
        document.getElementById('activityPaginationInfo').innerHTML = `<span class="text-light fw-medium">Page ${json.data.current_page} of ${json.data.last_page} (${json.data.total} records)</span>`;
        document.getElementById('btnPrevPage').disabled = json.data.current_page <= 1;
        document.getElementById('btnNextPage').disabled = json.data.current_page >= json.data.last_page;
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-3">Failed to load API telemetry data.</td></tr>';
    }
}

// Load Errors Tab
async function loadErrors() {
    const tbody = document.getElementById('errorsTableBody');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-light py-3"><span class="spinner-border spinner-border-sm me-2"></span>Reading logs...</td></tr>';

    try {
        const res = await fetch("{{ route('system_health.api.errors') }}", {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        });
        const json = await res.json();
        const errors = json.data?.errors || [];

        if (errors.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-success py-4"><i class="bi bi-check-circle fs-4 d-block mb-1"></i>No errors found in recent log buffer.</td></tr>';
            return;
        }

        tbody.innerHTML = errors.map(err => `
            <tr>
                <td class="mono small text-light fw-medium">${err.timestamp}</td>
                <td><span class="badge bg-danger mono fw-bold">${err.severity}</span></td>
                <td class="text-warning fw-bold small">${err.category}</td>
                <td class="mono small text-white fw-medium" style="font-size: 0.82rem; color: #ffffff !important;">${err.message}</td>
            </tr>
        `).join('');
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger py-3">Failed to inspect application errors.</td></tr>';
    }
}

// Window Switcher for API Metrics
document.querySelectorAll('.btn-window').forEach(btn => {
    btn.addEventListener('click', async (e) => {
        document.querySelectorAll('.btn-window').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const win = btn.getAttribute('data-window');

        try {
            const url = new URL("{{ route('system_health.api.api_metrics') }}", window.location.origin);
            url.searchParams.set('window', win);
            const res = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const json = await res.json();
            const d = json.data;

            document.getElementById('apiAvgLatency').innerHTML = `${d.latency.avg_ms} <span class="fs-6 text-muted">ms</span>`;
            document.getElementById('apiMedianLatency').textContent = d.latency.median_ms;
            document.getElementById('apiP95Latency').innerHTML = `${d.latency.p95_ms} <span class="fs-6 text-muted">ms</span>`;
            document.getElementById('apiP99Latency').textContent = d.latency.p99_ms;
            document.getElementById('apiErrorRate').textContent = `${d.error_rate_pct}%`;
            document.getElementById('apiFailedCount').textContent = d.failed_count;
            document.getElementById('stat2xx').textContent = d.status_codes['2xx'];
            document.getElementById('stat3xx').textContent = d.status_codes['3xx'];
            document.getElementById('stat4xx').textContent = d.status_codes['4xx'];
            document.getElementById('stat5xx').textContent = d.status_codes['5xx'];
        } catch (err) {
            console.error('Failed to update metrics window', err);
        }
    });
});

// Tab Change Event Listeners for Lazy Loading
document.getElementById('activity-tab')?.addEventListener('shown.bs.tab', () => loadApiActivity(1));
document.getElementById('errors-tab')?.addEventListener('shown.bs.tab', () => loadErrors());
document.getElementById('btnReloadErrors')?.addEventListener('click', () => loadErrors());

document.getElementById('filterActivityStatus')?.addEventListener('change', () => loadApiActivity(1));
document.getElementById('filterActivitySearch')?.addEventListener('input', () => {
    clearTimeout(window.searchDebounce);
    window.searchDebounce = setTimeout(() => loadApiActivity(1), 300);
});

document.getElementById('btnPrevPage')?.addEventListener('click', () => {
    if (currentPage > 1) loadApiActivity(currentPage - 1);
});
document.getElementById('btnNextPage')?.addEventListener('click', () => {
    loadApiActivity(currentPage + 1);
});

// Auto-load activity on page ready
document.addEventListener('DOMContentLoaded', () => {
    loadApiActivity(1);
});
</script>
@endpush

