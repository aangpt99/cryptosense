<!DOCTYPE html>
<html>
<head>
    <title>Edit Coin</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f8;
        color: #111827;
    }

    .container {
    max-width: 680px;
    margin: 38px auto;
    padding: 0 18px;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        padding: 10px 16px;
        margin-bottom: 22px;

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

    .back-btn:hover {
        background: white;

        transform: translateY(-2px);

        box-shadow:
            0 10px 24px rgba(15,23,42,.10),
            inset 0 1px rgba(255,255,255,.9);
    }

    .card {
        background: rgba(255,255,255,0.82);

        border-radius: 24px;
        padding: 28px;

        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);

        border: 1px solid rgba(255,255,255,.45);

        box-shadow:
            0 20px 45px rgba(15,23,42,.08),
            inset 0 1px rgba(255,255,255,.7);
    }

    h1 {
        margin-top: 0;
        margin-bottom: 34px;

        font-size: 30px;
        font-weight: 800;
        letter-spacing: -1px;

        color: #111827;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    label {
        font-size: 14px;
        font-weight: 600;
        color: #4b5563;
    }

    input,
    textarea {
        width: 100%;

        padding: 12px 14px;

        border-radius: 14px;
        border: 1px solid #e5e7eb;

        background: rgba(255,255,255,.72);

        font-size: 14px;

        box-sizing: border-box;

        transition:
            border .2s ease,
            box-shadow .2s ease,
            transform .2s ease;
    }

    input:focus,
    textarea:focus {
        outline: none;

        border-color: #6366f1;

        box-shadow:
            0 0 0 4px rgba(99,102,241,.12);

        transform: translateY(-1px);
    }

    textarea {
        resize: vertical;
        min-height: 110px;
    }

    .symbol-readonly {
        background: #f9fafb;
        color: #6b7280;
        cursor: not-allowed;
    }

    .color-picker {
        display: flex;
        gap: 14px;
        align-items: center;
    }

    #colorPicker {
        width: 72px;
        height: 56px;

        padding: 4px;

        border: none;
        border-radius: 14px;

        cursor: pointer;
    }

    .color-preview {
        width: 56px;
        height: 56px;

        border-radius: 16px;

        border: 2px solid rgba(255,255,255,.8);

        box-shadow:
            0 8px 20px rgba(15,23,42,.08);
    }

    .btn {
        width: 100%;

        padding: 15px;

        border: none;
        border-radius: 18px;

        font-size: 15px;
        font-weight: 600;

        cursor: pointer;

        transition:
            transform .2s ease,
            box-shadow .2s ease;
    }

    .btn-primary {
        color: white;

        background: linear-gradient(
            135deg,
            #4f46e5,
            #4338ca
        );

        box-shadow:
            0 10px 25px rgba(79,70,229,.25);
    }

    .btn-primary:hover {
        transform: translateY(-2px);

        box-shadow:
            0 18px 35px rgba(79,70,229,.35);
    }
</style>
</head>

<body>

<div class="container">
<a href="{{ route('coins.index') }}" class="back-btn">← Kembali</a>

<div class="card">
    <h1>Edit Coin</h1>

    <form action="{{ route('coins.update', $coin->symbol) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- 🔥 SYMBOL LAMA (WAJIB BANGET) -->
        <input type="hidden" name="old_symbol" value="{{ $coin->symbol }}">

        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="name" value="{{ $coin->name }}" required>
        </div>

        <div class="form-group">
            <label>Symbol (tidak bisa diubah)</label>
            <input type="text" value="{{ $coin->symbol }}" class="symbol-readonly" readonly>
        </div>

        <div class="form-group">
            <label>Logo (opsional)</label>
            <input type="file" name="logo">
        </div>

        <div class="form-group">
            <label>Keywords</label>

            <input 
                type="text" 
                name="keywords" 
                value="{{ $coin->keywords }}"
            >
        </div>

        <div class="form-group">
            <label>Color</label>

            <div class="color-picker">
                <input type="color" id="colorPicker" value="{{ $coin->color ?? '#4f46e5' }}">
                <input type="text" name="color" id="colorInput" value="{{ $coin->color }}">
                <div class="color-preview" id="colorPreview" style="background: {{ $coin->color }}"></div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
</div>

<script>
const picker = document.getElementById('colorPicker');
const input = document.getElementById('colorInput');
const preview = document.getElementById('colorPreview');

picker.addEventListener('input', function() {
    input.value = this.value;
    preview.style.background = this.value;
});

input.addEventListener('input', function() {
    picker.value = this.value;
    preview.style.background = this.value;
});
</script>

</body>
</html>