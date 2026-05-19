<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Coin;

class HomeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | HOME
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $articles = Article::whereDate('published_at', now()->toDateString())
        ->orderByDesc('is_pinned')
        ->latest('published_at')
        ->limit(35)
        ->get();

        $coinBar = Coin::all();
        $sentiment_summary = [];

        foreach ($coinBar as $coin) {

            $symbol = $coin->symbol;
            $coinArticles = $articles->where('coin_symbol', $symbol);

            $positive = $coinArticles->where('sentiment','positive')->count();
            $negative = $coinArticles->where('sentiment','negative')->count();
            $neutral  = $coinArticles->where('sentiment','neutral')->count();
            $total    = $coinArticles->count();

            $signedTotal = 0;

            foreach ($coinArticles as $a) {
                if ($a->sentiment === 'positive') {
                    $signedTotal += (float)$a->sentiment_score;
                } elseif ($a->sentiment === 'negative') {
                    $signedTotal -= (float)$a->sentiment_score;
                }
            }

            $avgScore = $total ? round($signedTotal/$total,3) : 0;

            $dominant = 'Neutral';
            if ($positive > $negative && $positive > $neutral) $dominant = 'Positive';
            elseif ($negative > $positive && $negative > $neutral) $dominant = 'Negative';

            $sentiment_summary[$symbol] = [
                'positive'=>$positive,
                'negative'=>$negative,
                'neutral'=>$neutral,
                'dominant'=>$dominant,
                'score'=>$avgScore
            ];
        }

        return view('home',compact(
            'articles',
            'sentiment_summary',
            'coinBar'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | COIN NEWS
    |--------------------------------------------------------------------------
    */
    public function coinNews($symbol)
    {
        $coinBar = Coin::all();
        $symbol = strtoupper($symbol);

        $coinInfo = collect($coinBar)->firstWhere('symbol',$symbol);
        if (!$coinInfo) abort(404);

        $articles = Article::where('coin_symbol',$symbol)
            ->latest('published_at')
            ->get();

        return view('coin_news',compact(
            'articles','coinInfo','coinBar'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | COIN DISTRIBUTION
    |--------------------------------------------------------------------------
    */
    public function coinDistribution($symbol)
{
    $coinBar = Coin::all();
    $symbol = strtoupper($symbol);

    $coinInfo = collect($coinBar)->firstWhere('symbol', $symbol);
    if (!$coinInfo) abort(404);

    $period = request('period', 'today');
    $now = now();

    // Tentukan range waktu
    $start = match($period) {
        'today'   => $now->copy()->startOfDay(),
        'weekly'  => $now->copy()->subDays(7),
        'monthly' => $now->copy()->subDays(30),
        default   => $now->copy()->startOfDay()
    };

    // Ambil artikel berdasarkan periode
    $articles = Article::where('coin_symbol', $symbol)
        ->where('published_at', '>=', $start)
        ->get();

    // Hitung berdasarkan sentiment_score (weighted)
    $positive = $articles->where('sentiment', 'positive')->sum('sentiment_score');
    $negative = $articles->where('sentiment', 'negative')->sum('sentiment_score');
    $neutral  = $articles->where('sentiment', 'neutral')->sum('sentiment_score');

    $total = $positive + $negative + $neutral;

    $distribution = [
        'positive' => $positive,
        'negative' => $negative,
        'neutral'  => $neutral,
        'total'    => $total,
        'positive_pct' => $total ? round(($positive / $total) * 100, 1) : 0,
        'negative_pct' => $total ? round(($negative / $total) * 100, 1) : 0,
        'neutral_pct'  => $total ? round(($neutral / $total) * 100, 1) : 0,
    ];

    return view('coin_distribution', [
        'coin' => $coinInfo,
        'coinBar' => $coinBar,
        'distribution' => $distribution,
        'period' => $period
    ]);
}

/*
|--------------------------------------------------------------------------
| COIN CHART (FROM ARTICLES - SYNC WITH HOMEPAGE)
|--------------------------------------------------------------------------
*/
public function coinChart($symbol)
{
    $coinBar = Coin::all();
    $symbol = strtoupper($symbol);

    $coin = collect($coinBar)->firstWhere('symbol', $symbol);
    if (!$coin) abort(404);

    $period = request('period', 'today');
    $now = now();

    $query = Article::where('coin_symbol', $symbol);

    // =========================
    // FILTER PERIODE (AMAN)
    // =========================
    switch ($period) {
        case 'today':
            $query->whereDate('published_at', $now->toDateString());
            break;

        case 'yesterday':
            $query->whereDate('published_at', $now->copy()->subDay()->toDateString());
            break;

        case 'weekly':
            $query->where('published_at', '>=', $now->copy()->subDays(7));
            break;

        case 'monthly':
            $query->where('published_at', '>=', $now->copy()->subDays(30));
            break;

        default:
            $query->whereDate('published_at', $now->toDateString());
            break;
    }

    // =========================
    // GROUP BY + COUNT (SAFE VERSION)
    // =========================
    $data = $query
        ->selectRaw("
            DATE(published_at) as date,
            SUM(CASE WHEN sentiment = 'positive' THEN 1 ELSE 0 END) as positive,
            SUM(CASE WHEN sentiment = 'negative' THEN 1 ELSE 0 END) as negative,
            SUM(CASE WHEN sentiment = 'neutral' THEN 1 ELSE 0 END) as neutral
        ")
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // =========================
    // FORMAT KE CHART
    // =========================
    $labels = [];
    $positive = [];
    $negative = [];
    $neutral = [];

    foreach ($data as $row) {
        $labels[] = $row->date;
        $positive[] = (int) $row->positive;
        $negative[] = (int) $row->negative;
        $neutral[]  = (int) $row->neutral;
    }

    $chart_data = [
        'labels'   => $labels,
        'positive' => $positive,
        'negative' => $negative,
        'neutral'  => $neutral
    ];

    return view('coin_chart', [
        'coin' => $coin,
        'coinBar' => $coinBar,
        'period' => $period,
        'chart_data' => $chart_data
    ]);
}
/*
|--------------------------------------------------------------------------
| TRENDING PAGE (SYNC WITH HOMEPAGE - TODAY ONLY)
|--------------------------------------------------------------------------
*/
public function trendingNews()
{
    $coinBar = Coin::all();

    $articles = Article::whereDate('published_at', now()->toDateString())
    // ->where('hidden_from_trending', false)
    ->orderByDesc('is_pinned')
    ->latest('published_at')
    ->limit(35)
    ->get();

    return view('trending_news', compact('articles','coinBar'));
}

/*
|--------------------------------------------------------------------------
| TRENDING FILTER (AJAX) - SYNC WITH HOMEPAGE
|--------------------------------------------------------------------------
*/
public function trendingFilter()
{
    $sentiment = request('sentiment', 'all');

    $query = Article::query()
        ->whereDate('published_at', now()->toDateString()) // 🔥 FIX UTAMA
        // ->where('hidden_from_trending', false)
        ->select([
            'id',
            'title',
            'url',
            'thumbnail',
            'sentiment',
            'sentiment_score',
            'published_at',
            'source',
            'coin_symbol',
            'is_pinned'
        ]);

    // filter sentiment
    if ($sentiment !== 'all') {
        $query->where('sentiment', $sentiment);
    }

    $articles = $query
        ->orderByDesc('is_pinned')
        ->latest('published_at') // 🔥 samain sama homepage
        ->limit(35)
        ->get();

    // mapping ringan
    $articles = $articles->map(function ($a) {
        return [
            'id' => $a->id,
            'title' => $a->title,
            'url' => $a->url,
            'thumbnail' => $a->thumbnail,
            'sentiment' => $a->sentiment,
            'sentiment_score' => $a->sentiment_score,
            'published_at_fmt' => \Carbon\Carbon::parse($a->published_at)->format('d M Y H:i'),
            'source' => $a->source,
            'coin' => $a->coin_symbol,
            'is_pinned' => $a->is_pinned
        ];
    });

    return response()->json([
        'articles' => $articles
    ]);
}
}