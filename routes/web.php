<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Apps Management
    Route::get('/apps', [AdminController::class, 'apps'])->name('apps');
    Route::get('/apps/create', [AdminController::class, 'createApp'])->name('apps.create');
    Route::post('/apps', [AdminController::class, 'storeApp'])->name('apps.store');
    Route::get('/apps/{app}/edit', [AdminController::class, 'editApp'])->name('apps.edit');
    Route::put('/apps/{app}', [AdminController::class, 'updateApp'])->name('apps.update');
    Route::delete('/apps/{app}', [AdminController::class, 'deleteApp'])->name('apps.delete');
    Route::post('/apps/{app}/build', [AdminController::class, 'buildApp'])->name('apps.build');
    Route::get('/apps/{app}/generate-flutter', [AdminController::class, 'generateFlutter'])->name('apps.generate-flutter');
    
    // Menu Items Management
    Route::get('/apps/{app}/menu-items', [AdminController::class, 'menuItems'])->name('menu-items');
    Route::get('/apps/{app}/menu-items/create', [AdminController::class, 'createMenuItem'])->name('menu-items.create');
    Route::post('/apps/{app}/menu-items', [AdminController::class, 'storeMenuItem'])->name('menu-items.store');
    Route::get('/apps/{app}/menu-items/{menuItem}/edit', [AdminController::class, 'editMenuItem'])->name('menu-items.edit');
    Route::put('/apps/{app}/menu-items/{menuItem}', [AdminController::class, 'updateMenuItem'])->name('menu-items.update');
    Route::delete('/apps/{app}/menu-items/{menuItem}', [AdminController::class, 'deleteMenuItem'])->name('menu-items.delete');
});

// API Routes for Flutter Apps
Route::prefix('api')->group(function () {
    Route::get('/app/{package_name}', function ($packageName) {
        $app = \App\Models\App::where('package_name', $packageName)->with('menuItems')->first();
        
        if (!$app) {
            return response()->json(['error' => 'App not found'], 404);
        }
        
        return response()->json([
            'app' => $app,
            'menu_items' => $app->menuItems()->active()->ordered()->get(),
        ]);
    });
    
    Route::get('/app/{package_name}/config', function ($packageName) {
        $app = \App\Models\App::where('package_name', $packageName)->with('configurations')->first();
        
        if (!$app) {
            return response()->json(['error' => 'App not found'], 404);
        }
        
        $config = [];
        foreach ($app->configurations as $configuration) {
            $config[$configuration->key] = $configuration->typed_value;
        }
        
        return response()->json($config);
    });
});
