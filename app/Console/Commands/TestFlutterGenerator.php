<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\App;
use App\Services\FlutterCodeGenerator;

class TestFlutterGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:flutter-generator {package_name=com.example.testapp}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Flutter code generator with a specific app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $packageName = $this->argument('package_name');
        
        $this->info("Flutter kod generator test ediliyor...");
        $this->info("Paket adı: {$packageName}");
        
        $app = App::where('package_name', $packageName)->first();
        
        if (!$app) {
            $this->error("Uygulama bulunamadı: {$packageName}");
            return 1;
        }
        
        $this->info("Uygulama bulundu: {$app->name}");
        $this->info("Menü öğesi sayısı: " . $app->menuItems->count());
        
        try {
            $generator = new FlutterCodeGenerator($app);
            $zipPath = $generator->generate();
            
            if ($zipPath) {
                $this->info("✅ Flutter projesi başarıyla oluşturuldu!");
                $this->info("📦 Zip dosyası: {$zipPath}");
                $this->info("📁 Tam yol: " . storage_path('app/public/' . $zipPath));
                
                // Dosya boyutunu göster
                $fullPath = storage_path('app/public/' . $zipPath);
                if (file_exists($fullPath)) {
                    $size = round(filesize($fullPath) / 1024, 2);
                    $this->info("📊 Dosya boyutu: {$size} KB");
                }
                
                return 0;
            } else {
                $this->error("❌ Flutter projesi oluşturulamadı!");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Hata oluştu: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }
}
