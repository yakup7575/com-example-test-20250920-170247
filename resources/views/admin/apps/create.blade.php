@extends('admin.layout')

@section('title', 'Yeni Uygulama')
@section('page-title', 'Yeni Uygulama Oluştur')
@section('page-description', 'Flutter webview uygulamanızı oluşturun')

@section('page-actions')
    <a href="{{ route('admin.apps') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>
        Geri Dön
    </a>
@endsection

@section('content')
    <form action="{{ route('admin.apps.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
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
                                           value="{{ old('name') }}" 
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
                                           value="{{ old('package_name') }}" 
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
                                           value="{{ old('bundle_id') }}" 
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
                                           value="{{ old('website_url') }}" 
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
                                      placeholder="Uygulamanızın kısa açıklaması">{{ old('description') }}</textarea>
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
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="platform" class="form-label">Platform *</label>
                                    <select class="form-select @error('platform') is-invalid @enderror" 
                                            id="platform" 
                                            name="platform" 
                                            required>
                                        <option value="both" {{ old('platform') === 'both' ? 'selected' : '' }}>
                                            Her İki Platform (Android + iOS)
                                        </option>
                                        <option value="android" {{ old('platform') === 'android' ? 'selected' : '' }}>
                                            Sadece Android
                                        </option>
                                        <option value="ios" {{ old('platform') === 'ios' ? 'selected' : '' }}>
                                            Sadece iOS
                                        </option>
                                    </select>
                                    @error('platform')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="primary_color" class="form-label">Ana Renk *</label>
                                    <input type="color" 
                                           class="form-control form-control-color @error('primary_color') is-invalid @enderror" 
                                           id="primary_color" 
                                           name="primary_color" 
                                           value="{{ old('primary_color', '#2196F3') }}" 
                                           required>
                                    @error('primary_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="secondary_color" class="form-label">İkincil Renk *</label>
                                    <input type="color" 
                                           class="form-control form-control-color @error('secondary_color') is-invalid @enderror" 
                                           id="secondary_color" 
                                           name="secondary_color" 
                                           value="{{ old('secondary_color', '#FFC107') }}" 
                                           required>
                                    @error('secondary_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Görseller -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-images me-2"></i>
                            Uygulama Görselleri
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="app_icon" class="form-label">Uygulama İkonu</label>
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
                            <label for="splash_image" class="form-label">Splash Ekranı</label>
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
                                   value="{{ old('onesignal_app_id') }}" 
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
                                   value="{{ old('onesignal_api_key') }}" 
                                   placeholder="API anahtarınız">
                            @error('onesignal_api_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle me-1"></i>
                                OneSignal hesabınızdan App ID ve API Key'i alabilirsiniz.
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
                    <a href="{{ route('admin.apps') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>
                        İptal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>
                        Uygulamayı Oluştur
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    // Uygulama adından otomatik paket adı oluştur
    document.getElementById('name').addEventListener('input', function() {
        const name = this.value.toLowerCase()
            .replace(/[^a-z0-9\s]/g, '')
            .replace(/\s+/g, '');
        
        if (name && !document.getElementById('package_name').value) {
            document.getElementById('package_name').value = `com.example.${name}`;
        }
    });
    
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
