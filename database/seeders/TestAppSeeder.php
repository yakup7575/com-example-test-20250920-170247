<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\App;
use App\Models\MenuItem;

class TestAppSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Test uygulaması oluştur
        $app = App::create([
            'name' => 'Test Webview App',
            'package_name' => 'com.example.testapp',
            'bundle_id' => 'com.example.testapp',
            'description' => 'Bu bir test webview uygulamasıdır. Flutter kod generator sistemini test etmek için oluşturulmuştur.',
            'website_url' => 'https://flutter.dev',
            'primary_color' => '#2196F3',
            'secondary_color' => '#FFC107',
            'platform' => 'both',
            'status' => 'draft',
            'onesignal_app_id' => 'test-app-id-12345',
            'onesignal_api_key' => 'test-api-key-67890',
        ]);

        // Test menü öğeleri oluştur
        $menuItems = [
            [
                'title' => 'Ana Sayfa',
                'url' => 'https://flutter.dev',
                'icon' => 'home',
                'icon_type' => 'material',
                'order' => 0,
                'is_active' => true,
                'target' => '_self',
            ],
            [
                'title' => 'Dokümantasyon',
                'url' => 'https://docs.flutter.dev',
                'icon' => 'book',
                'icon_type' => 'material',
                'order' => 1,
                'is_active' => true,
                'target' => '_self',
            ],
            [
                'title' => 'Paketler',
                'url' => 'https://pub.dev',
                'icon' => 'extension',
                'icon_type' => 'material',
                'order' => 2,
                'is_active' => true,
                'target' => '_self',
            ],
            [
                'title' => 'GitHub',
                'url' => 'https://github.com/flutter/flutter',
                'icon' => 'fab fa-github',
                'icon_type' => 'fontawesome',
                'order' => 3,
                'is_active' => true,
                'target' => '_blank',
            ],
            [
                'title' => 'İletişim',
                'url' => 'https://flutter.dev/community',
                'icon' => 'contact_mail',
                'icon_type' => 'material',
                'order' => 4,
                'is_active' => true,
                'target' => '_self',
            ],
        ];

        foreach ($menuItems as $menuItem) {
            $app->menuItems()->create($menuItem);
        }

        $this->command->info('Test uygulaması ve menü öğeleri başarıyla oluşturuldu!');
        $this->command->info('Uygulama ID: ' . $app->id);
        $this->command->info('Paket Adı: ' . $app->package_name);
    }
}
