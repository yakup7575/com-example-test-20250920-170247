# 🚀 GitHub Actions ile Ücretsiz Flutter Build Sistemi

Bu sistem, Flutter webview uygulamalarınızı GitHub Actions kullanarak **tamamen ücretsiz** olarak derleyebilmenizi sağlar.

## 📋 Gereksinimler

### 1. GitHub Hesabı ve Token
- GitHub hesabınız olmalı
- Personal Access Token oluşturmalısınız

### 2. GitHub Token Oluşturma
1. GitHub'da **Settings** > **Developer settings** > **Personal access tokens** > **Tokens (classic)**
2. **Generate new token (classic)** butonuna tıklayın
3. Aşağıdaki izinleri verin:
   - `repo` (Full control of private repositories)
   - `workflow` (Update GitHub Action workflows)
   - `write:packages` (Upload packages to GitHub Package Registry)

### 3. Sistem Konfigürasyonu
`.env` dosyanızda aşağıdaki değerleri ayarlayın:

```env
GITHUB_TOKEN=ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
GITHUB_USERNAME=your-github-username
```

## 🔧 Nasıl Çalışır?

### 1. Otomatik Repository Oluşturma
- Sistem her uygulama için otomatik GitHub repository oluşturur
- Repository adı: `com-example-myapp` formatında

### 2. GitHub Actions Workflow'ları
Sistem otomatik olarak 2 workflow dosyası oluşturur:

#### Android Build (`.github/workflows/android.yml`)
- **Runner**: Ubuntu (ücretsiz)
- **Çıktılar**: APK ve AAB dosyaları
- **Süre**: ~5-10 dakika

#### iOS Build (`.github/workflows/ios.yml`)
- **Runner**: macOS (ücretsiz)
- **Çıktılar**: IPA dosyası (code signing olmadan)
- **Süre**: ~10-15 dakika

### 3. Build Süreci
1. Flutter projesi generate edilir
2. GitHub repository oluşturulur
3. Kod GitHub'a push edilir
4. GitHub Actions otomatik başlar
5. Build tamamlandığında artifacts hazır olur

## 📱 Build Çıktıları

### Android
- **APK**: Test ve development için
- **AAB**: Google Play Store yüklemesi için

### iOS
- **IPA**: Development/test için (code signing gerekli değil)
- App Store yüklemesi için Xcode ile code signing gerekli

## 🎯 Kullanım Adımları

### 1. Admin Panelinden
1. **Uygulamalar** sayfasına gidin
2. Uygulamanızın yanındaki **GitHub Actions Build** butonuna tıklayın
3. Onay verin ve build başlasın

### 2. Build Takibi
- Build başladığında bildirim alırsınız
- **Repository'yi Görüntüle** linkinden kodu görebilirsiniz
- **Build İlerlemesi** linkinden build durumunu takip edebilirsiniz

### 3. Dosyaları İndirme
Build tamamlandığında:
1. GitHub repository'ye gidin
2. **Actions** sekmesine tıklayın
3. Son build'i seçin
4. **Artifacts** bölümünden dosyaları indirin

## 💰 Maliyet Analizi

### GitHub Actions Ücretsiz Limitler
- **Public repositories**: Sınırsız
- **Private repositories**: Aylık 2000 dakika

### Ortalama Build Süreleri
- **Android**: ~8 dakika
- **iOS**: ~12 dakika
- **Toplam**: ~20 dakika per uygulama

### Aylık Kapasite (Ücretsiz)
- **Public repo**: Sınırsız uygulama
- **Private repo**: ~100 uygulama/ay

## 🔄 Alternatif Ücretsiz Çözümler

### 1. Codemagic (Aylık 500 dakika ücretsiz)
- Daha kolay setup
- Built-in code signing
- Daha az konfigürasyon

### 2. AppCenter (Microsoft)
- Aylık 240 dakika ücretsiz
- Kolay entegrasyon
- Test distribution

### 3. Bitrise (Aylık 200 dakika ücretsiz)
- Profesyonel CI/CD
- Kolay konfigürasyon
- Slack entegrasyonu

## 🛠️ Troubleshooting

### Build Hataları
1. **Token geçersiz**: GitHub token'ınızı kontrol edin
2. **Repository zaten var**: Eski repository'yi silin
3. **Build fail**: GitHub Actions loglarını kontrol edin

### Yaygın Sorunlar
- **Flutter version**: Workflow'da Flutter 3.16.0 kullanılıyor
- **Dependencies**: pubspec.yaml'daki paketler otomatik yüklenir
- **Permissions**: Token izinlerini kontrol edin

## 📞 Destek

Build sorunları için:
1. GitHub Actions loglarını kontrol edin
2. Repository Issues bölümünde sorun bildirin
3. Admin panelinden tekrar build deneyin

## 🎉 Avantajlar

✅ **Tamamen ücretsiz** (public repo için)  
✅ **Otomatik build** sistemi  
✅ **Hem Android hem iOS** desteği  
✅ **Kolay kullanım** (tek tık)  
✅ **Versiyon kontrolü** (Git)  
✅ **Build history** takibi  
✅ **Artifacts** otomatik saklama  

Bu sistem sayesinde Flutter uygulamalarınızı hiçbir maliyet olmadan derleyebilir ve dağıtabilirsiniz! 🚀
