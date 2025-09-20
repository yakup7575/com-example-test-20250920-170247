<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\App;
use App\Services\GitHubActionsService;

class TestGitHubBuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:github-build {package_name=com.example.testapp}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test GitHub Actions build system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $packageName = $this->argument('package_name');
        
        $this->info("🚀 GitHub Actions Build Test Başlıyor...");
        $this->info("📦 Paket adı: {$packageName}");
        
        // GitHub konfigürasyonunu kontrol et
        $githubToken = config('services.github.token');
        $githubUsername = config('services.github.username');
        
        if (empty($githubToken) || empty($githubUsername)) {
            $this->error("❌ GitHub konfigürasyonu eksik!");
            $this->error("GITHUB_TOKEN ve GITHUB_USERNAME .env dosyasında ayarlanmalı.");
            return 1;
        }
        
        $this->info("✅ GitHub Token: " . substr($githubToken, 0, 10) . "...");
        $this->info("✅ GitHub Username: {$githubUsername}");
        
        // Test uygulamasını bul
        $app = App::where('package_name', $packageName)->first();
        
        if (!$app) {
            $this->error("❌ Uygulama bulunamadı: {$packageName}");
            $this->info("💡 Önce test uygulamasını oluşturun: php artisan db:seed --class=TestAppSeeder");
            return 1;
        }
        
        $this->info("✅ Uygulama bulundu: {$app->name}");
        $this->info("📋 Menü öğesi sayısı: " . $app->menuItems->count());
        
        // GitHub Actions build başlat
        $this->info("\n🔄 GitHub Actions build başlatılıyor...");
        
        try {
            $githubService = new GitHubActionsService();
            $result = $githubService->createAndBuildApp($app);
            
            if ($result['success']) {
                $this->info("✅ GitHub Actions build başarıyla başlatıldı!");
                $this->info("🔗 Repository URL: " . $result['repo_url']);
                $this->info("⚡ Actions URL: " . $result['actions_url']);
                
                // Uygulama durumunu güncelle
                $app->update([
                    'status' => 'building',
                    'github_repo_url' => $result['repo_url']
                ]);
                
                $this->info("\n📊 Build Takibi:");
                $this->info("1. Repository'yi görüntüle: " . $result['repo_url']);
                $this->info("2. Build durumunu takip et: " . $result['actions_url']);
                $this->info("3. Build tamamlandığında Artifacts bölümünden APK/IPA indir");
                
                $this->info("\n⏱️  Beklenen Build Süreleri:");
                $this->info("• Android: ~8-10 dakika");
                $this->info("• iOS: ~12-15 dakika");
                
                return 0;
            } else {
                $this->error("❌ GitHub Actions build başlatılamadı!");
                $this->error("Hata: " . $result['error']);
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Beklenmeyen hata oluştu!");
            $this->error("Hata mesajı: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}
