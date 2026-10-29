<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $fileName }}</title>

    {{-- Google Noto Fonts for Multilingual & Script Fidelity (Arabic, Malayalam, Latin) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Noto+Naskh+Arabic:wght@400;500;600;700&family=Noto+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Noto+Serif+Malayalam:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand-green: #1e4d2b;
            --brand-dark-green: #13381e;
            --brand-gold: #c9a84c;
            --brand-blue: #0f3554;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --bg-light: #f8fafc;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif;
            color: var(--text-dark);
            line-height: 1.55;
            font-size: 12.5px;
        }

        .lang-malayalam {
            font-family: 'Noto Serif Malayalam', serif !important;
        }

        .lang-arabic {
            font-family: 'Noto Naskh Arabic', 'Amiri', serif !important;
            direction: rtl;
        }

        /* ── Floating Controls (Screen Only) ── */
        .pdf-toolbar {
            position: fixed;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 23, 42, 0.94);
            backdrop-filter: blur(10px);
            padding: 8px 20px;
            border-radius: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            font-size: 13px;
        }

        .pdf-toolbar button, .pdf-toolbar a {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            padding: 6px 14px;
            border-radius: 20px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.2s;
        }

        .pdf-toolbar button:hover, .pdf-toolbar a:hover {
            background: #fff;
            color: #0f172a;
        }

        .pdf-toolbar .btn-print {
            background: #16a34a !important;
            border-color: #16a34a !important;
            color: #fff !important;
        }

        /* ── Page Layout ── */
        .pdf-container {
            max-width: 210mm;
            margin: 65px auto 40px auto;
            background: #fff;
            padding: 16mm 18mm;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
        }

        @page {
            size: A4 portrait;
            margin: 12mm 14mm;
        }

        @media print {
            body {
                background: #fff;
                font-size: 11.5px;
            }
            .pdf-toolbar {
                display: none !important;
            }
            .pdf-container {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }
            .page-break-before {
                page-break-before: always;
                break-before: page;
            }
            .avoid-break {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            thead {
                display: table-header-group;
            }
            tr {
                page-break-inside: avoid;
            }
        }

        /* ── Official Header ── */
        .report-header {
            border-bottom: 3px solid var(--brand-green);
            padding-bottom: 14px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-logo {
            width: 52px;
            height: 52px;
            background: var(--brand-green);
            color: var(--brand-gold);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }

        .brand-titles h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: var(--brand-green);
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .brand-titles .ar-subtitle {
            font-family: 'Amiri', serif;
            font-size: 16px;
            color: var(--brand-gold);
            font-weight: 700;
            direction: rtl;
            margin-top: 2px;
        }

        .brand-titles .mal-subtitle {
            font-family: 'Noto Serif Malayalam', serif;
            font-size: 11px;
            color: var(--text-muted);
        }

        .header-meta {
            text-align: right;
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .header-meta strong {
            color: var(--text-dark);
        }

        .period-banner {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-left: 4px solid var(--brand-gold);
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .period-banner h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: var(--brand-blue);
        }

        /* ── Metric Grid ── */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .metric-card {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 10px 12px;
            background: #fff;
        }

        .metric-card.border-green { border-left: 3px solid #16a34a; }
        .metric-card.border-blue { border-left: 3px solid #2563eb; }
        .metric-card.border-amber { border-left: 3px solid #d97706; }
        .metric-card.border-purple { border-left: 3px solid #9333ea; }

        .metric-card .label {
            font-size: 10.5px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.02em;
        }

        .metric-card .value {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-dark);
            margin: 3px 0;
        }

        .metric-card .subtext {
            font-size: 10px;
            color: var(--text-muted);
        }

        /* ── Sections ── */
        .report-section {
            margin-bottom: 22px;
        }

        .section-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--brand-green);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 6px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── Tables ── */
        table.report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            margin-bottom: 12px;
        }

        table.report-table th, table.report-table td {
            border: 1px solid var(--border-color);
            padding: 6px 9px;
            text-align: left;
        }

        table.report-table th {
            background-color: #f8fafc;
            font-weight: 700;
            color: var(--text-dark);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        table.report-table tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .badge-pill {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }
        .badge-amber { background: #fef3c7; color: #b45309; }
        .badge-red { background: #fee2e2; color: #b91c1c; }
        .badge-gray { background: #f1f5f9; color: #475569; }

        .disclaimer-box {
            background: #fffbeb;
            border: 1px dashed #f59e0b;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 11px;
            color: #92400e;
            margin-top: 15px;
        }

        .report-footer {
            border-top: 1px solid var(--border-color);
            padding-top: 10px;
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>

    {{-- Screen Floating Toolbar --}}
    <div class="pdf-toolbar">
        <button type="button" class="btn-print" onclick="window.print()">
            <i class="bi bi-printer-fill"></i> Print / Save as PDF
        </button>
        <button type="button" onclick="adjustScale(0.05)" title="Enlarge Text">A+</button>
        <button type="button" onclick="adjustScale(-0.05)" title="Shrink Text">A-</button>
        <a href="{{ route('admin.reports.index', request()->query()) }}">
            <i class="bi bi-arrow-left"></i> Return to Console
        </a>
    </div>

    <div class="pdf-container" id="pdfContainer">

        {{-- 1. Official Title & Organization Details --}}
        <div class="report-header">
            <div class="brand-section">
                <div class="brand-logo">
                    <i class="bi bi-bank2"></i>
                </div>
                <div class="brand-titles">
                    <h1>Darul Ifta Kauzariyya</h1>
                    <div class="ar-subtitle">دار الإفتاء الكوثرية — كيرلا، الهند</div>
                    <div class="malayalam-subtitle lang-malayalam">ദാറുൽ ഇഫ്താഹ് കൗസരിയ്യ — ഔദ്യോഗിക റിപ്പോർട്ട്</div>
                </div>
            </div>
            <div class="header-meta">
                <div><strong>Central Fatwa Board & Research Academy</strong></div>
                <div>Kauzariyya Islamic Complex, Kerala, India</div>
                <div>darulifta.kauzariyya@gmail.com · +91 9747650176</div>
                <div>Generated: <strong>{{ now()->format('d M Y, h:i A') }} ({{ $rangeData['timezone'] }})</strong></div>
            </div>
        </div>

        {{-- 2. Selected Reporting Period Banner --}}
        <div class="period-banner">
            <div>
                <h4><i class="bi bi-calendar-check me-2 text-success"></i>Official Analytics & Performance Report: {{ $periodLabel }}</h4>
                <div class="small text-muted mt-1">
                    @if($rangeData['start'] && $rangeData['end'])
                        Interval: <strong>{{ $rangeData['start']->format('d F Y') }}</strong> to <strong>{{ $rangeData['end']->format('d F Y') }}</strong> · Standard Timezone: {{ $rangeData['timezone'] }}
                    @else
                        Interval: <strong>Cumulative All Time</strong> · Standard Timezone: {{ $rangeData['timezone'] }}
                    @endif
                </div>
            </div>
            <div>
                <span class="badge-pill badge-green fs-6 px-3 py-1">Verified Audit Report</span>
            </div>
        </div>

        {{-- 3. Executive Summary Cards (12 Key Indicators) --}}
        <div class="metric-grid">
            <div class="metric-card border-blue">
                <div class="label">Total Questions Submitted</div>
                <div class="value">{{ number_format($metrics['total_questions']) }}</div>
                <div class="subtext">
                    @if($metrics['submission_change']['has_change'])
                        <span class="{{ $metrics['submission_change']['direction'] === 'up' ? 'text-success' : 'text-danger' }}">
                            {{ $metrics['submission_change']['label'] }}
                        </span>
                    @else
                        Inquiries submitted in period
                    @endif
                </div>
            </div>

            <div class="metric-card border-green">
                <div class="label">Published Fatwas in Period</div>
                <div class="value" style="color: #16a34a;">{{ number_format($metrics['published_fatwas']) }}</div>
                <div class="subtext">
                    @if($metrics['publication_change']['has_change'])
                        <span class="{{ $metrics['publication_change']['direction'] === 'up' ? 'text-success' : 'text-danger' }}">
                            {{ $metrics['publication_change']['label'] }}
                        </span>
                    @else
                        {{ $metrics['publish_rate'] }}% platform completion rate
                    @endif
                </div>
            </div>

            <div class="metric-card border-amber">
                <div class="label">Pending Review</div>
                <div class="value" style="color: #d97706;">{{ number_format($metrics['pending_questions']) }}</div>
                <div class="subtext">Awaiting scholar assignment</div>
            </div>

            <div class="metric-card border-blue">
                <div class="label">In Progress / Assigned</div>
                <div class="value">{{ number_format($metrics['in_progress_questions']) }}</div>
                <div class="subtext">Under scholarly study</div>
            </div>

            <div class="metric-card border-blue">
                <div class="label">Answered Questions</div>
                <div class="value">{{ number_format($metrics['answered_questions']) }}</div>
                <div class="subtext">Drafted / awaiting approval</div>
            </div>

            <div class="metric-card border-amber">
                <div class="label">Rejected Inquiries</div>
                <div class="value" style="color: #dc2626;">{{ number_format($metrics['rejected_questions']) }}</div>
                <div class="subtext">Ineligible / non-jurisprudential</div>
            </div>

            <div class="metric-card border-green">
                <div class="label">Total Public Readers / Reads</div>
                <div class="value">{{ number_format($metrics['total_views']) }}</div>
                <div class="subtext">
                    @if($metrics['views_change']['has_change'])
                        <span class="{{ $metrics['views_change']['direction'] === 'up' ? 'text-success' : 'text-danger' }}">
                            {{ $metrics['views_change']['label'] }}
                        </span>
                    @else
                        Public fatwa archive readership
                    @endif
                </div>
            </div>

            <div class="metric-card border-green">
                <div class="label">Unique Public Visitors</div>
                <div class="value">{{ number_format($metrics['unique_visitors']) }}</div>
                <div class="subtext">Privacy-anonymized sessions</div>
            </div>

            <div class="metric-card border-purple">
                <div class="label">Active Muftis & Scholars</div>
                <div class="value">{{ $metrics['active_muftis'] }}</div>
                <div class="subtext">Registered verification scholars</div>
            </div>

            <div class="metric-card border-purple">
                <div class="label">Total Work Sessions</div>
                <div class="value">{{ number_format($metrics['total_work_sessions']) }}</div>
                <div class="subtext">Scholar portal login sessions</div>
            </div>

            <div class="metric-card border-purple">
                <div class="label">Total Working Time</div>
                <div class="value" style="font-size: 16px;">{{ $metrics['working_time_formatted'] }}</div>
                <div class="subtext">
                    @if($metrics['working_time_available'])
                        Attributable working intervals
                    @else
                        Realtime session heartbeat active
                    @endif
                </div>
            </div>

            <div class="metric-card border-blue">
                <div class="label">Average Processing Time</div>
                <div class="value" style="font-size: 16px;">{{ $metrics['avg_processing_label'] }}</div>
                <div class="subtext">Submission to publication turnaround</div>
            </div>
        </div>

        {{-- 4. Executive Summary Narrative --}}
        <div class="report-section avoid-break">
            <div class="section-title"><i class="bi bi-file-earmark-text"></i> Executive Summary</div>
            <p style="margin: 0; line-height: 1.6; text-align: justify;">
                During the reporting period (<strong>{{ $periodLabel }}</strong>), Darul Ifta Kauzariyya recorded <strong>{{ number_format($metrics['total_questions']) }}</strong> submitted public inquiries. Over the same interval, <strong>{{ number_format($metrics['published_fatwas']) }}</strong> authoritative fatwas were approved and published to the public portal through the board's rigorous verification workflow. Public readership reached <strong>{{ number_format($metrics['total_views']) }}</strong> page views across <strong>{{ number_format($metrics['unique_visitors']) }}</strong> unique reader sessions. The scholar board maintains <strong>{{ $metrics['active_muftis'] }}</strong> active Muftis.
            </p>
        </div>

        {{-- 5. Question Status Distribution Table --}}
        <div class="report-section avoid-break">
            <div class="section-title"><i class="bi bi-pie-chart-fill"></i> Question Status Distribution Breakdown</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Status Category</th>
                        <th>Workflow Stage Description</th>
                        <th style="text-align: right;">Count</th>
                        <th style="text-align: right;">Percentage (% of Cohort)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statusDistribution['categories'] as $cat)
                    <tr>
                        <td style="font-weight: 700;">
                            <span class="badge-pill" style="background-color: {{ $cat['bg'] }}; color: {{ $cat['color'] }};">
                                {{ $cat['label'] }}
                            </span>
                        </td>
                        <td style="color: var(--text-muted);">{{ $cat['description'] }}</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($cat['count']) }}</td>
                        <td style="text-align: right; font-weight: 700;">{{ $cat['pct'] }}%</td>
                    </tr>
                    @endforeach
                    <tr style="background: #f8fafc; font-weight: 800;">
                        <td colspan="2">Total Questions Submitted in Cohort</td>
                        <td style="text-align: right;">{{ number_format($statusDistribution['total_cohort']) }}</td>
                        <td style="text-align: right;">100.0%</td>
                    </tr>
                </tbody>
            </table>
            <div style="font-size: 10.5px; color: var(--text-muted); font-style: italic;">
                Note: Status distribution tracks the current workflow status of questions submitted within the selected date range. In addition, <strong>{{ number_format($metrics['published_fatwas']) }}</strong> fatwas reached publication during this period independently of their initial submission date.
            </div>
        </div>

        {{-- 6. Mufti / Scholar Performance Matrix (Full Table) --}}
        <div class="report-section page-break-before">
            <div class="section-title"><i class="bi bi-person-badge"></i> Scholar / Mufti Individual Performance Matrix</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Mufti Name</th>
                        <th>Madhab</th>
                        <th style="text-align: center;">Logins</th>
                        <th>Last Login</th>
                        <th style="text-align: center;">Assigned</th>
                        <th style="text-align: center;">Worked</th>
                        <th style="text-align: center;">Answered</th>
                        <th style="text-align: center;">Published</th>
                        <th style="text-align: center;">Pending</th>
                        <th style="text-align: center;">Avg Time</th>
                        <th style="text-align: center;">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($muftis as $idx => $m)
                    <tr>
                        <td style="color: var(--text-muted); text-align: center;">{{ $idx + 1 }}</td>
                        <td style="font-weight: 700;">
                            {{ $m->name }}
                            @if(!$m->is_active)
                                <small style="color: #94a3b8; font-weight: normal;">(Inactive)</small>
                            @endif
                        </td>
                        <td>{{ $m->madhab_name }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ $m->login_count }}</td>
                        <td style="font-size: 10px; color: var(--text-muted);">{{ $m->last_login }}</td>
                        <td style="text-align: center;">{{ $m->questions_assigned }}</td>
                        <td style="text-align: center;">{{ $m->questions_worked }}</td>
                        <td style="text-align: center; font-weight: 600; color: #0284c7;">{{ $m->answers_submitted }}</td>
                        <td style="text-align: center; font-weight: 700; color: #16a34a;">{{ $m->fatwas_published }}</td>
                        <td style="text-align: center; color: #d97706;">{{ $m->pending_work }}</td>
                        <td style="text-align: center;">{{ $m->avg_processing }}</td>
                        <td style="text-align: center;">
                            <span class="badge-pill" style="background-color: {{ $m->score_color }}20; color: {{ $m->score_color }}; font-weight: 800;">
                                {{ $m->performance_score }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" style="text-align: center; padding: 15px; color: var(--text-muted);">No mufti records registered.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Scoring Methodology Documentation --}}
            <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: 6px; padding: 10px 12px; margin-top: 10px; font-size: 11px;">
                <strong><i class="bi bi-info-circle me-1 text-primary"></i>Transparent Performance Scoring Methodology:</strong>
                Score is calculated on a 100-point activity index:
                <strong>Fatwas Published (35 pts max)</strong> + 
                <strong>Answers Submitted (30 pts max)</strong> + 
                <strong>Workload Completion Rate (20 pts max)</strong> + 
                <strong>Platform Engagement & Logins (15 pts max)</strong>. 
                Scores classify into: <em>Exceptional (85–100)</em>, <em>High Impact (65–84)</em>, <em>Active (45–64)</em>, <em>Moderate (20–44)</em>, and <em>Low/Inactive (&lt;20)</em>.
            </div>
        </div>

        {{-- 7. Public Readership & Top Fatwas in Period --}}
        <div class="report-section avoid-break">
            <div class="section-title"><i class="bi bi-eye"></i> Public Readership & Top Viewed Fatwas During Period</div>
            <div style="display: flex; gap: 15px;">
                <div style="flex: 2;">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Question ID & Title</th>
                                <th>Category</th>
                                <th>Madhab</th>
                                <th style="text-align: right;">Period Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visitorAnalytics['top_fatwas'] as $idx => $tf)
                            <tr>
                                <td style="text-align: center; color: var(--text-muted);">{{ $idx + 1 }}</td>
                                <td style="font-weight: 600;">
                                    <span class="badge-pill badge-green" style="font-family: monospace;">{{ $tf->question_id }}</span>
                                    {{ Str::limit($tf->title ?: strip_tags($tf->body), 45) }}
                                </td>
                                <td>{{ $tf->category?->name ?? 'General' }}</td>
                                <td>{{ $tf->madhab?->name ?? 'General' }}</td>
                                <td style="text-align: right; font-weight: 700; color: #0284c7;">{{ number_format($tf->period_views ?? $tf->views) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 10px; color: var(--text-muted);">No published fatwa readership records found in period.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div style="flex: 1;">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Top Reader Country</th>
                                <th style="text-align: right;">Reads</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($visitorAnalytics['top_countries'] as $cName => $cViews)
                            <tr>
                                <td><strong>{{ $cName }}</strong></td>
                                <td style="text-align: right; font-weight: 600;">{{ number_format($cViews) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: var(--text-muted);">No country view data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 8. Data Limitations & Unavailable Metrics Disclosure --}}
        <div class="disclaimer-box avoid-break">
            <strong><i class="bi bi-shield-exclamation me-1"></i>Data Integrity & Operational Disclosures:</strong>
            <ul style="margin: 4px 0 0 16px; padding: 0; line-height: 1.5;">
                <li><strong>Event vs Cohort Distinction:</strong> Published fatwas ({{ number_format($metrics['published_fatwas']) }}) reflect actual publication events occurring within {{ $periodLabel }}, regardless of original question submission date. Total Questions ({{ number_format($metrics['total_questions']) }}) and status breakdown represent inquiries submitted within this window.</li>
                <li><strong>Visitor Identification:</strong> Unique visitors ({{ number_format($metrics['unique_visitors']) }}) are estimated using privacy-conscious daily IP/session hashes without collecting personally identifiable information. Internal automated indexing bots and administrative requests are excluded where identifiable.</li>
                <li><strong>Work-Session Durations:</strong> Realtime activity heartbeat tracking has been activated. For historical login sessions that terminated without heartbeat telemetry, working duration is indicated as "Not Available" to prevent data fabrication.</li>
            </ul>
        </div>

        {{-- 9. Footer & Sign-off --}}
        <div class="report-footer avoid-break">
            <div>
                Darul Ifta Kauzariyya Official Dossier · Generated on {{ now()->format('d M Y, h:i A') }} ({{ $rangeData['timezone'] }})
            </div>
            <div>
                Authentication: Confirmed by Darul Ifta Executive Secretariat · Page 1 of 1
            </div>
        </div>

    </div>

    <script>
        let currentScale = 1;
        function adjustScale(delta) {
            currentScale += delta;
            if (currentScale < 0.7) currentScale = 0.7;
            if (currentScale > 1.3) currentScale = 1.3;
            document.getElementById('pdfContainer').style.fontSize = (12.5 * currentScale) + 'px';
        }
    </script>
</body>
</html>

