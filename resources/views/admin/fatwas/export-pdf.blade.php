<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Darul Ifta Kauzariyya - Fatwa Dossier Export ({{ $statusLabels }})</title>

    {{-- Google Noto Fonts Preconnect & Stylesheets --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Noto+Naskh+Arabic:wght@400;500;600;700&family=Noto+Nastaliq+Urdu:wght@400;600;700&family=Noto+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Noto+Serif+Malayalam:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand-green: #1e4d2b;
            --brand-dark-green: #13381e;
            --brand-gold: #c9a84c;
            --brand-navy: #1d4054;
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
            background-color: #e2e8f0;
            font-family: 'Noto Sans', sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
            font-size: 13.5px;
        }

        /* ── Dynamic Google Noto Typography Classes ── */
        .lang-malayalam {
            font-family: 'Noto Serif Malayalam', 'Gayathri', 'Manjari', serif !important;
            line-height: 1.8 !important;
            letter-spacing: 0.01em;
        }

        .lang-arabic {
            font-family: 'Noto Naskh Arabic', 'Amiri', serif !important;
            direction: rtl;
            text-align: right;
            line-height: 2 !important;
            font-size: 1.15em;
        }

        .lang-urdu {
            font-family: 'Noto Nastaliq Urdu', 'Noto Naskh Arabic', serif !important;
            direction: rtl;
            text-align: right;
            line-height: 2.2 !important;
            font-size: 1.1em;
        }

        .lang-universal {
            font-family: 'Noto Sans', sans-serif !important;
        }

        /* ── Floating Toolbar (Hidden when printing) ── */
        .export-toolbar {
            position: fixed;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 23, 42, 0.92);
            backdrop-filter: blur(10px);
            padding: 10px 24px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 16px;
            color: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            z-index: 9999;
            font-size: 13px;
        }

        .export-toolbar button, .export-toolbar a {
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
            font-size: 12.5px;
            transition: all 0.2s;
        }

        .export-toolbar button:hover, .export-toolbar a:hover {
            background: #fff;
            color: #0f172a;
        }

        .export-toolbar .btn-primary-print {
            background: #10b981 !important;
            border-color: #10b981 !important;
            color: #fff !important;
        }
        .export-toolbar .btn-primary-print:hover {
            background: #059669 !important;
        }

        /* ── Page Container & Strict 1-Per-Page Breakdown ── */
        .pdf-page-container {
            max-width: 210mm;
            margin: 70px auto 40px auto;
        }

        .pdf-page {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            margin: 0 auto 30px auto;
            padding: 16mm 16mm 14mm 16mm;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-after: always;
            break-after: page;
            page-break-inside: avoid;
        }

        /* ── Header & Official Banner ── */
        .page-header {
            border-bottom: 2px solid #1e4d2b;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .emblem-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .emblem-icon {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: var(--brand-green);
            color: var(--brand-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .inst-names h2 {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 17px;
            font-weight: 800;
            color: var(--brand-green);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            line-height: 1.2;
        }

        .inst-names .arabic-sub {
            font-family: 'Amiri', serif;
            font-size: 15px;
            color: var(--brand-gold);
            font-weight: 700;
            direction: rtl;
        }

        .inst-meta {
            text-align: right;
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .inst-meta strong {
            color: var(--text-dark);
        }

        /* ── Inquiry Meta Grid ── */
        .inquiry-meta-bar {
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 14px;
            margin-bottom: 16px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            font-size: 11.5px;
        }

        .meta-field .meta-lbl {
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 9.5px;
            font-weight: 700;
            display: block;
            margin-bottom: 1px;
        }

        .meta-field .meta-val {
            font-weight: 700;
            color: var(--text-dark);
        }

        .badge-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-accepted { background: #e0e7ff; color: #3730a3; }
        .status-assigned { background: #fef08a; color: #854d0e; }
        .status-answered { background: #dbeafe; color: #1e40af; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-published { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        /* ── Content Blocks ── */
        .page-body {
            flex: 1;
        }

        .section-box {
            margin-bottom: 16px;
        }

        .section-heading {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--brand-navy);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 4px;
        }

        .question-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--brand-dark-green);
            margin-bottom: 8px;
        }

        .question-text-box {
            background: #ffffff;
            border-left: 3.5px solid var(--brand-navy);
            padding: 8px 12px;
            border-radius: 0 6px 6px 0;
            color: #334155;
            font-size: 13px;
        }

        .answer-text-box {
            background: #fdfdfd;
            border-left: 3.5px solid var(--brand-green);
            padding: 10px 14px;
            border-radius: 0 6px 6px 0;
            color: #1e293b;
            font-size: 13.5px;
        }

        .bismillah-bar {
            text-align: center;
            font-family: 'Amiri', serif;
            font-size: 16px;
            color: var(--brand-gold);
            font-weight: 700;
            margin-bottom: 6px;
        }

        /* ── References Table ── */
        .refs-mini-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 11px;
        }

        .refs-mini-table th {
            background: var(--bg-light);
            border: 1px solid var(--border-color);
            padding: 4px 8px;
            text-align: left;
            font-weight: 700;
            color: var(--text-muted);
        }

        .refs-mini-table td {
            border: 1px solid var(--border-color);
            padding: 6px 8px;
            vertical-align: top;
        }

        .ref-arabic-quote {
            font-family: 'Noto Naskh Arabic', 'Amiri', serif;
            direction: rtl;
            font-size: 13px;
            color: #0f172a;
        }

        /* ── Signature Stamps / Verification Box ── */
        .sig-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 11px;
        }

        .sig-block {
            background: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 6px;
            padding: 6px 10px;
        }

        .sig-block .sig-role {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .sig-block .sig-name {
            font-weight: 700;
            color: var(--text-dark);
        }

        /* ── Page Footer ── */
        .page-footer {
            border-top: 1px solid var(--border-color);
            padding-top: 8px;
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            color: var(--text-muted);
        }

        .footer-short-url {
            font-family: monospace;
            color: var(--brand-green);
            font-weight: 700;
        }

        /* ── Print Styles ── */
        @media print {
            body {
                background: #fff;
                font-size: 12.5px;
            }

            .export-toolbar, .no-print {
                display: none !important;
            }

            .pdf-page-container {
                margin: 0;
                max-width: 100%;
            }

            .pdf-page {
                box-shadow: none;
                margin: 0;
                width: 100%;
                min-height: 290mm;
                padding: 10mm 12mm;
                page-break-after: always;
                break-after: page;
            }
        }
    </style>
</head>
<body>

    {{-- Floating Action Toolbar --}}
    <div class="export-toolbar no-print">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold"><i class="bi bi-file-earmark-pdf me-1"></i>PDF Export:</span>
            <span>{{ count($questions) }} Inquiries ({{ $statusLabels }})</span>
        </div>

        <button type="button" class="btn-primary-print" onclick="window.print()">
            <i class="bi bi-printer-fill"></i>Print / Save as PDF
        </button>

        <button type="button" onclick="adjustFontSize(1)">
            <i class="bi bi-zoom-in"></i>A+
        </button>
        <button type="button" onclick="adjustFontSize(-1)">
            <i class="bi bi-zoom-out"></i>A-
        </button>

        <a href="{{ route('admin.dashboard') }}">
            <i class="bi bi-arrow-left"></i>Back to Dashboard
        </a>
    </div>

    {{-- Printable Document Container --}}
    <div class="pdf-page-container">
        @forelse($questions as $index => $q)
            @php
                // Detect Language and assign Google Noto font family
                $titleAndBody = ($q->title ?? '') . ' ' . strip_tags($q->body ?? '') . ' ' . strip_tags($q->answer ?? '');
                $isMalayalam = (bool) preg_match('/[\x{0D00}-\x{0D7F}]/u', $titleAndBody) || strtolower($q->language ?? '') === 'malayalam';
                $isArabic = (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $titleAndBody) || in_array(strtolower($q->language ?? ''), ['arabic', 'ar']);
                $isUrdu = strtolower($q->language ?? '') === 'urdu';

                if ($isMalayalam) {
                    $langClass = 'lang-malayalam';
                } elseif ($isUrdu) {
                    $langClass = 'lang-urdu';
                } elseif ($isArabic) {
                    $langClass = 'lang-arabic';
                } else {
                    $langClass = 'lang-universal';
                }

                $statusClass = 'status-' . strtolower($q->status);
                $cleanTitle = trim(strip_tags($q->title ?: $q->body));
            @endphp

            <div class="pdf-page">
                {{-- Header --}}
                <div class="page-header">
                    <div class="header-top">
                        <div class="emblem-title">
                            <div class="emblem-icon">
                                <i class="bi bi-bank2"></i>
                            </div>
                            <div class="inst-names">
                                <h2>Darul Ifta Kauzariyya</h2>
                                <div class="arabic-sub">دار الإفتاء الكوثرية — كيرالا</div>
                            </div>
                        </div>
                        <div class="inst-meta">
                            <div><strong>Official Fatwa & Inquiry Dossier</strong></div>
                            <div>Portal: <strong>daruliftakauzariyya.com</strong></div>
                            <div>Short Link: <span class="footer-short-url">{{ route('fatwa.short', $q->id) }}</span></div>
                        </div>
                    </div>
                </div>

                {{-- Body Content --}}
                <div class="page-body">
                    {{-- Inquiry Meta Bar --}}
                    <div class="inquiry-meta-bar">
                        <div class="meta-field">
                            <span class="meta-lbl">Inquiry ID</span>
                            <span class="meta-val">#{{ $q->question_id }}</span>
                        </div>
                        <div class="meta-field">
                            <span class="meta-lbl">Current Status</span>
                            <span class="badge-status {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $q->status)) }}</span>
                        </div>
                        <div class="meta-field">
                            <span class="meta-lbl">Madhab / Category</span>
                            <span class="meta-val">{{ $q->madhab?->name ?? 'Common' }} · {{ $q->category?->name ?? 'General' }}</span>
                        </div>
                        <div class="meta-field">
                            <span class="meta-lbl">Received Date</span>
                            <span class="meta-val">{{ $q->created_at ? $q->created_at->format('d M Y') : '—' }}</span>
                        </div>
                    </div>

                    {{-- Questioner & Language Row --}}
                    <div style="display: flex; justify-content: space-between; font-size: 11.5px; margin-bottom: 12px; color: #475569; padding: 0 4px;">
                        <div>
                            <strong>Questioner:</strong> {{ $q->user?->name ?? 'Mustafti' }}
                            @if($q->user?->mobile) ({{ $q->user->mobile }}) @endif
                        </div>
                        <div>
                            <strong>Language:</strong> {{ $q->language ?: 'Malayalam' }} 
                            <span style="font-size: 10px; color: #94a3b8;">(Google Noto)</span>
                        </div>
                    </div>

                    {{-- Question Section --}}
                    <div class="section-box">
                        <div class="section-heading">
                            <i class="bi bi-question-circle-fill"></i>
                            <span>Inquiry / Question Text (ചോദ്യം)</span>
                        </div>
                        
                        @if($q->title)
                            <div class="question-title {{ $langClass }}">
                                {{ $q->title }}
                            </div>
                        @endif

                        <div class="question-text-box {{ $langClass }}">
                            {!! $q->formatted_body ?? $q->body !!}
                        </div>
                    </div>

                    {{-- Answer Section (If answered or draft exists) --}}
                    @if(!empty($q->answer) || !empty($q->draft_answer))
                        <div class="section-box">
                            <div class="section-heading">
                                <i class="bi bi-chat-left-text-fill"></i>
                                <span>Scholarly Ruling / Answer (മറുപടി)</span>
                            </div>

                            <div class="bismillah-bar">
                                بسم الله الرحمن الرحيم · الجواب وبالله التوفيق
                            </div>

                            <div class="answer-text-box {{ $langClass }}">
                                {!! $q->formatted_answer ?? ($q->answer ?: $q->draft_answer) !!}
                            </div>
                        </div>

                        {{-- Supporting References --}}
                        @php
                            $refs = $q->references->isNotEmpty() ? $q->references : $q->draftReferences;
                        @endphp
                        @if($refs && $refs->count())
                            <div class="section-box">
                                <div class="section-heading">
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                    <span>Scholarly Citations & Kitab References (ഹവാലകൾ)</span>
                                </div>
                                <table class="refs-mini-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 35%;">Book & Volume</th>
                                            <th style="width: 65%;">Arabic / Source Quote</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($refs as $ref)
                                            <tr>
                                                <td>
                                                    <strong>{{ $ref->book_name }}</strong>
                                                    @if($ref->language)<div style="color:#64748b; font-size:10px;">{{ $ref->language }}</div>@endif
                                                </td>
                                                <td>
                                                    <div class="ref-arabic-quote">{{ $ref->arabic_text }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        {{-- Signatures Stamp Row --}}
                        <div class="sig-row">
                            <div class="sig-block">
                                <div class="sig-role">Written / Answered By</div>
                                <div class="sig-name">{{ $q->assignedMufti?->name ?? 'Darul Ifta Faculty' }}</div>
                            </div>

                            <div class="sig-block">
                                <div class="sig-role">Verified By</div>
                                <div class="sig-name">
                                    @php
                                        $verifiers = collect();
                                        if (!empty($q->verified_by)) {
                                            $ids = is_array($q->verified_by) ? $q->verified_by : [$q->verified_by];
                                            $verifiers = \App\Models\User::whereIn('id', $ids)->pluck('name');
                                        }
                                    @endphp
                                    {{ $verifiers->isNotEmpty() ? $verifiers->implode(', ') : 'Ifta Verification Board' }}
                                </div>
                            </div>

                            <div class="sig-block">
                                <div class="sig-role">Checked & Approved</div>
                                <div class="sig-name">{{ $q->approver?->name ?? 'Authorized Scholar' }}</div>
                            </div>
                        </div>
                    @else
                        {{-- For Pending / Accepted Questions without answer yet --}}
                        <div style="background: #fffbeb; border: 1px dashed #f59e0b; border-radius: 6px; padding: 12px; margin-top: 20px; font-size: 12px; color: #92400e;">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Status Notice:</strong> This inquiry is currently in <strong>{{ ucfirst(str_replace('_', ' ', $q->status)) }}</strong> status and awaiting scholarly answer drafting and verification.
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="page-footer">
                    <div>
                        Darul Ifta Kauzariyya Dossier · Document Generated on {{ now()->format('d M Y, H:i') }}
                    </div>
                    <div style="font-weight: 700;">
                        Inquiry #{{ $q->question_id }} · Page {{ $index + 1 }} of {{ count($questions) }}
                    </div>
                </div>
            </div>
        @empty
            <div style="background: #fff; padding: 60px; text-align: center; border-radius: 8px; max-width: 600px; margin: 100px auto;">
                <i class="bi bi-journal-x" style="font-size: 48px; color: #94a3b8;"></i>
                <h3 style="margin-top: 16px; color: #1e293b;">No Fatwas or Inquiries Found</h3>
                <p style="color: #64748b;">No records match the selected status ({{ $statusLabels }}) and language filters.</p>
                <a href="{{ route('admin.dashboard') }}" style="display: inline-block; background: #1e4d2b; color: #fff; padding: 8px 18px; border-radius: 20px; text-decoration: none; font-weight: 600; margin-top: 10px;">
                    Return to Dashboard
                </a>
            </div>
        @endforelse
    </div>

    {{-- Script for Font Zoom & Auto-Print --}}
    <script>
        let currentScale = 1;
        function adjustFontSize(delta) {
            currentScale += delta * 0.08;
            if (currentScale < 0.7) currentScale = 0.7;
            if (currentScale > 1.4) currentScale = 1.4;
            document.querySelectorAll('.pdf-page').forEach(el => {
                el.style.fontSize = (13.5 * currentScale) + 'px';
            });
        }

        @if($autoPrint && count($questions) > 0)
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 800);
            });
        @endif
    </script>
</body>
</html>
