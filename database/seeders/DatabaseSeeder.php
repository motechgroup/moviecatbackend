<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\User;
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
            'admob_android_banner_id' => 'ca-app-pub-6725687905795161/2199152801',
            'admob_android_interstitial_id' => 'ca-app-pub-6725687905795161/7159388334',
            'admob_android_rewarded_id' => 'ca-app-pub-3940256099942544/5224354917',
            'admob_ios_banner_id' => 'ca-app-pub-3940256099942544/2934735716',
            'admob_ios_interstitial_id' => 'ca-app-pub-3940256099942544/4411468910',
            'admob_ios_rewarded_id' => 'ca-app-pub-3940256099942544/1712485313',
            'interstitial_frequency' => 4,
            'banner_interval' => 30,
            'announcement_active' => true,
            'announcement_text' => 'MovieCat is live! Seamless streaming & bulk downloads enabled.',
            'announcement_banner' => [
                'active' => true,
                'text' => 'MovieCat is live! Seamless streaming & bulk downloads enabled.',
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
    }
}
