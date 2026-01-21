<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        $games = [
            [
                'id' => 'apex-sg',
                'name' => 'Apex Legends',
                'region' => 'Singapore',
                'url' => 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
                'icon' => '🎮',
            ],
            [
                'id' => 'marvel-sg',
                'name' => 'Marvel Rivals',
                'region' => 'Singapore',
                'url' => 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
                'icon' => '⚔️',
            ],
            [
                'id' => 'lol-sg',
                'name' => 'League of Legends',
                'region' => 'Singapore',
                'url' => 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
                'icon' => '🏆',
            ],
            [
                'id' => 'helldivers-sg',
                'name' => 'Helldivers',
                'region' => 'Singapore',
                'url' => 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
                'icon' => '🚀',
            ],
            [
                'id' => 'fortnite-jp',
                'name' => 'Fortnite',
                'region' => 'Tokyo',
                'url' => 'https://dynamodb.ap-northeast-1.amazonaws.com/ping',
                'icon' => '🏝️',
            ],
        ];

        return view('landing', compact('games'));
    }

    public function ping(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $pings = [];
        
        for ($i = 0; $i < 3; $i++) {
            $start = now();
            try {
                $response = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    ])
                    ->get($request->url);
                
                $duration = (int) abs(now()->diffInMilliseconds($start));
                $pings[] = $duration;
            } catch (\Exception $e) {
                $pings[] = 999; // Timeout/error
            }
        }

        $avgLatency = count($pings) > 0 ? (int) (array_sum($pings) / count($pings)) : 0;

        return response()->json([
            'average' => $avgLatency,
            'pings' => $pings,
        ]);
    }
}