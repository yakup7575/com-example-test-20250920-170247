@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-description', 'Uygulama geliştirme platformuna hoş geldiniz!')

@section('page-actions')
    <a href="{{ route('admin.apps.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>
        Yeni Uygulama
    </a>
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-primary mb-2">
                        <i class="fas fa-mobile-alt fa-2x"></i>
                    </div>
                    <h3 class="card-title">{{ $totalApps }}</h3>
                    <p class="card-text text-muted">Toplam Uygulama</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-success mb-2">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <h3 class="card-title">{{ $publishedApps }}</h3>
                    <p class="card-text text-muted">Yayınlanan</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-warning mb-2">
                        <i class="fas fa-edit fa-2x"></i>
                    </div>
                    <h3 class="card-title">{{ $draftApps }}</h3>
                    <p class="card-text text-muted">Taslak</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-info mb-2">
                        <i class="fas fa-cogs fa-2x"></i>
                    </div>
                    <h3 class="card-title">{{ $apps->where('status', 'building')->count() }}</h3>
                    <p class="card-text text-muted">Derleniyor</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Apps -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-clock me-2"></i>
                Son Uygulamalar
            </h5>
            <a href="{{ route('admin.apps') }}" class="btn btn-sm btn-outline-primary">
                Tümünü Gör
            </a>
        </div>
        <div class="card-body">
            @if($apps->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Uygulama</th>
                                <th>Platform</th>
                                <th>Durum</th>
                                <th>Menü Sayısı</th>
                                <th>Oluşturulma</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apps->take(5) as $app)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($app->app_icon)
                                                <img src="{{ Storage::url($app->app_icon) }}" 
                                                     alt="{{ $app->name }}" 
                                                     class="rounded me-3" 
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-primary rounded d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-mobile-alt text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $app->name }}</h6>
                                                <small class="text-muted">{{ $app->package_name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($app->platform === 'both')
                                            <span class="badge bg-success">
                                                <i class="fab fa-android me-1"></i>
                                                <i class="fab fa-apple me-1"></i>
                                                Her İkisi
                                            </span>
                                        @elseif($app->platform === 'android')
                                            <span class="badge bg-success">
                                                <i class="fab fa-android me-1"></i>
                                                Android
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fab fa-apple me-1"></i>
                                                iOS
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($app->status)
                                            @case('published')
                                                <span class="badge bg-success">Yayınlandı</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-info">Tamamlandı</span>
                                                @break
                                            @case('building')
                                                <span class="badge bg-warning">Derleniyor</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">Taslak</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $app->menuItems->count() }} menü
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $app->created_at->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.apps.edit', $app) }}" 
                                               class="btn btn-outline-primary" 
                                               title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.menu-items', $app) }}" 
                                               class="btn btn-outline-info" 
                                               title="Menüler">
                                                <i class="fas fa-bars"></i>
                                            </a>
                                            @if($app->status === 'draft')
                                                <form action="{{ route('admin.apps.build', $app) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-outline-success" 
                                                            title="Derle">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-mobile-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Henüz uygulama yok</h5>
                    <p class="text-muted">İlk uygulamanızı oluşturmak için başlayın!</p>
                    <a href="{{ route('admin.apps.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        İlk Uygulamayı Oluştur
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-3">
                        <i class="fas fa-rocket fa-2x"></i>
                    </div>
                    <h5 class="card-title">Hızlı Başlangıç</h5>
                    <p class="card-text">Yeni bir uygulama oluşturun ve hemen geliştirmeye başlayın.</p>
                    <a href="{{ route('admin.apps.create') }}" class="btn btn-primary">
                        Başla
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-3">
                        <i class="fas fa-book fa-2x"></i>
                    </div>
                    <h5 class="card-title">Dokümantasyon</h5>
                    <p class="card-text">Platform kullanımı hakkında detaylı bilgi alın.</p>
                    <button class="btn btn-info" onclick="alert('Yakında gelecek!')">
                        İncele
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-3">
                        <i class="fas fa-headset fa-2x"></i>
                    </div>
                    <h5 class="card-title">Destek</h5>
                    <p class="card-text">Sorularınız için destek ekibimizle iletişime geçin.</p>
                    <button class="btn btn-success" onclick="alert('Yakında gelecek!')">
                        İletişim
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
