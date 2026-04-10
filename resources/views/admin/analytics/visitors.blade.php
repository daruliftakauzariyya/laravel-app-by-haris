@extends('layouts.admin')
@section('title', 'Visitor Insights')
@section('topbar-title', 'Visitor Insights — Audience Analytics')

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="glass-header p-4 rounded-4 shadow-sm mb-4" style="background: linear-gradient(135deg, #1a7a4a 0%, #0d4a2d 100%);">
            <div class="d-flex align-items-center justify-content-between text-white">
                <div>
                    <h3 class="fw-black mb-1"><i class="bi bi-globe-central-south-asia me-2"></i>Global Audience Footprint</h3>
                    <p class="mb-0 opacity-75">Visualizing your site's geographic reach and engagement metrics.</p>
                </div>
                <div class="d-none d-md-block text-end">
                    <span class="badge bg-white text-green px-3 py-2 rounded-pill fw-bold">Live Data Sync</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Top Countries --}}
    <div class="col-xl-4 col-lg-6">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-black text-green"><i class="bi bi-flag-fill me-2"></i>Top Countries</h6>
                <span class="badge bg-light text-muted fw-bold">{{ $countries->total() }} Total</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="ps-4 text-muted small fw-bold">Code</th>
                                <th class="text-muted small fw-bold">Country</th>
                                <th class="text-end pe-4 text-muted small fw-bold">Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($countries as $c)
                            <tr>
                                <td class="ps-4"><span class="fw-mono text-muted small">{{ $c->country_code }}</span></td>
                                <td class="fw-bold text-dark">{{ $c->country }}</td>
                                <td class="text-end pe-4">
                                    <span class="text-green fw-black">{{ number_format($c->count) }}</span>
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
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-black text-green"><i class="bi bi-geo-fill me-2"></i>Major Regions</h6>
                <span class="badge bg-light text-muted fw-bold">{{ $regions->total() }} Total</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="ps-4 text-muted small fw-bold">Region</th>
                                <th class="text-muted small fw-bold">Country</th>
                                <th class="text-end pe-4 text-muted small fw-bold">Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($regions as $r)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $r->region }}</td>
                                <td class="text-muted small">{{ $r->country }}</td>
                                <td class="text-end pe-4">
                                    <span class="text-green fw-black">{{ number_format($r->count) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-center custom-pagination">
                    {{ $regions->appends(request()->except('region_page'))->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Top Cities --}}
    <div class="col-xl-4 col-lg-12">
        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-black text-green"><i class="bi bi-house-door-fill me-2"></i>Urban Centres</h6>
                <span class="badge bg-light text-muted fw-bold">{{ $cities->total() }} Total</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="ps-4 text-muted small fw-bold">City</th>
                                <th class="text-muted small fw-bold">Location</th>
                                <th class="text-end pe-4 text-muted small fw-bold">Visits</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cities as $ct)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-black text-dark">{{ $ct->city }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $ct->region }}, {{ $ct->country }}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="text-green fw-black">{{ number_format($ct->count) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
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
