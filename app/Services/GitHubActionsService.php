<?php

namespace App\Services;

use App\Models\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubActionsService
{
    protected $githubToken;
    protected $githubUsername;
    protected $baseRepoName = 'flutter-app-builder';

    public function __construct()
    {
        $this->githubToken = config('services.github.token');
        $this->githubUsername = config('services.github.username');
    }

    /**
     * GitHub repository oluştur ve Flutter projesini push et
     */
    public function createAndBuildApp(App $app)
    {
        try {
            // 1. Repository oluştur
            $repoName = $this->sanitizeRepoName($app->package_name);
            $repo = $this->createRepository($repoName, $app);
            
            if (!$repo) {
                throw new \Exception('Repository oluşturulamadı');
            }

            // 2. Flutter projesini generate et
            $generator = new FlutterCodeGenerator($app);
            $zipPath = $generator->generate();
            
            if (!$zipPath) {
                throw new \Exception('Flutter projesi oluşturulamadı');
            }

            // 3. GitHub Actions workflow dosyalarını oluştur
            $this->createWorkflowFiles($app);

            // 4. Projeyi GitHub'a push et
            $this->pushToGitHub($app, $repoName);

            // 5. Build'i tetikle
            $this->triggerBuild($repoName);

            return [
                'success' => true,
                'repo_url' => "https://github.com/{$this->githubUsername}/{$repoName}",
                'actions_url' => "https://github.com/{$this->githubUsername}/{$repoName}/actions"
            ];

        } catch (\Exception $e) {
            Log::error('GitHub Actions build hatası: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Repository adını temizle
     */
    protected function sanitizeRepoName($packageName)
    {
        $baseName = strtolower(str_replace(['.', '_'], '-', $packageName));
        $timestamp = date('Ymd-His');
        return $baseName . '-' . $timestamp;
    }

    /**
     * GitHub repository oluştur
     */
    protected function createRepository($repoName, App $app)
    {
        $response = Http::withOptions([
            'verify' => false, // SSL doğrulamasını devre dışı bırak
        ])->withHeaders([
            'Authorization' => 'token ' . $this->githubToken,
            'Accept' => 'application/vnd.github.v3+json',
        ])->post('https://api.github.com/user/repos', [
            'name' => $repoName,
            'description' => $app->description ?: "Flutter webview app: {$app->name}",
            'private' => false, // Public repo (ücretsiz build için)
            'auto_init' => true,
            'gitignore_template' => 'Dart'
        ]);

        if (!$response->successful()) {
            Log::error('GitHub repository oluşturma hatası', [
                'status' => $response->status(),
                'body' => $response->body(),
                'repo_name' => $repoName
            ]);
            throw new \Exception('GitHub API Hatası: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * GitHub Actions workflow dosyalarını oluştur
     */
    protected function createWorkflowFiles(App $app)
    {
        $workflowDir = storage_path('app/flutter-projects/' . $app->package_name . '/.github/workflows');
        
        if (!file_exists($workflowDir)) {
            mkdir($workflowDir, 0755, true);
        }

        // Android build workflow
        $this->createAndroidWorkflow($workflowDir, $app);
        
        // iOS build workflow (macOS runner gerekli)
        $this->createIOSWorkflow($workflowDir, $app);
    }

    /**
     * Android build workflow oluştur
     */
    protected function createAndroidWorkflow($workflowDir, App $app)
    {
        $content = <<<YAML
name: Build Android APK

on:
  push:
    branches: [ main, master ]
  pull_request:
    branches: [ main, master ]
  workflow_dispatch:

jobs:
  build-android:
    runs-on: ubuntu-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v4
      
    - name: Setup Java
      uses: actions/setup-java@v4
      with:
        distribution: 'zulu'
        java-version: '17'
        
    - name: Setup Flutter
      uses: subosito/flutter-action@v2
      with:
        flutter-version: '3.16.0'
        channel: 'stable'
        
    - name: Get dependencies
      run: flutter pub get
      
    - name: Analyze code
      run: flutter analyze
      
    - name: Run tests
      run: flutter test
      
    - name: Build APK
      run: flutter build apk --release
      
    - name: Build App Bundle
      run: flutter build appbundle --release
      
    - name: Upload APK artifact
      uses: actions/upload-artifact@v4
      with:
        name: android-apk
        path: build/app/outputs/flutter-apk/app-release.apk
        
    - name: Upload AAB artifact
      uses: actions/upload-artifact@v4
      with:
        name: android-aab
        path: build/app/outputs/bundle/release/app-release.aab

    - name: Create Release
      if: github.ref == 'refs/heads/main' || github.ref == 'refs/heads/master'
      uses: softprops/action-gh-release@v1
      with:
        tag_name: v1.0.\${{ github.run_number }}
        name: Release v1.0.\${{ github.run_number }}
        body: |
          🚀 **{$app->name}** - Otomatik Build
          
          📱 **Android Uygulaması**
          - APK dosyası: Artifacts bölümünden indirin
          - AAB dosyası: Play Store yüklemesi için hazır
          
          🔧 **Build Bilgileri**
          - Flutter Version: 3.16.0
          - Build Number: \${{ github.run_number }}
          - Commit: \${{ github.sha }}
        files: |
          build/app/outputs/flutter-apk/app-release.apk
          build/app/outputs/bundle/release/app-release.aab
        env:
          GITHUB_TOKEN: \${{ secrets.GITHUB_TOKEN }}
YAML;

        file_put_contents($workflowDir . '/android.yml', $content);
    }

    /**
     * iOS build workflow oluştur
     */
    protected function createIOSWorkflow($workflowDir, App $app)
    {
        $content = <<<YAML
name: Build iOS IPA

on:
  push:
    branches: [ main, master ]
  pull_request:
    branches: [ main, master ]
  workflow_dispatch:

jobs:
  build-ios:
    runs-on: macos-latest
    
    steps:
    - name: Checkout code
      uses: actions/checkout@v4
      
    - name: Setup Flutter
      uses: subosito/flutter-action@v2
      with:
        flutter-version: '3.16.0'
        channel: 'stable'
        
    - name: Get dependencies
      run: flutter pub get
      
    - name: Analyze code
      run: flutter analyze
      
    - name: Run tests
      run: flutter test
      
    - name: Build iOS (No Codesign)
      run: |
        flutter build ios --release --no-codesign
        
    - name: Create IPA
      run: |
        cd build/ios/iphoneos
        mkdir -p Payload
        cp -r Runner.app Payload/
        zip -r app-release.ipa Payload/
        
    - name: Upload IPA artifact
      uses: actions/upload-artifact@v4
      with:
        name: ios-ipa
        path: build/ios/iphoneos/app-release.ipa

    - name: Create Release
      if: github.ref == 'refs/heads/main' || github.ref == 'refs/heads/master'
      uses: softprops/action-gh-release@v1
      with:
        tag_name: ios-v1.0.\${{ github.run_number }}
        name: iOS Release v1.0.\${{ github.run_number }}
        body: |
          🍎 **{$app->name}** - iOS Build
          
          📱 **iOS Uygulaması**
          - IPA dosyası: Artifacts bölümünden indirin
          - ⚠️ Not: Code signing yapılmamış, development amaçlı
          
          🔧 **Build Bilgileri**
          - Flutter Version: 3.16.0
          - Build Number: \${{ github.run_number }}
          - Commit: \${{ github.sha }}
        files: |
          build/ios/iphoneos/app-release.ipa
        env:
          GITHUB_TOKEN: \${{ secrets.GITHUB_TOKEN }}
YAML;

        file_put_contents($workflowDir . '/ios.yml', $content);
    }

    /**
     * Projeyi GitHub'a push et
     */
    protected function pushToGitHub(App $app, $repoName)
    {
        $projectPath = storage_path('app/flutter-projects/' . $app->package_name);
        
        // Git komutlarını çalıştır
        $appName = str_replace("'", "\'", $app->name);
        $commands = [
            "cd {$projectPath}",
            "git init",
            "git add .",
            "git commit -m \"Initial commit: {$appName} Flutter app\"",
            "git branch -M main",
            "git remote add origin https://{$this->githubToken}@github.com/{$this->githubUsername}/{$repoName}.git",
            "git push -u origin main --force"
        ];

        foreach ($commands as $command) {
            exec($command, $output, $returnCode);
            if ($returnCode !== 0) {
                throw new \Exception("Git komutu başarısız: {$command}");
            }
        }
    }

    /**
     * Build'i tetikle
     */
    protected function triggerBuild($repoName)
    {
        // Workflow dispatch ile build'i tetikle
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'Authorization' => 'token ' . $this->githubToken,
            'Accept' => 'application/vnd.github.v3+json',
        ])->post("https://api.github.com/repos/{$this->githubUsername}/{$repoName}/actions/workflows/android.yml/dispatches", [
            'ref' => 'main'
        ]);

        return $response->successful();
    }

    /**
     * Build durumunu kontrol et
     */
    public function getBuildStatus($repoName)
    {
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'Authorization' => 'token ' . $this->githubToken,
            'Accept' => 'application/vnd.github.v3+json',
        ])->get("https://api.github.com/repos/{$this->githubUsername}/{$repoName}/actions/runs");

        if ($response->successful()) {
            $runs = $response->json()['workflow_runs'] ?? [];
            return array_slice($runs, 0, 5); // Son 5 build
        }

        return [];
    }
}
