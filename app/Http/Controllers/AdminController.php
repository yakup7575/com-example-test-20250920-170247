<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $apps = App::with('menuItems')->latest()->get();
        $totalApps = App::count();
        $publishedApps = App::where('status', 'published')->count();
        $draftApps = App::where('status', 'draft')->count();
        
        return view('admin.dashboard', compact('apps', 'totalApps', 'publishedApps', 'draftApps'));
    }

    public function apps()
    {
        $apps = App::with('menuItems')->latest()->paginate(10);
        return view('admin.apps.index', compact('apps'));
    }

    public function createApp()
    {
        return view('admin.apps.create');
    }

    public function storeApp(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'package_name' => 'required|string|unique:apps,package_name',
            'bundle_id' => 'nullable|string',
            'description' => 'nullable|string',
            'website_url' => 'required|url',
            'app_icon' => 'nullable|image|max:2048',
            'splash_image' => 'nullable|image|max:2048',
            'primary_color' => 'required|string',
            'secondary_color' => 'required|string',
            'platform' => 'required|in:android,ios,both',
            'onesignal_app_id' => 'nullable|string',
            'onesignal_api_key' => 'nullable|string',
        ]);

        if ($request->hasFile('app_icon')) {
            $validated['app_icon'] = $request->file('app_icon')->store('app-icons', 'public');
        }

        if ($request->hasFile('splash_image')) {
            $validated['splash_image'] = $request->file('splash_image')->store('splash-images', 'public');
        }

        $app = App::create($validated);

        return redirect()->route('admin.apps')->with('success', 'Uygulama başarıyla oluşturuldu!');
    }

    public function editApp(App $app)
    {
        return view('admin.apps.edit', compact('app'));
    }

    public function updateApp(Request $request, App $app)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'package_name' => 'required|string|unique:apps,package_name,' . $app->id,
            'bundle_id' => 'nullable|string',
            'description' => 'nullable|string',
            'website_url' => 'required|url',
            'app_icon' => 'nullable|image|max:2048',
            'splash_image' => 'nullable|image|max:2048',
            'primary_color' => 'required|string',
            'secondary_color' => 'required|string',
            'platform' => 'required|in:android,ios,both',
            'status' => 'required|in:draft,building,completed,published',
            'onesignal_app_id' => 'nullable|string',
            'onesignal_api_key' => 'nullable|string',
        ]);

        if ($request->hasFile('app_icon')) {
            if ($app->app_icon) {
                Storage::disk('public')->delete($app->app_icon);
            }
            $validated['app_icon'] = $request->file('app_icon')->store('app-icons', 'public');
        }

        if ($request->hasFile('splash_image')) {
            if ($app->splash_image) {
                Storage::disk('public')->delete($app->splash_image);
            }
            $validated['splash_image'] = $request->file('splash_image')->store('splash-images', 'public');
        }

        $app->update($validated);

        return redirect()->route('admin.apps')->with('success', 'Uygulama başarıyla güncellendi!');
    }

    public function deleteApp(App $app)
    {
        if ($app->app_icon) {
            Storage::disk('public')->delete($app->app_icon);
        }
        if ($app->splash_image) {
            Storage::disk('public')->delete($app->splash_image);
        }

        $app->delete();

        return redirect()->route('admin.apps')->with('success', 'Uygulama başarıyla silindi!');
    }

    public function menuItems(App $app)
    {
        $menuItems = $app->menuItems()->ordered()->get();
        return view('admin.menu-items.index', compact('app', 'menuItems'));
    }

    public function createMenuItem(App $app)
    {
        return view('admin.menu-items.create', compact('app'));
    }

    public function storeMenuItem(Request $request, App $app)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'icon' => 'nullable|string',
            'icon_type' => 'required|in:material,fontawesome,custom',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'target' => 'required|in:_self,_blank',
        ]);

        $validated['app_id'] = $app->id;
        $validated['is_active'] = $request->has('is_active');

        MenuItem::create($validated);

        return redirect()->route('admin.menu-items', $app)->with('success', 'Menü öğesi başarıyla oluşturuldu!');
    }

    public function editMenuItem(App $app, MenuItem $menuItem)
    {
        return view('admin.menu-items.edit', compact('app', 'menuItem'));
    }

    public function updateMenuItem(Request $request, App $app, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'icon' => 'nullable|string',
            'icon_type' => 'required|in:material,fontawesome,custom',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'target' => 'required|in:_self,_blank',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $menuItem->update($validated);

        return redirect()->route('admin.menu-items', $app)->with('success', 'Menü öğesi başarıyla güncellendi!');
    }

    public function deleteMenuItem(App $app, MenuItem $menuItem)
    {
        $menuItem->delete();

        return redirect()->route('admin.menu-items', $app)->with('success', 'Menü öğesi başarıyla silindi!');
    }

    public function buildApp(App $app)
    {
        // Codemagic.io API entegrasyonu burada yapılacak
        $app->update(['status' => 'building']);
        
        return redirect()->route('admin.apps')->with('success', 'Uygulama derleme işlemi başlatıldı!');
    }

    public function generateFlutter(App $app)
    {
        try {
            $generator = new \App\Services\FlutterCodeGenerator($app);
            $zipPath = $generator->generate();
            
            if ($zipPath) {
                return response()->download(storage_path('app/public/' . $zipPath));
            } else {
                return response()->json(['error' => 'Flutter kodu oluşturulamadı'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Hata: ' . $e->getMessage()], 500);
        }
    }
}
