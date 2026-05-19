<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Crypto Analysis Sentiment</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/appaan.css') }}" rel="stylesheet">

    <style>
        body {
    background: linear-gradient(to bottom, #f9fafb, #e5e7eb, #d1d5db);
        }

        .page-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px 60px;
        }

        .section-space { margin-top: 20px; }

        .highlight-wrapper { margin-top: 40px; padding: 0 10px; }

        .highlight-scroll { display: flex; gap: 24px; }

        .highlight-card { min-width: 280px; }

        .coin-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 32px;
            margin-top: 20px;
            padding: 0 10px;
        }

        .admin-delete-btn {
            background: #dc3545;
            color: #fff;
            border: none;
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 4px;
            margin-top: 8px;
        }

        .admin-delete-btn:hover {
            background: #bb2d3b;
        }
    </style>
</head>

<body>

<!-- ===================== NAVBAR ===================== -->
<nav class="navbar bg-white shadow-sm px-3 px-md-4 py-2 mb-3 d-flex justify-content-between align-items-center">

    <!-- LOGO -->
    <a class="navbar-brand fw-bold m-0 d-flex align-items-center gap-2" href="/">

        <img 
            src="{{ asset('images/lobo.png') }}"
            alt="Logo"
            style="
                width: 38px;
                height: 38px;
                object-fit: contain;
            "
        >

        <!-- DESKTOP TITLE -->
        <span class="d-none d-md-inline">
            CRYPTO ANALYSIS SENTIMENT
        </span>

        <!-- MOBILE TITLE -->
        <span class="d-inline d-md-none">
            CRYPTO ANALYSIS SENTIMENT
        </span>

    </a>

    <!-- DESKTOP MENU -->
    <div class="d-none d-md-flex align-items-center gap-3">

        <a href="/" class="nav-link-menu active">
            Home
        </a>

        <a href="/coin/BTC/distribution" class="nav-link-menu">
            Distribusi Sentimen
        </a>

        <a href="/coin/BTC/chart" class="nav-link-menu">
            Chart Sentimen
        </a>

        <a href="/trending" class="nav-link-menu">
            Trending News
        </a>

        @guest
            <a href="{{ route('login') }}" class="login-btn">
                Login
            </a>
        @endguest

        @auth
            <div class="dropdown ms-3">

                <button class="btn btn-dark btn-sm dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="true">

                    {{ auth()->user()->name }}

                </button>

                <ul class="dropdown-menu dropdown-menu-end">

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit" class="dropdown-item">
                                Logout
                            </button>
                        </form>
                    </li>

                </ul>

            </div>
        @endauth

    </div>

    <!-- MOBILE MENU -->
    <div class="dropdown d-md-none">

        <button class="btn btn-dark btn-sm"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">

            ⋮

        </button>

        <ul class="dropdown-menu dropdown-menu-end">

            <li>
                <a class="dropdown-item" href="/">
                    Home
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="/coin/BTC/distribution">
                    Distribusi Sentimen
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="/coin/BTC/chart">
                    Chart Sentimen
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="/trending">
                    Trending News
                </a>
            </li>

            @guest
                <li>
                    <a class="dropdown-item" href="{{ route('login') }}">
                        Login
                    </a>
                </li>
            @endguest

            @auth
                <li><hr class="dropdown-divider"></li>

                <li class="px-3 py-2 text-muted small">
                    {{ auth()->user()->name }}
                </li>

                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit" class="dropdown-item text-danger">
                            Logout
                        </button>
                    </form>
                </li>
            @endauth

        </ul>

    </div>

</nav>

<div class="page-container">

    <!-- ================= COIN TICKER ================= -->
    <div class="coin-ticker section-space">

        <div class="coin-track">
            @for ($i = 0; $i < 2; $i++)
                @foreach($coinBar as $coin)
                    @php
                        $stats = $sentiment_summary[$coin['symbol']] ?? null;
                        $score = $stats['score'] ?? 0;
                        $dominant = $stats['dominant'] ?? 'Neutral';
                    @endphp

                    <div class="coin">
                        <img src="{{ asset('images/'.$coin['logo']) }}" class="coin-logo">
                        <span class="coin-name">{{ $coin['name'] }}</span>
                        <span class="coin-symbol">({{ $coin['symbol'] }})</span>
                        <span class="coin-sentiment {{ $dominant }}">
                            @if($dominant === 'Neutral')
                                0.000
                            @else
                                {{ number_format($score, 3) }}
                            @endif

                        </span>
                    </div>
                @endforeach
            @endfor
        </div>
    </div>

    <!-- ================= HIGHLIGHT NEWS ================= -->
    @if($articles->count())
<div class="highlight-wrapper section-space">

    <!-- BUTTON KIRI -->
    <button class="scroll-btn left" onclick="scrollHighlight(-300)">‹</button>

    <div class="highlight-scroll" id="highlightScroll">

        @foreach($articles as $article)
        <div class="highlight-card position-relative bg-white p-2 shadow-sm rounded">

            <img src="{{ $article->thumbnail ?: 'https://via.placeholder.com/300x180?text=No+Image' }}"
                 class="card-img-top rounded">

            <div class="card-body p-2">
                <h6 class="title-truncate-2">{{ $article->title }}</h6>

                <small class="text-muted text-compact">
                {{ \Carbon\Carbon::parse($article->published_at)->format('Y-m-d H:i') }}
                | {{ $article->source }}
                | {{ strtoupper($article->coin_symbol) }}
                </small>
                </div>

            <a href="{{ $article->url }}" class="stretched-link" target="_blank"></a>

            @auth
            @if(auth()->user()->is_admin)
            <div class="position-absolute top-0 end-0 m-2" style="z-index: 20;">
                <form action="{{ route('admin.article.delete', $article->id) }}"
                      method="POST"
                      onsubmit="return confirm('Yakin hapus artikel ini?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger btn-sm">
                        Delete
                    </button>
                </form>
            </div>
            @endif
            @endauth

        </div>
        @endforeach

    </div>

    <!-- BUTTON KANAN -->
    <button class="scroll-btn right" onclick="scrollHighlight(300)">›</button>

</div>
@endif


@auth
@if(auth()->user()->is_admin)
    <div style="margin-top:20px; margin-bottom:10px;">
        <a href="/admin/coins" class="btn btn-outline-dark btn-sm">
            ⚙️ Kelola Coin
        </a>
    </div>
@endif
@endauth

    <!-- ================= COIN SUMMARY GRID ================= -->
    <div class="coin-summary-grid section-space">
        @foreach($coinBar as $coin)

        @php
            $data = $sentiment_summary[$coin['symbol']] ?? [
                'positive' => 0,
                'negative' => 0,
                'neutral'  => 0,
                'dominant' => 'Neutral',
                'score'    => 0
            ];

            $trend_class = 'trend-neutral';
            if($data['dominant'] === 'Positive') $trend_class = 'trend-positive';
            if($data['dominant'] === 'Negative') $trend_class = 'trend-negative';
        @endphp

        <div class="coin-summary-card {{ strtolower($data['dominant']) }}">

            <div class="trend-badge {{ $trend_class }}">
                <span class="trend-dot"></span>
                <span class="trend-text">{{ $data['dominant'] }}</span>
            </div>

            <div class="coin-summary-header">
                <div class="coin-summary-left">
                    <img src="{{ asset('images/'.$coin['logo']) }}" class="coin-summary-logo">
                    <div>
                        <div class="coin-summary-name">{{ $coin['name'] }}</div>
                        <div class="coin-summary-sub">{{ $coin['symbol'] }}</div>
                    </div>
                </div>

                <div class="coin-summary-right {{ $data['dominant'] }}">

                @if($data['dominant'] === 'Neutral')
                    0.00
                @else
                    {{ number_format($data['score'], 2) }}
                @endif

            </div>
            </div>

            <div class="coin-summary-details mt-2">
                <span>Positive <b>{{ $data['positive'] }}</b></span>
                <span>•</span>
                <span>Negative <b>{{ $data['negative'] }}</b></span>
                <span>•</span>
                <span>Neutral <b>{{ $data['neutral'] }}</b></span>
            </div>

        </div>
        @endforeach
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('main.js') }}"></script>
</body>
</html>