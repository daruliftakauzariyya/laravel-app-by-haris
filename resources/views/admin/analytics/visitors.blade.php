@extends('layouts.admin')
@section('title', 'Visitor Insights')
@section('topbar-title', 'Visitor Insights & Analytics')
@section('topbar-icon', 'bi-geo-alt-fill')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
    <span class="text-dark fw-semibold">Visitor Insights</span>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="modern-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-inline-flex p-3 rounded-3" style="background: #EFF6FF; color: #2563EB;">
                        <i class="bi bi-globe-central-south-asia fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1 text-dark">Global Audience Footprint</h4>
                        <p class="mb-0 text-muted small">Visualizing worldwide traffic distribution, top reading cities, and audience devices.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge" style="background: #ECFDF5; color: #059669; padding: 6px 12px; font-weight: 700; border-radius: 20px;">
                        <span class="live-pulse-dot me-1.5" style="width: 7px; height: 7px;"></span> Live Telemetry Sync
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Top Countries --}}
    <div class="col-xl-4 col-lg-6">
        <div class="modern-card h-100 overflow-hidden">
            <div class="p-3.5 px-4 bg-white border-bottom d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-flag-fill me-2 text-primary"></i>Top Countries</h6>
                <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; border-radius: 20px; padding: 4px 10px;">{{ $countries->total() }} Total</span>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="modern-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Code</th>
                                <th>Country</th>
                                <th class="text-end pe-4">Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($countries as $c)
                            <tr>
                                <td class="ps-4"><span class="font-mono text-muted small">{{ $c->country_code }}</span></td>
                                <td class="fw-bold text-dark">{{ $c->country }}</td>
                                <td class="text-end pe-4">
                                    <span class="text-primary fw-bold">{{ number_format($c->count) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-center custom-pagination">
                    {{ $countries->appends(request()->except('country_page'))->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Top Regions/States --}}
    <div class="col-xl-4 col-lg-6">
        <div class="modern-card h-100 overflow-hidden">
            <div class="p-3.5 px-4 bg-white border-bottom d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-geo-fill me-2 text-primary"></i>Major Regions</h6>
                <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; border-radius: 20px; padding: 4px 10px;">{{ $regions->total() }} Total</span>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="modern-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Region</th>
                                <th>Country</th>
                                <th class="text-end pe-4">Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($regions as $r)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $r->region }}</td>
                                <td class="text-muted small">{{ $r->country }}</td>
                                <td class="text-end pe-4">
                                    <span class="text-primary fw-bold">{{ number_format($r->count) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="p-3 bg-white border-top">
                <div class="d-flex justify-content-center custom-pagination">
                    {{ $regions->appends(request()->except('region_page'))->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Top Cities --}}
    <div class="col-xl-4 col-lg-12">
        <div class="modern-card h-100 overflow-hidden">
            <div class="p-3.5 px-4 bg-white border-bottom d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-house-door-fill me-2 text-primary"></i>Urban Centres</h6>
                <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700; border-radius: 20px; padding: 4px 10px;">{{ $cities->total() }} Total</span>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="modern-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">City</th>
                                <th>Location</th>
                                <th class="text-end pe-4">Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cities as $ct)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $ct->city }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $ct->region ?? '' }}{{ (!empty($ct->region) && !empty($ct->country)) ? ', ' : '' }}{{ $ct->country ?? ($ct->location ?? '') }}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="text-primary fw-bold">{{ number_format($ct->count) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="p-3 bg-white border-top">
                <div class="d-flex justify-content-center custom-pagination">
                    {{ $cities->appends(request()->except('city_page'))->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .fw-black { font-weight: 900 !important; }
    .text-green { color: #1a7a4a !important; }
    .fw-mono { font-family: 'Inter', monospace; }
    
    .card { transition: transform 0.3s ease; }
    .card:hover { transform: translateY(-5px); }
    
    .table thead th { border-top: none; }
    .table tbody td { border-bottom: 1px solid rgba(0,0,0,0.03); }
    
    .custom-pagination .pagination {
        margin-bottom: 0;
    }
    .custom-pagination .page-link {
        border-radius: 8px !important;
        margin: 0 3px;
        border: none;
        background: #f8fafc;
        color: #1a7a4a;
        font-weight: 700;
        font-size: 0.75rem;
    }
    .custom-pagination .page-item.active .page-link {
        background: #1a7a4a;
        color: #fff;
    }
</style>
@endpush

@endsection
