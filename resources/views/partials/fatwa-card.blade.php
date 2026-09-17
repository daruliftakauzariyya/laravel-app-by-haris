@php
    // 1. Clean and process Title
    $rawTitle = (string)($fatwa->title ?: $fatwa->body);
    $cleanTitle = preg_replace('/<!--.*?-->/s', '', $rawTitle);
    $cleanTitle = preg_replace('/<(style|script|xml)[^>]*?>.*?<\/\1>/si', '', $cleanTitle);
    $decodedTitle = html_entity_decode($cleanTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $plainTitle = trim(preg_replace('/\s+/u', ' ', str_replace(["\xc2\xa0", "\u{00A0}", "&nbsp;"], ' ', strip_tags($decodedTitle))));
    $limitedTitle = Str::limit($plainTitle, 80);

    // 2. Clean and process Excerpt / Answer
    $rawAnswer = (string)($fatwa->answer ?: $fatwa->body);
    $cleanAnswer = preg_replace('/<!--.*?-->/s', '', $rawAnswer);
    $cleanAnswer = preg_replace('/<(style|script|xml)[^>]*?>.*?<\/\1>/si', '', $cleanAnswer);
    $decodedAnswer = html_entity_decode($cleanAnswer, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $plainAnswer = trim(preg_replace('/\s+/u', ' ', str_replace(["\xc2\xa0", "\u{00A0}", "&nbsp;"], ' ', strip_tags($decodedAnswer))));
    $limitedExcerpt = Str::limit($plainAnswer, 110);

    // 3. Multilingual language detection
    $isMalayalam = (bool) preg_match('/[\x{0D00}-\x{0D7F}]/u', $plainTitle . ' ' . $plainAnswer);
    $isArabic = (bool) preg_match('/[\x{0600}-\x{06FF}]/u', $plainTitle . ' ' . $plainAnswer);
    $langClass = $isMalayalam ? 'lang-malayalam' : ($isArabic ? 'lang-arabic' : 'lang-universal');

    // 4. Grid class wrapper
    $col = $colClass ?? 'col-12 col-md-6 col-lg-4';
@endphp

<div class="{{ $col }}">
    <a href="{{ route('fatwa.show', $fatwa) }}" class="text-decoration-none text-dark d-block h-100">
        <div class="fatwa-card h-100">
            {{-- Header Image & Madhab Badge --}}
            <div class="fatwa-thumb">
                <img src="{{ asset($fatwa->featured_image ?: 'assets/img/fatwa-placeholder.jpg') }}"
                     alt="Fatwa"
                     loading="lazy"
                     onerror="this.src='https://placehold.co/400x180/1a7a4a/fff?text=Fatwa'">
                <span class="madhab-badge">
                    {{ $fatwa->madhab?->name == 'Shafii' ? "Shafi'i" : ($fatwa->madhab?->name ?? 'General/Other') }}
                </span>
            </div>

            {{-- Card Body --}}
            <div class="fatwa-body">
                {{-- Meta Bar: Category + 5 Stars + Date --}}
                <div class="fatwa-meta">
                    <span class="fatwa-meta-cat"><i class="bi bi-folder2 me-1"></i>{{ $fatwa->category->name ?? 'General' }}</span>
                    <span class="text-warning fatwa-meta-stars">
                        @for($i = 0; $i < ($fatwa->category->stars ?? 5); $i++)
                            <i class="bi bi-star-fill"></i>
                        @endfor
                    </span>
                    <span class="fatwa-meta-date">{{ $fatwa->published_at?->format('d M Y') ?? 'Recent' }}</span>
                </div>

                {{-- Multilingual Title --}}
                <h6 class="fatwa-title {{ $langClass }}" @if($isArabic && !$isMalayalam) dir="rtl" @endif>
                    {{ $limitedTitle }}
                </h6>

                {{-- Multilingual Excerpt --}}
                <div class="fatwa-excerpt {{ $langClass }}" @if($isArabic && !$isMalayalam) dir="rtl" @endif>
                    {{ $limitedExcerpt }}
                </div>

                {{-- Footer: Scholar Avatar + Likes + Read More Pill --}}
                <div class="fatwa-footer">
                    <div class="fatwa-author-box">
                        <i class="bi bi-person-circle text-muted flex-shrink-0"></i>
                        <span class="fatwa-author">
                            {{ $fatwa->assignedMufti->name ?? 'Darul Ifta Kauzariyya' }}
                        </span>
                    </div>
                    <div class="fatwa-actions-box">
                        <span class="like-count text-danger small fw-semibold">
                            <i class="bi bi-heart-fill me-1"></i>{{ number_format($fatwa->likes ?? 0) }}
                        </span>
                        <span class="read-more-btn">Read More <i class="bi bi-arrow-right"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>
