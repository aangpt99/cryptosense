<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $coin['name'] }} ({{ $coin['symbol'] }}) – Distribusi Sentimen</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/appaan.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { background:#f6f8fb; }

        .card-clean {
            background:#fff;
            border-radius:18px;
            border:1px solid #eef1f6;
            box-shadow:0 12px 28px rgba(15,23,42,.06);
        }

        .back-btn {
            border:1px solid #e5e7eb;
            padding:6px 14px;
            border-radius:999px;
            font-size:13px;
            text-decoration:none;
            color:#374151;
            background:#fff;
            transition:.2s;
        }

        .back-btn:hover {
            background:#f3f4f6;
        }

        .stat-box {
            padding:12px 16px;
            border-radius:14px;
            font-size:14px;
            font-weight:500;
        }

        .positive-box { background:#ecfdf5; color:#16a34a; }
        .negative-box { background:#fef2f2; color:#dc2626; }
        .neutral-box  { background:#f3f4f6; color:#6b7280; }
    </style>
</head>

<body>

<!-- ===================== NAVBAR ===================== -->
<nav class="navbar bg-white shadow-sm px-3 px-md-4 py-2 mb-3 d-flex justify-content-between align-items-center">

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
    <div class="d-none d-md-flex align-items-center gap-3">

        <a href="/" class="nav-link-menu">
            Home
        </a>

        <a href="/coin/BTC/distribution" class="nav-link-menu active">
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

<!-- COIN TICKER -->
<div class="coin-ticker">
    <div class="coin-track">
        @for ($i = 0; $i < 2; $i++)
            @foreach ($coinBar as $c)
                <a href="{{ route('coin.distribution', $c['symbol']) }}"
                   class="coin text-decoration-none text-dark">
                    <img src="{{ asset('images/'.$c['logo']) }}" class="coin-logo">
                    <span class="coin-name">{{ $c['name'] }}</span>
                    <span class="coin-symbol">({{ $c['symbol'] }})</span>
                </a>
            @endforeach
        @endfor
    </div>
</div>

<div class="container pb-5">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">

        <div class="d-flex align-items-center gap-3">
            <a href="/" class="back-btn">← Kembali</a>

            <img src="{{ asset('images/'.$coin['logo']) }}"
                 style="width:44px;height:44px;">

            <div>
                <h4 class="mb-0 fw-semibold">
                    Distribusi Sentimen
                </h4>
                <small class="text-muted">
                    {{ $coin['name'] }} ({{ $coin['symbol'] }})
                </small>
            </div>
        </div>

        <select onchange="changePeriod(this.value)"
                class="form-select form-select-sm d-inline-block w-auto">

            <option value="today"
                {{ $period == 'today' ? 'selected' : '' }}>Today</option>

            <option value="weekly"
                {{ $period == 'weekly' ? 'selected' : '' }}>Weekly</option>

            <option value="monthly"
                {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
        </select>
    </div>

    <div class="row g-4">

        <!-- PIE CHART -->
        <div class="col-md-7">
            <div class="card-clean p-4">
                <h6 class="fw-semibold mb-3">Sentiment Distribution</h6>
                <canvas id="pieChart" height="120"></canvas>
            </div>
        </div>

        <!-- SUMMARY -->
        <div class="col-md-5">
            <div class="card-clean p-4">

                <h6 class="fw-semibold mb-3">Ringkasan</h6>

                <p>Total Score Sentiment:
                    <b>{{ $distribution['total'] }}</b>
                </p>

                <div class="stat-box positive-box mb-3">
                    Positive:
                    {{ $distribution['positive'] }}
                    ({{ $distribution['positive_pct'] }}%)
                </div>

                <div class="stat-box negative-box mb-3">
                    Negative:
                    {{ $distribution['negative'] }}
                    ({{ $distribution['negative_pct'] }}%)
                </div>

                <div class="stat-box neutral-box">
                    Neutral:
                    {{ $distribution['neutral'] }}
                    ({{ $distribution['neutral_pct'] }}%)
                </div>

            </div>
        </div>

    </div>

</div>

<script>
function changePeriod(value) {
    window.location.href = "?period=" + value;
}

const ctx = document.getElementById('pieChart');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Positive', 'Negative', 'Neutral'],
        datasets: [{
            data: [
                {{ $distribution['positive'] }},
                {{ $distribution['negative'] }},
                {{ $distribution['neutral'] }}
            ],
            backgroundColor: [
                '#16a34a',
                '#dc2626',
                '#6b7280'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>