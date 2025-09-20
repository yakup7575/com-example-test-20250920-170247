@extends('admin.layout')

@section('title', 'Uygulama Düzenle')
@section('page-title', $app->name . ' - Düzenle')
@section('page-description', 'Uygulama ayarlarını güncelleyin')

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.apps') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Geri Dön
        </a>
        <a href="{{ route('admin.menu-items', $app) }}" class="btn btn-outline-info">
            <i class="fas fa-bars me-2"></i>
            Menüler ({{ $app->menuItems->count() }})
        </a>
        <a href="{{ route('admin.apps.generate-flutter', $app) }}" class="btn btn-outline-success">
            <i class="fas fa-download me-2"></i>
            Flutter Kodu İndir
        </a>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.apps.update', $app) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Temel Bilgiler -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Temel Bilgiler
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Uygulama Adı *</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $app->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="package_name" class="form-label">Paket Adı (Android) *</label>
                                    <input type="text" 
                                           class="form-control @error('package_name') is-invalid @enderror" 
                                           id="package_name" 
                                           name="package_name" 
                                           value="{{ old('package_name', $app->package_name) }}" 
                                           placeholder="com.example.myapp"
                                           required>
                                    @error('package_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Örnek: com.sirketadi.uygulamaadi</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bundle_id" class="form-label">Bundle ID (iOS)</label>
                                    <input type="text" 
                                           class="form-control @error('bundle_id') is-invalid @enderror" 
                                           id="bundle_id" 
                                           name="bundle_id" 
                                           value="{{ old('bundle_id', $app->bundle_id) }}" 
                                           placeholder="com.example.myapp">
                                    @error('bundle_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Boş bırakılırsa paket adı kullanılır</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="website_url" class="form-label">Website URL *</label>
                                    <input type="url" 
                                           class="form-control @error('website_url') is-invalid @enderror" 
                                           id="website_url" 
                                           name="website_url" 
                                           value="{{ old('website_url', $app->website_url) }}" 
                                           placeholder="https://example.com"
                                           required>
                                    @error('website_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Uygulamanızın kısa açıklaması">{{ old('description', $app->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Platform ve Renk Ayarları -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-palette me-2"></i>
                            Platform ve Görünüm
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="platform" class="form-label">Platform *</label>
                                    <select class="form-select @error('platform') is-invalid @enderror" 
                                            id="platform" 
                                            name="platform" 
                                            required>
                                        <option value="both" {{ old('platform', $app->platform) === 'both' ? 'selected' : '' }}>
                                            Her İki Platform (Android + iOS)
                                        </option>
                                        <option value="android" {{ old('platform', $app->platform) === 'android' ? 'selected' : '' }}>
                                            Sadece Android
                                        </option>
                                        <option value="ios" {{ old('platform', $app->platform) === 'ios' ? 'selected' : '' }}>
                                            Sadece iOS
                                        </option>
                                    </select>
                                    @error('platform')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Durum *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="draft" {{ old('status', $app->status) === 'draft' ? 'selected' : '' }}>
                                            Taslak
                                        </option>
                                        <option value="building" {{ old('status', $app->status) === 'building' ? 'selected' : '' }}>
                                            Derleniyor
                                        </option>
                                        <option value="completed" {{ old('status', $app->status) === 'completed' ? 'selected' : '' }}>
                                            Tamamlandı
                                        </option>
                                        <option value="published" {{ old('status', $app->status) === 'published' ? 'selected' : '' }}>
                                            Yayınlandı
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">Ana Renk *</label>
                                    <input type="color" 
                                           class="form-control form-control-color @error('primary_color') is-invalid @enderror" 
                                           id="primary_color" 
                                           name="primary_color" 
                                           value="{{ old('primary_color', $app->primary_color) }}" 
                                           required>
                                    @error('primary_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="secondary_color" class="form-label">İkincil Renk *</label>
                                    <input type="color" 
                                           class="form-control form-control-color @error('secondary_color') is-invalid @enderror" 
                                           id="secondary_color" 
                                           name="secondary_color" 
                                           value="{{ old('secondary_color', $app->secondary_color) }}" 
                                           required>
                                    @error('secondary_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Build Bilgileri -->
                @if($app->codemagic_build_id || $app->android_build_url || $app->ios_build_url)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Build Bilgileri
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($app->codemagic_build_id)
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Codemagic Build ID</label>
                                    <input type="text" class="form-control" value="{{ $app->codemagic_build_id }}" readonly>
                                </div>
                            </div>
                            @endif
                            @if($app->last_build_at)
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Son Build</label>
                                    <input type="text" class="form-control" value="{{ $app->last_build_at->diffForHumans() }}" readonly>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        @if($app->android_build_url || $app->ios_build_url)
                        <div class="row">
                            @if($app->android_build_url)
                            <div class="col-md-6">
                                <a href="{{ $app->android_build_url }}" class="btn btn-success w-100" target="_blank">
                                    <i class="fab fa-android me-2"></i>
                                    Android APK İndir
                                </a>
                            </div>
                            @endif
                            @if($app->ios_build_url)
                            <div class="col-md-6">
                                <a href="{{ $app->ios_build_url }}" class="btn btn-secondary w-100" target="_blank">
                                    <i class="fab fa-apple me-2"></i>
                                    iOS IPA İndir
                                </a>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            
            <div class="col-lg-4">
                <!-- Mevcut Görseller -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-images me-2"></i>
                            Mevcut Görseller
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label">Uygulama İkonu</label>
                                @if($app->app_icon)
                                    <div class="text-center mb-3">
                                        <img src="{{ Storage::url($app->app_icon) }}" 
                                             alt="App Icon" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                @else
                                    <div class="text-center mb-3">
                                        <div class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-6">
                                <label class="form-label">Splash Ekranı</label>
                                @if($app->splash_image)
                                    <div class="text-center mb-3">
                                        <img src="{{ Storage::url($app->splash_image) }}" 
                                             alt="Splash Image" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    </div>
                                @else
                                    <div class="text-center mb-3">
                                        <div class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Yeni Görseller -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-upload me-2"></i>
                            Görselleri Güncelle
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="app_icon" class="form-label">Yeni Uygulama İkonu</label>
                            <input type="file" 
                                   class="form-control @error('app_icon') is-invalid @enderror" 
                                   id="app_icon" 
                                   name="app_icon" 
                                   accept="image/png,image/jpeg">
                            @error('app_icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">PNG veya JPEG, maksimum 2MB</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="splash_image" class="form-label">Yeni Splash Ekranı</label>
                            <input type="file" 
                                   class="form-control @error('splash_image') is-invalid @enderror" 
                                   id="splash_image" 
                                   name="splash_image" 
                                   accept="image/png,image/jpeg">
                            @error('splash_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">PNG veya JPEG, maksimum 2MB</div>
                        </div>
                    </div>
                </div>

                <!-- OneSignal Ayarları -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bell me-2"></i>
                            Bildirim Ayarları
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="onesignal_app_id" class="form-label">OneSignal App ID</label>
                            <input type="text" 
                                   class="form-control @error('onesignal_app_id') is-invalid @enderror" 
                                   id="onesignal_app_id" 
                                   name="onesignal_app_id" 
                                   value="{{ old('onesignal_app_id', $app->onesignal_app_id) }}" 
                                   placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                            @error('onesignal_app_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="onesignal_api_key" class="form-label">OneSignal API Key</label>
                            <input type="password" 
                                   class="form-control @error('onesignal_api_key') is-invalid @enderror" 
                                   id="onesignal_api_key" 
                                   name="onesignal_api_key" 
                                   value="{{ old('onesignal_api_key', $app->onesignal_api_key) }}" 
                                   placeholder="API anahtarınız">
                            @error('onesignal_api_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- İstatistikler -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            İstatistikler
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="text-primary">{{ $app->menuItems->count() }}</h4>
                                    <small class="text-muted">Menü Öğesi</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info">{{ $app->configurations->count() }}</h4>
                                <small class="text-muted">Konfigürasyon</small>
                            </div>
                        </div>
                        <hr>
                        <div class="text-center">
                            <small class="text-muted">
                                Oluşturulma: {{ $app->created_at->format('d.m.Y H:i') }}<br>
                                Son Güncelleme: {{ $app->updated_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="{{ route('admin.apps') }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-2"></i>
                            İptal
                        </a>
                        @if($app->status === 'draft')
                            <button type="button" class="btn btn-outline-warning" onclick="buildApp()">
                                <i class="fas fa-play me-2"></i>
                                Derle
                            </button>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Değişiklikleri Kaydet
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    function buildApp() {
        if (confirm('Bu uygulamayı derlemek istediğinizden emin misiniz?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.apps.build", $app) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Dosya yükleme önizleme
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                if (preview) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    document.getElementById('app_icon').addEventListener('change', function() {
        previewImage(this, 'app_icon_preview');
    });
    
    document.getElementById('splash_image').addEventListener('change', function() {
        previewImage(this, 'splash_image_preview');
    });
</script>
@endsection
