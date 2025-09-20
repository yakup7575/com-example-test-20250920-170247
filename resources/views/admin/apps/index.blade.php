@extends('admin.layout')

@section('title', 'Uygulamalar')
@section('page-title', 'Uygulamalar')
@section('page-description', 'Tüm uygulamalarınızı yönetin')

@section('page-actions')
    <a href="{{ route('admin.apps.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>
        Yeni Uygulama
    </a>
@endsection

@section('content')
    <div class="card">
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
                            @foreach($apps as $app)
                                <tr>
                                    <td>
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
                                                @if($app->description)
                                                    <br><small class="text-muted">{{ Str::limit($app->description, 50) }}</small>
                                                @endif
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
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Yayınlandı
                                                </span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-info">
                                                    <i class="fas fa-check me-1"></i>
                                                    Tamamlandı
                                                </span>
                                                @break
                                            @case('building')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-cog fa-spin me-1"></i>
                                                    Derleniyor
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-edit me-1"></i>
                                                    Taslak
                                                </span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-bars me-1"></i>
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
                                            <a href="{{ route('admin.apps.generate-flutter', $app) }}" 
                                               class="btn btn-outline-success" 
                                               title="Flutter Kodu İndir">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @if($app->status === 'draft')
                                                <form action="{{ route('admin.apps.build', $app) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-outline-warning" 
                                                            title="Derle">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.apps.delete', $app) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Bu uygulamayı silmek istediğinizden emin misiniz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger" 
                                                        title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $apps->links() }}
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
@endsection
