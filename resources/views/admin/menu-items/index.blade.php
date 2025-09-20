@extends('admin.layout')

@section('title', 'Menü Öğeleri')
@section('page-title', $app->name . ' - Menü Öğeleri')
@section('page-description', 'Bottom navigation menü öğelerini yönetin')

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.apps.edit', $app) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Uygulamaya Dön
        </a>
        <a href="{{ route('admin.menu-items.create', $app) }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Yeni Menü Öğesi
        </a>
    </div>
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
                         style="width: 60px; height: 60px; object-fit: cover;">
                @else
                    <div class="bg-primary rounded d-flex align-items-center justify-content-center me-3" 
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-mobile-alt text-white fa-lg"></i>
                    </div>
                @endif
                <div class="flex-grow-1">
                    <h5 class="mb-1">{{ $app->name }}</h5>
                    <p class="text-muted mb-1">{{ $app->package_name }}</p>
                    <small class="text-muted">{{ $app->website_url }}</small>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-dark fs-6">
                        {{ $menuItems->count() }} menü öğesi
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Menü Öğeleri -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-bars me-2"></i>
                Bottom Navigation Menüleri
            </h5>
        </div>
        <div class="card-body">
            @if($menuItems->count() > 0)
                <div class="row" id="menu-items-container">
                    @foreach($menuItems as $item)
                        <div class="col-md-6 col-lg-4 mb-3" data-order="{{ $item->order }}">
                            <div class="card h-100 {{ $item->is_active ? 'border-success' : 'border-secondary' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                @switch($item->icon_type)
                                                    @case('material')
                                                        <i class="fas fa-{{ $item->icon ?: 'circle' }} text-primary"></i>
                                                        @break
                                                    @case('fontawesome')
                                                        <i class="{{ $item->icon ?: 'fas fa-circle' }} text-primary"></i>
                                                        @break
                                                    @default
                                                        <i class="fas fa-circle text-primary"></i>
                                                @endswitch
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $item->title }}</h6>
                                                <small class="text-muted">Sıra: {{ $item->order }}</small>
                                            </div>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" 
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" 
                                                       href="{{ route('admin.menu-items.edit', [$app, $item]) }}">
                                                        <i class="fas fa-edit me-2"></i>
                                                        Düzenle
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" 
                                                       href="{{ $item->url }}" 
                                                       target="{{ $item->target }}">
                                                        <i class="fas fa-external-link-alt me-2"></i>
                                                        Ziyaret Et
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.menu-items.delete', [$app, $item]) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Bu menü öğesini silmek istediğinizden emin misiniz?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i>
                                                            Sil
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted d-block">URL:</small>
                                        <code class="small">{{ Str::limit($item->url, 40) }}</code>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            @if($item->is_active)
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
                                            
                                            <span class="badge bg-light text-dark ms-1">
                                                {{ ucfirst($item->icon_type) }}
                                            </span>
                                        </div>
                                        
                                        <div class="btn-group btn-group-sm">
                                            @if(!$loop->first)
                                                <button type="button" 
                                                        class="btn btn-outline-secondary" 
                                                        onclick="moveItem({{ $item->id }}, 'up')"
                                                        title="Yukarı Taşı">
                                                    <i class="fas fa-arrow-up"></i>
                                                </button>
                                            @endif
                                            @if(!$loop->last)
                                                <button type="button" 
                                                        class="btn btn-outline-secondary" 
                                                        onclick="moveItem({{ $item->id }}, 'down')"
                                                        title="Aşağı Taşı">
                                                    <i class="fas fa-arrow-down"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Önizleme -->
                <div class="mt-4">
                    <h6 class="mb-3">
                        <i class="fas fa-eye me-2"></i>
                        Bottom Navigation Önizlemesi
                    </h6>
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-around align-items-center" 
                             style="background: white; border-radius: 25px; padding: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            @foreach($menuItems->where('is_active', true)->take(5) as $item)
                                <div class="text-center" style="min-width: 60px;">
                                    <div class="mb-1" style="color: {{ $app->primary_color }};">
                                        @switch($item->icon_type)
                                            @case('material')
                                                <i class="fas fa-{{ $item->icon ?: 'circle' }}"></i>
                                                @break
                                            @case('fontawesome')
                                                <i class="{{ $item->icon ?: 'fas fa-circle' }}"></i>
                                                @break
                                            @default
                                                <i class="fas fa-circle"></i>
                                        @endswitch
                                    </div>
                                    <small style="font-size: 10px; color: #666;">
                                        {{ Str::limit($item->title, 8) }}
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bars fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz menü öğesi yok</h5>
                    <p class="text-muted">Bottom navigation için menü öğeleri oluşturun</p>
                    <a href="{{ route('admin.menu-items.create', $app) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        İlk Menü Öğesini Oluştur
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Bilgi Kutusu -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        Bottom Navigation Hakkında
                    </h6>
                    <ul class="list-unstyled small text-muted">
                        <li><i class="fas fa-check text-success me-2"></i>Maksimum 5 menü öğesi önerilir</li>
                        <li><i class="fas fa-check text-success me-2"></i>Sıralama önemlidir</li>
                        <li><i class="fas fa-check text-success me-2"></i>Sadece aktif öğeler görünür</li>
                        <li><i class="fas fa-check text-success me-2"></i>İkonlar Material Design uyumludur</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-info">
                        <i class="fas fa-palette me-2"></i>
                        Renk Ayarları
                    </h6>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <small class="text-muted d-block">Ana Renk</small>
                            <div class="d-flex align-items-center">
                                <div class="rounded me-2" 
                                     style="width: 20px; height: 20px; background: {{ $app->primary_color }};"></div>
                                <code class="small">{{ $app->primary_color }}</code>
                            </div>
                        </div>
                        <div>
                            <small class="text-muted d-block">İkincil Renk</small>
                            <div class="d-flex align-items-center">
                                <div class="rounded me-2" 
                                     style="width: 20px; height: 20px; background: {{ $app->secondary_color }};"></div>
                                <code class="small">{{ $app->secondary_color }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function moveItem(itemId, direction) {
        // AJAX ile menü öğesi sırasını değiştir
        fetch(`/admin/menu-items/${itemId}/move`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                direction: direction
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Sıralama değiştirilemedi');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
    }
</script>
@endsection
