<!DOCTYPE html>
<html>
<head>
    <title>Tambah Coin</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f8;
        color: #111827;
    }

    .container {
        max-width: 720px;
        margin: 60px auto;
        padding: 0 20px;
    }

    .back {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        padding: 10px 16px;
        margin-bottom: 20px;

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
            box-shadow .2s ease;
    }

    .back:hover {
        background: white;

        transform: translateY(-2px);

        box-shadow:
            0 10px 24px rgba(15,23,42,.10),
            inset 0 1px rgba(255,255,255,.9);
    }

    .card {
        background: rgba(255,255,255,0.82);

        padding: 34px;
        border-radius: 28px;

        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);

        border: 1px solid rgba(255,255,255,.45);

        box-shadow:
            0 20px 45px rgba(15,23,42,.08),
            inset 0 1px rgba(255,255,255,.7);
    }

    h2 {
        margin-top: 0;
        margin-bottom: 28px;

        font-size: 28px;
        font-weight: 700;
        color: #111827;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    input[type="text"],
    input[type="file"],
    input[type="color"] {
        width: 100%;

        padding: 14px 16px;

        border-radius: 16px;
        border: 1px solid #e5e7eb;

        background: rgba(255,255,255,.72);

        font-size: 15px;

        box-sizing: border-box;

        transition:
            border .2s ease,
            box-shadow .2s ease,
            transform .2s ease;
    }

    input:focus {
        outline: none;

        border-color: #6366f1;

        box-shadow:
            0 0 0 4px rgba(99,102,241,.12);

        transform: translateY(-1px);
    }

    label {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: -8px;
    }

    input[type="color"] {
        height: 58px;
        padding: 8px;
        cursor: pointer;
    }

    button {
        width: 100%;

        padding: 15px;

        border: none;
        border-radius: 18px;

        background: linear-gradient(
            135deg,
            #4f46e5,
            #4338ca
        );

        color: white;

        font-size: 15px;
        font-weight: 600;

        cursor: pointer;

        box-shadow:
            0 10px 25px rgba(79,70,229,.25);

        transition:
            transform .2s ease,
            box-shadow .2s ease;
    }

    button:hover {
        transform: translateY(-2px);

        box-shadow:
            0 18px 35px rgba(79,70,229,.35);
    }
</style>
</head>
<body>

<div class="container">

    <a href="/admin/coins" class="back">← Kembali</a>

    <div class="card">
        <h2>Tambah Coin</h2>

        <form method="POST" action="{{ route('coins.store') }}" enctype="multipart/form-data">
            @csrf

            <input type="text" name="name" placeholder="Nama Coin">
            <input type="text" name="symbol" placeholder="Symbol (BTC)">
            <input type="file" name="logo" placeholder="Logo filename (btc.png)">
            <input type="text" name="keywords" placeholder="Keywords">
            <label>Warna Hover</label>
            <input type="color" name="color" style="height:40px;">

            <button type="submit">Simpan</button>
        </form>
    </div>

</div>

</body>
</html>