<!DOCTYPE html>

<html>
<head>
    <title>Kelola Coin</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f8;
    }

    .container {
        max-width: 820px;
        margin: 50px auto;
    }

    .back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 10px 16px;
    margin-bottom: 18px;

    background: rgba(255,255,255,0.75);
    color: #374151;

    text-decoration: none;
    font-size: 14px;
    font-weight: 600;

    border-radius: 14px;

    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);

    box-shadow:
        0 4px 14px rgba(15,23,42,.06),
        inset 0 1px rgba(255,255,255,.7);

    transition:
        transform .2s ease,
        background .2s ease,
        box-shadow .2s ease,
        color .2s ease;
    }

    .back-btn:hover {
        background: white;
        color: #111827;

        transform: translateY(-2px);

        box-shadow:
            0 10px 24px rgba(15,23,42,.10),
            inset 0 1px rgba(255,255,255,.9);
    }

    h1 {
        margin-bottom: 20px;
    }

    .card {
        background: white;
        border-radius: 12px;
        padding: 10px;
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        text-align: left;
        padding: 14px;
        background: #f1f3f5;
        font-weight: 600;
        font-size: 14px;
    }

    td {
        padding: 16px 14px;
        border-top: 1px solid #eee;
        font-size: 14px;
        vertical-align: middle;
    }
    
    .logo {
        width: 42px;
        height: 42px;
        object-fit: contain;
    }

    .symbol {
        font-weight: bold;
        color: #444;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .btn {
        padding: 7px 12px;
        border-radius: 6px;
        font-size: 12px;
        transition: 0.2s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: #4f46e5;
        color: white;
    }

    .btn-primary:hover {
        background: #4338ca;
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-warning:hover {
        background: #d97706;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
        border: none;
        cursor: pointer;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    .aksi {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .aksi form {
        margin: 0;
    }
</style>

</head>
<body>

<div class="container">
<a href="/" class="back-btn">← Kembali</a>

<div class="header">
    <h1>Kelola Coin</h1>
    <a href="{{ route('coins.create') }}" class="btn btn-primary">+ Tambah Coin</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Logo</th>
                <th>Nama</th>
                <th>Symbol</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach($coins as $coin)

            @php
                $color = $coin->color ?? '#f9fafb';

                if ($coin->symbol == 'BTC') $color = '#f7931a';
                elseif ($coin->symbol == 'ETH') $color = '#3c3c3d';
                elseif ($coin->symbol == 'BNB') $color = '#f3ba2f';
                elseif ($coin->symbol == 'ADA') $color = '#2a6cff';
                elseif ($coin->symbol == 'LTC') $color = '#345c9c';
                elseif ($coin->symbol == 'SOL') $color = '#14f195';
                elseif ($coin->symbol == 'XRP') $color = '#23292f';
                elseif ($coin->symbol == 'TRX') $color = '#ff060a';
                elseif ($coin->symbol == 'SUI') $color = '#6fbcf0';
                elseif ($coin->symbol == 'USDT') $color = '#26a17b';
            @endphp

            <tr
                onmouseover="this.style.background='{{ $color }}22'"
                onmouseout="this.style.background='white'"
            >
                <td>
                    <img src="/images/{{ $coin->logo }}" class="logo">
                </td>

                <td>{{ $coin->name }}</td>

                <td class="symbol">{{ $coin->symbol }}</td>

                <td>
                    <div class="aksi">

                        <!-- EDIT -->
                        <a href="{{ route('coins.edit', $coin) }}" class="btn btn-warning">
                            Edit
                        </a>

                        <!-- DELETE -->
                        <form action="{{ route('coins.destroy', $coin) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Yakin mau hapus coin ini?')">
                                Hapus
                            </button>
                        </form>

                    </div>
                </td>
            </tr>

            @endforeach
        </tbody>
    </table>
</div>
</div>
</body>
</html>
