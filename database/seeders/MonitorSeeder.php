<?php

namespace Database\Seeders;

use App\Models\Monitor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first() ?? \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@pingpulse.com',
            'password' => bcrypt('password'),
        ]);

        // Create Tags
        $eaTag = \App\Models\Tag::firstOrCreate(['name' => 'EA Games'], ['color' => '#ff0000']);
        $epicTag = \App\Models\Tag::firstOrCreate(['name' => 'Epic Games'], ['color' => '#000000']);
        $bethesdaTag = \App\Models\Tag::firstOrCreate(['name' => 'Bethesda'], ['color' => '#ffffff']);
        $riotTag = \App\Models\Tag::firstOrCreate(['name' => 'Riot Games'], ['color' => '#d13639']);
        $sonyTag = \App\Models\Tag::firstOrCreate(['name' => 'PlayStation'], ['color' => '#003791']);
        $netEase = \App\Models\Tag::firstOrCreate(['name' => 'NetEase'], ['color' => '#ed1d24']);

        // Define Monitors
        $monitors = [
            // Singapore (ap-southeast-1)
            [
                'alias' => 'Apex Legends (SG)',
                'url' => 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
                'tag' => $eaTag,
                'region' => 'Singapore',
            ],
            [
                'alias' => 'Marvel Rivals (SG)',
                'url' => 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
                'tag' => $netEase,
                'region' => 'Singapore',
            ],
            [
                'alias' => 'League of Legends (SG)',
                'url' => 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
                'tag' => $riotTag,
                'region' => 'Singapore',
            ],
            [
                'alias' => 'Helldivers (SG)',
                'url' => 'https://dynamodb.ap-southeast-1.amazonaws.com/ping',
                'tag' => $sonyTag,
                'region' => 'Singapore',
            ],

            // Northeast (ap-northeast-1)
            [
                'alias' => 'Fortnite (JP)',
                'url' => 'https://dynamodb.ap-northeast-1.amazonaws.com/ping',
                'tag' => $epicTag,
                'region' => 'Asia (Tokyo)',
            ],
        ];

        foreach ($monitors as $data) {
            $monitor = Monitor::updateOrCreate(
                ['alias' => $data['alias']],
                [
                    'user_id' => $user->id,
                    'url' => $data['url'],
                    'check_interval' => 300,
                    'status' => 'pending',
                    'next_check_at' => now(),
                    'region' => $data['region'],
                ]
            );

            $monitor->tags()->syncWithoutDetaching([$data['tag']->id]);
        }
    }
}
