@extends('layouts.admin')
@section('title', 'Visitor Insights')
@section('topbar-title', 'Visitor Insights (Sprite)')
@section('content')

<div class="container-fluid p-0">
    {{-- Header Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-globe-americas me-2 text-brand"></i>Global Visitor Footprint</h5>
                        <p class="text-muted small mb-0">Comprehensive analytics of your audience reach across countries, regions, and cities.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Countries Table --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-map me-2 text-primary"></i>Countries</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Code</th>
                                    <th>Country</th>
                                    <th class="text-end pe-4">Visitors</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($countries as $c)
                                <tr>
                                    <td class="ps-4"><span class="badge bg-light text-dark border fw-monospace">{{ $c->country_code }}</span></td>
                                    <td class="fw-semibold">{{ $c->country }}</td>
                                    <td class="text-end pe-4 fw-bold text-brand">{{ number_format($c->count) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $countries->appends(request()->except('country_page'))->links() }}
                </div>
            </div>
        </div>

        {{-- Regions Table --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-geo-alt me-2 text-danger"></i>States & Regions</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Region</th>
                                    <th>Country</th>
                                    <th class="text-end pe-4">Visitors</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($regions as $r)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ $r->region }}</td>
                                    <td class="text-muted">{{ $r->country }}</td>
                                    <td class="text-end pe-4 fw-bold text-danger">{{ number_format($r->count) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $regions->appends(request()->except('region_page'))->links() }}
                </div>
            </div>
        </div>

        {{-- Cities Table --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-building me-2 text-success"></i>City Breakdown</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">City</th>
                                    <th>Region</th>
                                    <th>Country</th>
                                    <th class="text-end pe-4">Visitors</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cities as $ct)
                                <tr>
                                    <td class="ps-4 fw-bold">{{ $ct->city }}</td>
                                    <td>{{ $ct->region }}</td>
                                    <td class="text-muted">{{ $ct->country }}</td>
                                    <td class="text-end pe-4 fw-bold text-success">{{ number_format($ct->count) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 py-3">
                    {{ $cities->appends(request()->except('city_page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-brand { color: var(--brand-blue); }
    .italic { font-style: italic; }
    .last-child-border-0:last-child { border-bottom: none !important; }
</style>

@endsection
