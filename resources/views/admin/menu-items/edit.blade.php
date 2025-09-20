@extends('admin.layout')

@section('title', 'Menü Öğesi Düzenle')
@section('page-title', $app->name . ' - Menü Düzenle')
@section('page-description', $menuItem->title . ' menü öğesini düzenleyin')

@section('page-actions')
    <a href="{{ route('admin.menu-items', $app) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>
        Menülere Dön
    </a>
@endsection

@section('content')
    <!-- Uygulama Bilgisi -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                @if($app->app_icon)
                    <img src="{{ Storage::url($app->app_icon) }}" 
                         alt="{{ $app->name }}" 
                         class="rounded me-3" 
                         style="width: 50px; height: 50px; object-fit: cover;">
                @else
                    <div class="bg-primary rounded d-flex align-items-center justify-content-center me-3" 
                         style="width: 50px; height: 50px;">
                        <i class="fas fa-mobile-alt text-white"></i>
                    </div>
                @endif
                <div>
                    <h6 class="mb-0">{{ $app->name }}</h6>
                    <small class="text-muted">{{ $app->package_name }}</small>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.menu-items.update', [$app, $menuItem]) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Temel Bilgiler -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Menü Öğesi Bilgileri
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Menü Başlığı *</label>
                                    <input type="text" 
                                           class="form-control @error('title') is-invalid @enderror" 
                                           id="title" 
                                           name="title" 
                                           value="{{ old('title', $menuItem->title) }}" 
                                           placeholder="Ana Sayfa"
                                           required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Bottom navigation'da görünecek metin</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="url" class="form-label">URL *</label>
                                    <input type="url" 
                                           class="form-control @error('url') is-invalid @enderror" 
                                           id="url" 
                                           name="url" 
                                           value="{{ old('url', $menuItem->url) }}" 
                                           placeholder="https://example.com/page"
                                           required>
                                    @error('url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Menü öğesine tıklandığında açılacak sayfa</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="order" class="form-label">Sıra *</label>
                                    <input type="number" 
                                           class="form-control @error('order') is-invalid @enderror" 
                                           id="order" 
                                           name="order" 
                                           value="{{ old('order', $menuItem->order) }}" 
                                           min="0"
                                           required>
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Menüde görünme sırası (0'dan başlar)</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="target" class="form-label">Hedef *</label>
                                    <select class="form-select @error('target') is-invalid @enderror" 
                                            id="target" 
                                            name="target" 
                                            required>
                                        <option value="_self" {{ old('target', $menuItem->target) === '_self' ? 'selected' : '' }}>
                                            Aynı Pencerede (_self)
                                        </option>
                                        <option value="_blank" {{ old('target', $menuItem->target) === '_blank' ? 'selected' : '' }}>
                                            Yeni Pencerede (_blank)
                                        </option>
                                    </select>
                                    @error('target')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <div class="form-check form-switch mt-4">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               {{ old('is_active', $menuItem->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Aktif
                                        </label>
                                    </div>
                                    <div class="form-text">Pasif öğeler uygulamada görünmez</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- İkon Ayarları -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-icons me-2"></i>
                            İkon Ayarları
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon_type" class="form-label">İkon Tipi *</label>
                                    <select class="form-select @error('icon_type') is-invalid @enderror" 
                                            id="icon_type" 
                                            name="icon_type" 
                                            required>
                                        <option value="material" {{ old('icon_type', $menuItem->icon_type) === 'material' ? 'selected' : '' }}>
                                            Material Icons
                                        </option>
                                        <option value="fontawesome" {{ old('icon_type', $menuItem->icon_type) === 'fontawesome' ? 'selected' : '' }}>
                                            Font Awesome
                                        </option>
                                        <option value="custom" {{ old('icon_type', $menuItem->icon_type) === 'custom' ? 'selected' : '' }}>
                                            Özel İkon
                                        </option>
                                    </select>
                                    @error('icon_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">İkon Adı</label>
                                    <input type="text" 
                                           class="form-control @error('icon') is-invalid @enderror" 
                                           id="icon" 
                                           name="icon" 
                                           value="{{ old('icon', $menuItem->icon) }}" 
                                           placeholder="home">
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text" id="icon-help">
                                        Material Icons için: home, search, favorite, person
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Önizleme -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-eye me-2"></i>
                            Önizleme
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="border rounded p-3 mb-3" style="background: #f8f9fa;">
                            <div id="icon-preview" class="mb-2" style="font-size: 24px; color: {{ $app->primary_color }};">
                                @switch($menuItem->icon_type)
                                    @case('material')
                                        <i class="fas fa-{{ $menuItem->icon ?: 'circle' }}"></i>
                                        @break
                                    @case('fontawesome')
                                        <i class="{{ $menuItem->icon ?: 'fas fa-circle' }}"></i>
                                        @break
                                    @default
                                        <i class="fas fa-circle"></i>
                                @endswitch
                            </div>
                            <div id="title-preview" class="small" style="color: #666;">
                                {{ $menuItem->title }}
                            </div>
                        </div>
                        <small class="text-muted">Bottom navigation'da böyle görünecek</small>
                    </div>
                </div>

                <!-- Popüler İkonlar -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>
                            Popüler İkonlar
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2" id="popular-icons">
                            <!-- Material Icons -->
                            <div class="col-3 text-center popular-icon" data-icon="home" data-type="material">
                                <div class="border rounded p-2" style="cursor: pointer;">
                                    <i class="fas fa-home"></i>
                                    <small class="d-block">home</small>
                                </div>
                            </div>
                            <div class="col-3 text-center popular-icon" data-icon="search" data-type="material">
                                <div class="border rounded p-2" style="cursor: pointer;">
                                    <i class="fas fa-search"></i>
                                    <small class="d-block">search</small>
                                </div>
                            </div>
                            <div class="col-3 text-center popular-icon" data-icon="favorite" data-type="material">
                                <div class="border rounded p-2" style="cursor: pointer;">
                                    <i class="fas fa-heart"></i>
                                    <small class="d-block">favorite</small>
                                </div>
                            </div>
                            <div class="col-3 text-center popular-icon" data-icon="person" data-type="material">
                                <div class="border rounded p-2" style="cursor: pointer;">
                                    <i class="fas fa-user"></i>
                                    <small class="d-block">person</small>
                                </div>
                            </div>
                            <div class="col-3 text-center popular-icon" data-icon="settings" data-type="material">
                                <div class="border rounded p-2" style="cursor: pointer;">
                                    <i class="fas fa-cog"></i>
                                    <small class="d-block">settings</small>
                                </div>
                            </div>
                            <div class="col-3 text-center popular-icon" data-icon="info" data-type="material">
                                <div class="border rounded p-2" style="cursor: pointer;">
                                    <i class="fas fa-info"></i>
                                    <small class="d-block">info</small>
                                </div>
                            </div>
                            <div class="col-3 text-center popular-icon" data-icon="phone" data-type="material">
                                <div class="border rounded p-2" style="cursor: pointer;">
                                    <i class="fas fa-phone"></i>
                                    <small class="d-block">phone</small>
                                </div>
                            </div>
                            <div class="col-3 text-center popular-icon" data-icon="email" data-type="material">
                                <div class="border rounded p-2" style="cursor: pointer;">
                                    <i class="fas fa-envelope"></i>
                                    <small class="d-block">email</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menü Bilgileri -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info me-2"></i>
                            Menü Bilgileri
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted d-block">Durum</small>
                            @if($menuItem->is_active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="fas fa-times me-1"></i>
                                    Pasif
                                </span>
                            @endif
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Mevcut Sıra</small>
                            <span class="badge bg-light text-dark">{{ $menuItem->order }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">İkon Tipi</small>
                            <span class="badge bg-info">{{ ucfirst($menuItem->icon_type) }}</span>
                        </div>
                        <hr>
                        <div class="text-center">
                            <small class="text-muted">
                                Oluşturulma: {{ $menuItem->created_at->format('d.m.Y H:i') }}<br>
                                Son Güncelleme: {{ $menuItem->updated_at->diffForHumans() }}
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
                        <a href="{{ route('admin.menu-items', $app) }}" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-2"></i>
                            İptal
                        </a>
                        <a href="{{ $menuItem->url }}" 
                           target="{{ $menuItem->target }}" 
                           class="btn btn-outline-info">
                            <i class="fas fa-external-link-alt me-2"></i>
                            Ziyaret Et
                        </a>
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
    // İkon tipi değiştiğinde yardım metnini güncelle
    document.getElementById('icon_type').addEventListener('change', function() {
        const iconHelp = document.getElementById('icon-help');
        const iconType = this.value;
        
        switch(iconType) {
            case 'material':
                iconHelp.textContent = 'Material Icons için: home, search, favorite, person, settings';
                break;
            case 'fontawesome':
                iconHelp.textContent = 'Font Awesome için: fas fa-home, fab fa-facebook, far fa-heart';
                break;
            case 'custom':
                iconHelp.textContent = 'Özel ikon URL\'si veya sınıf adı';
                break;
        }
        
        updatePreview();
    });
    
    // Başlık değiştiğinde önizlemeyi güncelle
    document.getElementById('title').addEventListener('input', function() {
        document.getElementById('title-preview').textContent = this.value || 'Menü Başlığı';
    });
    
    // İkon değiştiğinde önizlemeyi güncelle
    document.getElementById('icon').addEventListener('input', function() {
        updatePreview();
    });
    
    // Popüler ikon seçimi
    document.querySelectorAll('.popular-icon').forEach(function(element) {
        element.addEventListener('click', function() {
            const icon = this.dataset.icon;
            const type = this.dataset.type;
            
            document.getElementById('icon').value = icon;
            document.getElementById('icon_type').value = type;
            
            // Seçili görünümü güncelle
            document.querySelectorAll('.popular-icon .border').forEach(el => {
                el.classList.remove('border-primary', 'bg-light');
            });
            this.querySelector('.border').classList.add('border-primary', 'bg-light');
            
            updatePreview();
        });
    });
    
    function updatePreview() {
        const iconType = document.getElementById('icon_type').value;
        const iconName = document.getElementById('icon').value;
        const iconPreview = document.getElementById('icon-preview');
        
        if (!iconName) {
            iconPreview.innerHTML = '<i class="fas fa-circle"></i>';
            return;
        }
        
        let iconClass = '';
        switch(iconType) {
            case 'material':
                iconClass = `fas fa-${iconName}`;
                break;
            case 'fontawesome':
                iconClass = iconName;
                break;
            case 'custom':
                iconClass = iconName;
                break;
        }
        
        iconPreview.innerHTML = `<i class="${iconClass}"></i>`;
    }
    
    // Sayfa yüklendiğinde mevcut ikonu vurgula
    document.addEventListener('DOMContentLoaded', function() {
        const currentIcon = '{{ $menuItem->icon }}';
        const currentType = '{{ $menuItem->icon_type }}';
        
        document.querySelectorAll('.popular-icon').forEach(function(element) {
            if (element.dataset.icon === currentIcon && element.dataset.type === currentType) {
                element.querySelector('.border').classList.add('border-primary', 'bg-light');
            }
        });
        
        updatePreview();
    });
</script>
@endsection
