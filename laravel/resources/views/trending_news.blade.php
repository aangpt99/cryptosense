<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Trending News</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/appaan.css') }}" rel="stylesheet">

    <style>
        body { background:#f5f7fa; color:#212529; }

        .back-btn {
            background:#fff;
            border:1px solid #ddd;
            padding:6px 16px;
            border-radius:999px;
            font-size:.9rem;
            text-decoration:none;
            color:#333;
        }

        #news-list {
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:22px;
        }

        @media (max-width:992px){
            #news-list{ grid-template-columns:repeat(2,1fr); }
        }

        @media (max-width:576px){
            #news-list{ grid-template-columns:repeat(1,1fr); }
        }

        .news-card{
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:12px;
            overflow:hidden;
            cursor:pointer;
            transition:.25s ease;
            box-shadow:0 2px 6px rgba(0,0,0,0.06);
        }

        .news-card:hover{
            transform:translateY(-4px);
            box-shadow:0 8px 18px rgba(0,0,0,0.15);
        }

        .news-card img{
            width:100%;
            height:150px;
            object-fit:cover;
        }

        .news-card .card-body{
            padding:12px 14px 16px;
        }

        .sentiment-badge{
            padding:3px 8px;
            border-radius:12px;
            font-size:.78rem;
            font-weight:600;
            display:inline-block;
            text-transform:capitalize;
        }

        .sentiment-positive{ background:#e8f5e9;color:#16a34a; }
        .sentiment-negative{ background:#fdecea;color:#dc2626; }
        .sentiment-neutral{ background:#f3f4f6;color:#6b7280; }
    </style>
</head>
<body>

<!-- ===================== NAVBAR ===================== -->
<nav class="navbar bg-white shadow-sm px-3 px-md-4 py-2 mb-3 d-flex align-items-center">

    <!-- LOGO -->
    <a class="navbar-brand fw-bold m-0 d-flex align-items-center gap-2 text-decoration-none text-dark" href="/">

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
    <div class="d-none d-md-flex align-items-center gap-3 ms-auto">

        <a href="/" class="nav-link-menu">
            Home
        </a>

        <a href="/coin/BTC/distribution" class="nav-link-menu">
            Distribusi Sentimen
        </a>

        <a href="/coin/BTC/chart" class="nav-link-menu">
            Chart Sentimen
        </a>

        <a href="/trending" class="nav-link-menu active">
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
    <div class="dropdown d-md-none ms-auto">

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

<!-- COIN TICKER -->
<div class="coin-ticker">
    <div class="coin-track">
        @for ($i=0;$i<2;$i++)
            @foreach($coinBar as $c)
                <div class="coin">
                    <img src="{{ asset('images/'.$c['logo']) }}" class="coin-logo">
                    <span class="coin-name">{{ $c['name'] }}</span>
                    <span class="coin-symbol">({{ $c['symbol'] }})</span>
                </div>
            @endforeach
        @endfor
    </div>
</div>

<div class="container pb-5">

    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="/" class="back-btn">← Back</a>
        <h3 class="mb-0 fw-semibold">Trending News</h3>
    </div>

    <div class="mb-4 d-flex align-items-center gap-2">
        <label class="fw-semibold">Kategori Sentiment:</label>
        <select id="filterSentiment" class="form-select form-select-sm" style="width:auto;">
            <option value="all">Semua</option>
            <option value="positive">Positive</option>
            <option value="negative">Negative</option>
            <option value="neutral">Neutral</option>
        </select>
    </div>

    <div id="news-list">
        @foreach($articles as $a)
        <div class="news-card" onclick="window.open('{{ $a->url }}','_blank')">

            <img src="{{ $a->thumbnail ?: asset('images/no-image.png') }}">

            <div class="card-body">
                <h6 class="title-truncate-2">{{ $a->title }}</h6>

                <small class="text-muted d-block mb-2">
                    {{ \Carbon\Carbon::parse($a->published_at)->format('Y-m-d H:i') }}
                    | {{ $a->source }}
                    | {{ $a->coin_symbol }}
                </small>

                <div class="sentiment-badge sentiment-{{ $a->sentiment }}">
                    {{ $a->sentiment }}
                    ({{ number_format($a->sentiment_score,3) }})
                </div>

                @auth
                @if(auth()->user()->is_admin)
                <div class="mt-2 d-flex gap-2" onclick="event.stopPropagation();">

                    <form method="POST" action="{{ route('admin.trending.pin',$a->id) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-warning">
                            {{ $a->is_pinned ? 'Unpin' : 'Pin' }}
                        </button>
                    </form>

                </div>
                @endif
                @endauth

            </div>
        </div>
        @endforeach

    </div>
</div>

<script>
const newsBox = document.getElementById("news-list");
const selectSentiment = document.getElementById("filterSentiment");

selectSentiment.addEventListener("change", () => {

    fetch(`/trending/filter?sentiment=${selectSentiment.value}`)
    .then(res => res.json())
    .then(data => {

        if(!data.articles || data.articles.length === 0){
            newsBox.innerHTML = `
                <div class="alert alert-info w-100 text-center">
                    Tidak ada berita untuk kategori ini.
                </div>
            `;
            return;
        }

        newsBox.innerHTML = "";

        const isAdmin = {{ auth()->check() && auth()->user()->is_admin ? 'true' : 'false' }};

        data.articles.forEach(a => {

            const adminButtons = isAdmin ? `
                <div class="mt-2 d-flex gap-2" onclick="event.stopPropagation();">

                    <form method="POST" action="/admin/trending/pin/${a.id}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="PATCH">

                        <button type="submit" class="btn btn-sm btn-warning">
                            ${a.is_pinned ? 'Unpin' : 'Pin'}
                        </button>
                    </form>

                </div>
            ` : "";

            newsBox.insertAdjacentHTML("beforeend", `
                <div class="news-card" onclick="window.open('${a.url}','_blank')">

                    <img src="${a.thumbnail ?? '/images/no-image.png'}">

                    <div class="card-body">

                        <h6 class="title-truncate-2">
                            ${a.title}
                        </h6>

                        <small class="text-muted d-block mb-2">
                            ${a.published_at_fmt} | ${a.source} | ${a.coin}
                        </small>

                        <div class="sentiment-badge sentiment-${a.sentiment}">
                            ${a.sentiment} (${Number(a.sentiment_score).toFixed(3)})
                        </div>

                        ${adminButtons}

                    </div>

                </div>
            `);
        });

    });

});
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>