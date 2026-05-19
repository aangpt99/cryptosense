<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $coin['name'] }} ({{ $coin['symbol'] }}) – Sentiment Chart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/appaan.css') }}" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body style="background:#f6f8fb; overflow-x:hidden;">

<!-- ===================== NAVBAR ===================== -->
<nav class="navbar bg-white shadow-sm px-3 px-md-4 py-2 mb-3 d-flex justify-content-between align-items-center">

    <!-- LOGO -->
    <a href="/"
       class="navbar-brand fw-bold m-0 d-flex align-items-center gap-2 text-decoration-none text-dark">

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

        <a href="/coin/BTC/distribution" class="nav-link-menu">
            Distribusi Sentimen
        </a>

        <a href="/coin/BTC/chart" class="nav-link-menu active">
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
                <a href="/coin/{{ $c['symbol'] }}/chart" class="coin">
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
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 mb-4 mt-3">

        <a href="/" class="btn btn-sm btn-outline-secondary">
            ← Kembali
        </a>

        <div class="d-flex align-items-center gap-3">

            <img src="{{ asset('images/'.$coin['logo']) }}"
                 style="width:44px;height:44px;">

            <h4 class="mb-0 fw-semibold fs-5 fs-md-4">
                Visualisasi Tren Sentimen –
                {{ $coin['name'] }} ({{ $coin['symbol'] }})
            </h4>

        </div>

    </div>

    <!-- PERIOD SELECT -->
    <div class="mb-4">

        <label class="fw-semibold me-2">
            Periode:
        </label>

        <select onchange="changePeriod(this.value)"
                class="form-select form-select-sm d-inline-block w-auto">

            <option value="today" {{ $period == 'today' ? 'selected' : '' }}>
                Today
            </option>

            <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>
                Weekly
            </option>

            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>
                Monthly
            </option>

        </select>

    </div>

    <!-- CHART -->
    <div class="card p-3 p-md-4 shadow-sm">

        <h6 class="fw-semibold mb-3">
            Sentiment Trend
        </h6>

        <div style="position:relative; height:300px;">
            <canvas id="sentimentChart"></canvas>
        </div>

    </div>

</div>

<script>
function changePeriod(value) {
    window.location.href = "?period=" + value;
}

const ctx = document.getElementById('sentimentChart');

const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chart_data['labels']),
        datasets: [
            {
                label: 'Positive',
                data: @json($chart_data['positive']),
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,0.1)',
                tension: 0.4
            },
            {
                label: 'Negative',
                data: @json($chart_data['negative']),
                borderColor: '#dc2626',
                backgroundColor: 'rgba(220,38,38,0.1)',
                tension: 0.4
            },
            {
                label: 'Neutral',
                data: @json($chart_data['neutral']),
                borderColor: '#6b7280',
                backgroundColor: 'rgba(107,114,128,0.1)',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,

        plugins: {
            legend: {
                position: 'top'
            }
        },

        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>