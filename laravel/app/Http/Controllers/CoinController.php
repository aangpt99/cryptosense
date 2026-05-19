<?php

namespace App\Http\Controllers;

use App\Models\Coin;
use App\Models\Article; // <-- WAJIB
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SentimentSummary;

class CoinController extends Controller
{
    public function index()
    {
        $coins = Coin::all();
        return view('admin.coins.index', compact('coins'));
    }

    public function create()
    {
        return view('admin.coins.create');
    }

    public function store(Request $request)
    {
        $fileName = null;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $fileName);
        }

        Coin::create([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'logo' => $fileName,
            'keywords' => $request->keywords,
            'color' => $request->color,
        ]);

        return redirect()->route('coins.index');
    }

    public function edit(Coin $coin)
    {
        return view('admin.coins.edit', compact('coin'));
    }

    public function update(Request $request, Coin $coin)
{
    $request->validate([
        'name' => 'required',
        'keywords' => 'nullable',
        'color' => 'nullable',
    ]);

    $data = [
        'name' => $request->name,
        'keywords' => $request->keywords,
    ];

    // update color hanya kalau diisi
    if ($request->filled('color')) {
        $data['color'] = $request->color;
    }

    // update logo kalau upload baru
    if ($request->hasFile('logo')) {
        $file = $request->file('logo');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('images'), $fileName);

        $data['logo'] = $fileName;
    }

    $coin->update($data);

    return redirect()->route('coins.index');
}

    public function destroy(Coin $coin)
{
    DB::transaction(function () use ($coin) {
        // hapus sentiment dulu (ini yang bikin error tadi)
        SentimentSummary::where('coin_symbol', $coin->symbol)->delete();

        // hapus artikel
        Article::where('coin_symbol', $coin->symbol)->delete();

        // baru hapus coin
        $coin->delete();
    });

    return redirect()->route('coins.index');
}
}