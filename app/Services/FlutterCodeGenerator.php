<?php

namespace App\Services;

use App\Models\App;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class FlutterCodeGenerator
{
    protected $app;
    protected $outputPath;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->outputPath = storage_path('app/flutter-projects/' . $this->app->package_name);
    }

    public function generate()
    {
        // Proje dizinini oluştur
        $this->createProjectStructure();
        
        // Flutter dosyalarını oluştur
        $this->generatePubspecYaml();
        $this->generateMainDart();
        $this->generateConfigDart();
        $this->generateWebViewScreen();
        $this->generateBottomNavigation();
        $this->generateSplashScreen();
        $this->generateAndroidManifest();
        $this->generateIOSInfo();
        
        // Görselleri kopyala
        $this->copyAssets();
        
        // Zip dosyası oluştur
        return $this->createZipFile();
    }

    protected function createProjectStructure()
    {
        $directories = [
            $this->outputPath,
            $this->outputPath . '/lib',
            $this->outputPath . '/lib/screens',
            $this->outputPath . '/lib/services',
            $this->outputPath . '/lib/models',
            $this->outputPath . '/lib/widgets',
            $this->outputPath . '/assets',
            $this->outputPath . '/assets/images',
            $this->outputPath . '/android/app/src/main',
            $this->outputPath . '/ios/Runner',
        ];

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    protected function generatePubspecYaml()
    {
        $content = <<<YAML
name: {$this->app->package_name}
description: {$this->app->description}
version: 1.0.0+1

environment:
  sdk: '>=3.0.0 <4.0.0'

dependencies:
  flutter:
    sdk: flutter
  webview_flutter: ^4.4.2
  http: ^1.1.0
  shared_preferences: ^2.2.2
  onesignal_flutter: ^5.0.4
  url_launcher: ^6.2.1
  connectivity_plus: ^5.0.2
  flutter_launcher_icons: ^0.13.1

dev_dependencies:
  flutter_test:
    sdk: flutter
  flutter_lints: ^3.0.1

flutter:
  uses-material-design: true
  assets:
    - assets/images/

flutter_launcher_icons:
  android: "launcher_icon"
  ios: true
  image_path: "assets/images/app_icon.png"
  min_sdk_android: 21
YAML;

        file_put_contents($this->outputPath . '/pubspec.yaml', $content);
    }

    protected function generateMainDart()
    {
        $content = <<<DART
import 'package:flutter/material.dart';
import 'package:onesignal_flutter/onesignal_flutter.dart';
import 'screens/splash_screen.dart';
import 'services/config_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // OneSignal initialization
  if (ConfigService.oneSignalAppId.isNotEmpty) {
    OneSignal.initialize(ConfigService.oneSignalAppId);
    OneSignal.Notifications.requestPermission(true);
  }
  
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: '{$this->app->name}',
      theme: ThemeData(
        primaryColor: Color({$this->hexToColorCode($this->app->primary_color)}),
        colorScheme: ColorScheme.fromSeed(
          seedColor: Color({$this->hexToColorCode($this->app->primary_color)}),
        ),
        useMaterial3: true,
      ),
      home: SplashScreen(),
      debugShowCheckedModeBanner: false,
    );
  }
}
DART;

        file_put_contents($this->outputPath . '/lib/main.dart', $content);
    }

    protected function generateConfigDart()
    {
        $oneSignalAppId = $this->app->onesignal_app_id ?? '';
        
        $appName = $this->app->name;
        $packageName = $this->app->package_name;
        $websiteUrl = $this->app->website_url;
        $primaryColor = $this->app->primary_color;
        $secondaryColor = $this->app->secondary_color;
        
        $content = <<<DART
class ConfigService {
  static const String appName = '$appName';
  static const String packageName = '$packageName';
  static const String websiteUrl = '$websiteUrl';
  static const String oneSignalAppId = '$oneSignalAppId';
  static const String primaryColor = '$primaryColor';
  static const String secondaryColor = '$secondaryColor';
  static const String apiBaseUrl = 'http://127.0.0.1:8000/api';
  
  static const List<Map<String, dynamic>> menuItems = [
DART;

        foreach ($this->app->menuItems()->active()->ordered()->get() as $item) {
            $title = $item->title;
            $url = $item->url;
            $icon = $item->icon;
            $iconType = $item->icon_type;
            $target = $item->target;
            
            $content .= <<<DART
    {
      'title': '$title',
      'url': '$url',
      'icon': '$icon',
      'iconType': '$iconType',
      'target': '$target',
    },
DART;
        }

        $content .= <<<DART
  ];
}
DART;

        file_put_contents($this->outputPath . '/lib/services/config_service.dart', $content);
    }

    protected function generateWebViewScreen()
    {
        $primaryColorCode = $this->hexToColorCode($this->app->primary_color);
        
        $content = <<<DART
import 'package:flutter/material.dart';
import 'package:webview_flutter/webview_flutter.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import '../services/config_service.dart';

class WebViewScreen extends StatefulWidget {
  @override
  _WebViewScreenState createState() => _WebViewScreenState();
}

class _WebViewScreenState extends State<WebViewScreen> {
  late WebViewController controller;
  bool isLoading = true;
  bool hasError = false;
  int currentIndex = 0;

  @override
  void initState() {
    super.initState();
    initializeWebView();
    checkConnectivity();
  }

  void initializeWebView() {
    controller = WebViewController()
      ..setJavaScriptMode(JavaScriptMode.unrestricted)
      ..setNavigationDelegate(
        NavigationDelegate(
          onPageStarted: (String url) {
            setState(() {
              isLoading = true;
              hasError = false;
            });
          },
          onPageFinished: (String url) {
            setState(() {
              isLoading = false;
            });
          },
          onWebResourceError: (WebResourceError error) {
            setState(() {
              hasError = true;
              isLoading = false;
            });
          },
        ),
      )
      ..loadRequest(Uri.parse(ConfigService.websiteUrl));
  }

  void checkConnectivity() async {
    var connectivityResult = await Connectivity().checkConnectivity();
    if (connectivityResult == ConnectivityResult.none) {
      setState(() {
        hasError = true;
        isLoading = false;
      });
    }
  }

  void navigateToUrl(String url) {
    controller.loadRequest(Uri.parse(url));
    setState(() {
      isLoading = true;
      hasError = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(ConfigService.appName),
        backgroundColor: Color($primaryColorCode),
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: Icon(Icons.refresh),
            onPressed: () {
              controller.reload();
            },
          ),
        ],
      ),
      body: Stack(
        children: [
          if (hasError)
            Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.error_outline,
                    size: 64,
                    color: Colors.grey,
                  ),
                  SizedBox(height: 16),
                  Text(
                    'Bağlantı Hatası',
                    style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                  ),
                  SizedBox(height: 8),
                  Text(
                    'İnternet bağlantınızı kontrol edin',
                    style: TextStyle(color: Colors.grey),
                  ),
                  SizedBox(height: 16),
                  ElevatedButton(
                    onPressed: () {
                      checkConnectivity();
                      controller.reload();
                    },
                    child: Text('Tekrar Dene'),
                  ),
                ],
              ),
            )
          else
            WebViewWidget(controller: controller),
          if (isLoading)
            Center(
              child: CircularProgressIndicator(
                color: Color($primaryColorCode),
              ),
            ),
        ],
      ),
      bottomNavigationBar: ConfigService.menuItems.isNotEmpty
          ? BottomNavigationBar(
              currentIndex: currentIndex,
              onTap: (index) {
                setState(() {
                  currentIndex = index;
                });
                if (index < ConfigService.menuItems.length) {
                  navigateToUrl(ConfigService.menuItems[index]['url']);
                }
              },
              type: BottomNavigationBarType.fixed,
              selectedItemColor: Color($primaryColorCode),
              unselectedItemColor: Colors.grey,
              items: ConfigService.menuItems.map<BottomNavigationBarItem>((item) {
                return BottomNavigationBarItem(
                  icon: Icon(_getIconData(item['icon'], item['iconType'])),
                  label: item['title'],
                );
              }).toList(),
            )
          : null,
    );
  }

  IconData _getIconData(String iconName, String iconType) {
    // Material Icons
    switch (iconName.toLowerCase()) {
      case 'home':
        return Icons.home;
      case 'search':
        return Icons.search;
      case 'favorite':
        return Icons.favorite;
      case 'person':
        return Icons.person;
      case 'settings':
        return Icons.settings;
      case 'info':
        return Icons.info;
      case 'contact':
        return Icons.contact_mail;
      case 'phone':
        return Icons.phone;
      case 'email':
        return Icons.email;
      case 'location':
        return Icons.location_on;
      default:
        return Icons.web;
    }
  }
}
DART;

        file_put_contents($this->outputPath . '/lib/screens/webview_screen.dart', $content);
    }

    protected function generateBottomNavigation()
    {
        // Bottom navigation zaten WebViewScreen içinde oluşturuldu
    }

    protected function generateSplashScreen()
    {
        $primaryColorCode = $this->hexToColorCode($this->app->primary_color);
        
        $content = <<<DART
import 'package:flutter/material.dart';
import 'dart:async';
import 'webview_screen.dart';
import '../services/config_service.dart';

class SplashScreen extends StatefulWidget {
  @override
  _SplashScreenState createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _animationController;
  late Animation<double> _animation;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      duration: Duration(seconds: 2),
      vsync: this,
    );
    _animation = CurvedAnimation(
      parent: _animationController,
      curve: Curves.easeInOut,
    );

    _animationController.forward();

    Timer(Duration(seconds: 3), () {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (context) => WebViewScreen()),
      );
    });
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Color($primaryColorCode),
      body: Center(
        child: FadeTransition(
          opacity: _animation,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                width: 120,
                height: 120,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black26,
                      blurRadius: 10,
                      offset: Offset(0, 5),
                    ),
                  ],
                ),
                child: Icon(
                  Icons.mobile_friendly,
                  size: 60,
                  color: Color($primaryColorCode),
                ),
              ),
              SizedBox(height: 30),
              Text(
                ConfigService.appName,
                style: TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
              SizedBox(height: 10),
              Text(
                'Yükleniyor...',
                style: TextStyle(
                  fontSize: 16,
                  color: Colors.white70,
                ),
              ),
              SizedBox(height: 30),
              CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
DART;

        file_put_contents($this->outputPath . '/lib/screens/splash_screen.dart', $content);
    }

    protected function generateAndroidManifest()
    {
        $content = <<<XML
<manifest xmlns:android="http://schemas.android.com/apk/res/android"
    package="{$this->app->package_name}">

    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />
    <uses-permission android:name="android.permission.WAKE_LOCK" />
    <uses-permission android:name="android.permission.VIBRATE" />

    <application
        android:label="{$this->app->name}"
        android:name="\${applicationName}"
        android:icon="@mipmap/launcher_icon">
        
        <activity
            android:name=".MainActivity"
            android:exported="true"
            android:launchMode="singleTop"
            android:theme="@style/LaunchTheme"
            android:configChanges="orientation|keyboardHidden|keyboard|screenSize|smallestScreenSize|locale|layoutDirection|fontScale|screenLayout|density|uiMode"
            android:hardwareAccelerated="true"
            android:windowSoftInputMode="adjustResize">
            
            <meta-data
              android:name="io.flutter.embedding.android.NormalTheme"
              android:resource="@style/NormalTheme"
              />
              
            <intent-filter android:autoVerify="true">
                <action android:name="android.intent.action.MAIN"/>
                <category android:name="android.intent.category.LAUNCHER"/>
            </intent-filter>
        </activity>
        
        <meta-data
            android:name="flutterEmbedding"
            android:value="2" />
    </application>
</manifest>
XML;

        file_put_contents($this->outputPath . '/android/app/src/main/AndroidManifest.xml', $content);
    }

    protected function generateIOSInfo()
    {
        $bundleId = $this->app->bundle_id ?: $this->app->package_name;
        
        $content = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>CFBundleDevelopmentRegion</key>
    <string>\$(DEVELOPMENT_LANGUAGE)</string>
    <key>CFBundleDisplayName</key>
    <string>{$this->app->name}</string>
    <key>CFBundleExecutable</key>
    <string>\$(EXECUTABLE_NAME)</string>
    <key>CFBundleIdentifier</key>
    <string>$bundleId</string>
    <key>CFBundleInfoDictionaryVersion</key>
    <string>6.0</string>
    <key>CFBundleName</key>
    <string>{$this->app->name}</string>
    <key>CFBundlePackageType</key>
    <string>APPL</string>
    <key>CFBundleShortVersionString</key>
    <string>\$(FLUTTER_BUILD_NAME)</string>
    <key>CFBundleSignature</key>
    <string>????</string>
    <key>CFBundleVersion</key>
    <string>\$(FLUTTER_BUILD_NUMBER)</string>
    <key>LSRequiresIPhoneOS</key>
    <true/>
    <key>UILaunchStoryboardName</key>
    <string>LaunchScreen</string>
    <key>UIMainStoryboardFile</key>
    <string>Main</string>
    <key>UISupportedInterfaceOrientations</key>
    <array>
        <string>UIInterfaceOrientationPortrait</string>
        <string>UIInterfaceOrientationLandscapeLeft</string>
        <string>UIInterfaceOrientationLandscapeRight</string>
    </array>
    <key>UISupportedInterfaceOrientations~ipad</key>
    <array>
        <string>UIInterfaceOrientationPortrait</string>
        <string>UIInterfaceOrientationPortraitUpsideDown</string>
        <string>UIInterfaceOrientationLandscapeLeft</string>
        <string>UIInterfaceOrientationLandscapeRight</string>
    </array>
    <key>NSAppTransportSecurity</key>
    <dict>
        <key>NSAllowsArbitraryLoads</key>
        <true/>
    </dict>
</dict>
</plist>
XML;

        file_put_contents($this->outputPath . '/ios/Runner/Info.plist', $content);
    }

    protected function copyAssets()
    {
        // App icon kopyala
        if ($this->app->app_icon) {
            $iconPath = storage_path('app/public/' . $this->app->app_icon);
            if (file_exists($iconPath)) {
                copy($iconPath, $this->outputPath . '/assets/images/app_icon.png');
            }
        }

        // Splash image kopyala
        if ($this->app->splash_image) {
            $splashPath = storage_path('app/public/' . $this->app->splash_image);
            if (file_exists($splashPath)) {
                copy($splashPath, $this->outputPath . '/assets/images/splash_image.png');
            }
        }
    }

    protected function createZipFile()
    {
        $zipFileName = $this->app->package_name . '_flutter_project.zip';
        $zipPath = storage_path('app/public/flutter-exports/' . $zipFileName);
        
        // Export dizinini oluştur
        $exportDir = dirname($zipPath);
        if (!file_exists($exportDir)) {
            mkdir($exportDir, 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $this->addDirectoryToZip($zip, $this->outputPath, '');
            $zip->close();
            
            return 'flutter-exports/' . $zipFileName;
        }

        return false;
    }

    protected function addDirectoryToZip($zip, $dir, $zipDir)
    {
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (($file = readdir($handle)) !== false) {
                    if ($file != "." && $file != "..") {
                        $filePath = $dir . '/' . $file;
                        $zipFilePath = $zipDir . $file;
                        
                        if (is_dir($filePath)) {
                            $zip->addEmptyDir($zipFilePath);
                            $this->addDirectoryToZip($zip, $filePath, $zipFilePath . '/');
                        } else {
                            $zip->addFile($filePath, $zipFilePath);
                        }
                    }
                }
                closedir($handle);
            }
        }
    }

    protected function hexToColorCode($hex)
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return sprintf('0xFF%02X%02X%02X', $r, $g, $b);
    }
}
