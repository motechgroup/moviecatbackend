<?php

namespace Database\Seeders;

use App\Models\AdEvent;
use App\Models\AppSetting;
use App\Models\Bookmark;
use App\Models\Device;
use App\Models\Episode;
use App\Models\MediaItem;
use App\Models\Season;
use App\Models\User;
use App\Models\WatchProgress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        User::firstOrCreate(
            ['email' => 'admin@moviecat.app'],
            [
                'name' => 'MovieCat Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_premium' => true,
            ]
        );

        // 2. Default App Settings
        $defaults = [
            'ads_enabled' => true,
            'admob_android_banner_id' => 'ca-app-pub-3940256099942544/6300978111',
            'admob_android_interstitial_id' => 'ca-app-pub-3940256099942544/1033173712',
            'admob_android_rewarded_id' => 'ca-app-pub-3940256099942544/5224354917',
            'admob_ios_banner_id' => 'ca-app-pub-3940256099942544/2934735716',
            'admob_ios_interstitial_id' => 'ca-app-pub-3940256099942544/4411468910',
            'admob_ios_rewarded_id' => 'ca-app-pub-3940256099942544/1712485313',
            'interstitial_frequency' => 4,
            'banner_interval' => 30,
            'announcement_active' => true,
            'announcement_text' => 'MovieCat v1.0 is live! Seamless streaming & bulk downloads enabled.',
            'announcement_banner' => [
                'active' => true,
                'text' => 'MovieCat v1.0 is live! Seamless streaming & bulk downloads enabled.',
                'type' => 'info',
            ],
            'min_app_version' => '1.0.0',
            'feature_bulk_download' => true,
            'feature_similar_series' => true,
            'feature_cloud_sync' => true,
        ];

        foreach ($defaults as $key => $val) {
            AppSetting::firstOrCreate(['key' => $key], ['value' => $val, 'group' => 'system']);
        }

        // 3. Sample Media Catalog
        $series = MediaItem::firstOrCreate(
            ['title' => 'Stranger Things'],
            [
                'tmdb_id' => 66732,
                'moviebox_id' => 'mb_66732',
                'media_type' => 'tv',
                'year' => '2016',
                'overview' => 'When a young boy vanishes, a small town uncovers a mystery involving secret experiments, terrifying supernatural forces and one strange little girl.',
                'poster' => 'https://image.tmdb.org/t/p/w500/49WJfeN0moxb9IPfGn8AIqMGskD.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/original/56v2KjBlU4XaOv9rVYEQypROD7P.jpg',
                'vote_average' => 8.6,
                'genres' => ['Sci-Fi', 'Drama', 'Mystery'],
                'is_featured' => true,
                'is_trending' => true,
                'status' => 'published',
            ]
        );

        $season1 = Season::firstOrCreate(
            ['media_item_id' => $series->id, 'season_number' => 1],
            [
                'name' => 'Season 1',
                'overview' => 'A young boy disappears, and his friends and mother search for answers.',
                'episode_count' => 8,
            ]
        );

        for ($ep = 1; $ep <= 8; $ep++) {
            Episode::firstOrCreate(
                ['media_item_id' => $series->id, 'season_number' => 1, 'episode_number' => $ep],
                [
                    'season_id' => $season1->id,
                    'title' => "Chapter $ep",
                    'overview' => "Episode $ep of Stranger Things Season 1.",
                    'duration' => 50,
                ]
            );
        }

        MediaItem::firstOrCreate(
            ['title' => 'Inception'],
            [
                'tmdb_id' => 27205,
                'moviebox_id' => 'mb_27205',
                'media_type' => 'movie',
                'year' => '2010',
                'overview' => 'Cobb, a skilled thief who commits corporate espionage by infiltrating the subconscious of his targets, is offered a chance to regain his old life.',
                'poster' => 'https://image.tmdb.org/t/p/w500/oYuLEt3zVCKq57qu2F8dT7NIa6f.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/original/s3TBrRGB1iav7gFOCNx3H31MoES.jpg',
                'vote_average' => 8.8,
                'genres' => ['Action', 'Sci-Fi', 'Thriller'],
                'is_featured' => true,
                'is_trending' => true,
                'status' => 'published',
            ]
        );

        MediaItem::firstOrCreate(
            ['title' => 'Interstellar'],
            [
                'tmdb_id' => 157336,
                'moviebox_id' => 'mb_157336',
                'media_type' => 'movie',
                'year' => '2014',
                'overview' => 'The adventures of a group of explorers who make use of a newly discovered wormhole to surpass the limitations on human space travel.',
                'poster' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/original/rAiYTsqJJR0KP8UN8vCnZ9v9jl0.jpg',
                'vote_average' => 8.7,
                'genres' => ['Adventure', 'Drama', 'Sci-Fi'],
                'is_featured' => true,
                'is_trending' => false,
                'status' => 'published',
            ]
        );

        // 4. Sample Devices & Telemetry
        $d1 = Device::firstOrCreate(
            ['device_id' => 'dev-ios-iphone15pro'],
            [
                'platform' => 'ios',
                'device_name' => 'iPhone 15 Pro',
                'os_version' => 'iOS 18.2',
                'app_version' => '1.0.0',
                'ip_address' => '192.168.1.105',
                'country' => 'United States',
                'city' => 'San Francisco',
                'sessions_count' => 14,
                'total_watch_seconds' => 4820,
                'last_active_at' => now()->subMinutes(2),
            ]
        );

        $d2 = Device::firstOrCreate(
            ['device_id' => 'dev-android-pixel8'],
            [
                'platform' => 'android',
                'device_name' => 'Pixel 8 Pro',
                'os_version' => 'Android 15',
                'app_version' => '1.0.0',
                'ip_address' => '192.168.1.112',
                'country' => 'United States',
                'city' => 'New York',
                'sessions_count' => 9,
                'total_watch_seconds' => 7820,
                'last_active_at' => now()->subMinutes(12),
            ]
        );

        $d3 = Device::firstOrCreate(
            ['device_id' => 'dev-ios-ipadpro13'],
            [
                'platform' => 'ios',
                'device_name' => 'iPad Pro (13-inch, M4)',
                'os_version' => 'iPadOS 18.1',
                'app_version' => '1.0.0',
                'ip_address' => '10.0.0.45',
                'country' => 'United Kingdom',
                'city' => 'London',
                'sessions_count' => 28,
                'total_watch_seconds' => 15400,
                'last_active_at' => now()->subHours(6),
            ]
        );

        // Sample Watch Progress
        WatchProgress::firstOrCreate(
            ['device_id' => $d1->device_id, 'media_id' => (string)$series->id, 'season' => 1, 'episode' => 3],
            [
                'media_type' => 'tv',
                'title' => 'Stranger Things - S01E03: Chapter Three: Holly, Jolly',
                'poster' => $series->poster,
                'current_time' => 1845,
                'duration' => 3000,
                'completed' => false,
                'last_watched_at' => now()->subMinutes(4),
            ]
        );

        WatchProgress::firstOrCreate(
            ['device_id' => $d2->device_id, 'media_id' => '27205'],
            [
                'media_type' => 'movie',
                'title' => 'Inception',
                'poster' => 'https://image.tmdb.org/t/p/w500/oYuLEt3zVCKq57qu2F8dT7NIa6f.jpg',
                'current_time' => 7820,
                'duration' => 8880,
                'completed' => false,
                'last_watched_at' => now()->subMinutes(15),
            ]
        );

        // Sample Bookmarks
        Bookmark::firstOrCreate(
            ['device_id' => $d1->device_id, 'media_id' => (string)$series->id],
            [
                'media_type' => 'tv',
                'title' => 'Stranger Things',
                'poster' => $series->poster,
                'vote_average' => 8.6,
                'year' => '2016',
            ]
        );

        // Sample Ad Events
        AdEvent::create([
            'device_id' => $d1->device_id,
            'ad_unit_id' => 'ca-app-pub-3940256099942544/2934735716',
            'ad_type' => 'banner',
            'event_type' => 'impression',
            'extra' => ['screen' => 'player'],
            'created_at' => now()->subMinutes(3),
        ]);
    }
}
