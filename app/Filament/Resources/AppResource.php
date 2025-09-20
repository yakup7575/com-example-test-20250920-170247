<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppResource\Pages;
use App\Models\App;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Support\Colors\Color;
use App\Services\GitHubActionsService;
use Filament\Notifications\Notification;

class AppResource extends Resource
{
    protected static ?string $model = App::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?string $navigationLabel = 'Uygulamalar';

    protected static ?string $pluralLabel = 'Uygulamalar';

    protected static ?string $label = 'Uygulama';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Uygulama Detayları')
                    ->tabs([
                        Tabs\Tab::make('Genel Bilgiler')
                            ->schema([
                                Section::make('Temel Bilgiler')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Uygulama Adı')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('package_name')
                                            ->label('Paket Adı (Android)')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->placeholder('com.example.myapp')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('bundle_id')
                                            ->label('Bundle ID (iOS)')
                                            ->placeholder('com.example.myapp')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Açıklama')
                                            ->rows(3),
                                        Forms\Components\TextInput::make('website_url')
                                            ->label('Website URL')
                                            ->required()
                                            ->url()
                                            ->placeholder('https://example.com'),
                                    ])->columns(2),
                            ]),
                        
                        Tabs\Tab::make('Görsel Ayarlar')
                            ->schema([
                                Section::make('Uygulama Görselleri')
                                    ->schema([
                                        Forms\Components\FileUpload::make('app_icon')
                                            ->label('Uygulama İkonu')
                                            ->image()
                                            ->directory('app-icons')
                                            ->acceptedFileTypes(['image/png', 'image/jpeg'])
                                            ->maxSize(2048),
                                        Forms\Components\FileUpload::make('splash_image')
                                            ->label('Splash Ekranı')
                                            ->image()
                                            ->directory('splash-images')
                                            ->acceptedFileTypes(['image/png', 'image/jpeg'])
                                            ->maxSize(2048),
                                    ])->columns(2),
                                
                                Section::make('Renk Ayarları')
                                    ->schema([
                                        Forms\Components\ColorPicker::make('primary_color')
                                            ->label('Ana Renk')
                                            ->default('#2196F3'),
                                        Forms\Components\ColorPicker::make('secondary_color')
                                            ->label('İkincil Renk')
                                            ->default('#FFC107'),
                                    ])->columns(2),
                            ]),
                        
                        Tabs\Tab::make('Platform & Bildirim')
                            ->schema([
                                Section::make('Platform Ayarları')
                                    ->schema([
                                        Forms\Components\Select::make('platform')
                                            ->label('Platform')
                                            ->options([
                                                'android' => 'Sadece Android',
                                                'ios' => 'Sadece iOS',
                                                'both' => 'Her İki Platform',
                                            ])
                                            ->default('both')
                                            ->required(),
                                        Forms\Components\Select::make('status')
                                            ->label('Durum')
                                            ->options([
                                                'draft' => 'Taslak',
                                                'building' => 'Derleniyor',
                                                'completed' => 'Tamamlandı',
                                                'published' => 'Yayınlandı',
                                            ])
                                            ->default('draft')
                                            ->required(),
                                    ])->columns(2),
                                
                                Section::make('OneSignal Bildirim Ayarları')
                                    ->schema([
                                        Forms\Components\TextInput::make('onesignal_app_id')
                                            ->label('OneSignal App ID')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('onesignal_api_key')
                                            ->label('OneSignal API Key')
                                            ->password()
                                            ->maxLength(255),
                                    ])->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('app_icon')
                    ->label('İkon')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('name')
                    ->label('Uygulama Adı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('package_name')
                    ->label('Paket Adı')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\BadgeColumn::make('platform')
                    ->label('Platform')
                    ->colors([
                        'success' => 'both',
                        'warning' => 'android',
                        'info' => 'ios',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Durum')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'building',
                        'success' => 'completed',
                        'primary' => 'published',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('platform')
                    ->label('Platform')
                    ->options([
                        'android' => 'Android',
                        'ios' => 'iOS',
                        'both' => 'Her İkisi',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'draft' => 'Taslak',
                        'building' => 'Derleniyor',
                        'completed' => 'Tamamlandı',
                        'published' => 'Yayınlandı',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('generate_flutter')
                    ->label('Flutter Projesi İndir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (App $record) {
                        $generator = new \App\Services\FlutterCodeGenerator($record);
                        $zipPath = $generator->generate();
                        
                        if ($zipPath) {
                            $fullPath = storage_path('app/public/' . $zipPath);
                            return response()->download($fullPath);
                        }
                        
                        Notification::make()
                            ->title('Hata!')
                            ->body('Flutter projesi oluşturulamadı.')
                            ->danger()
                            ->send();
                    }),
                
                Tables\Actions\Action::make('build_github')
                    ->label('GitHub Actions Build')
                    ->icon('heroicon-o-rocket-launch')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('GitHub Actions ile Build Et')
                    ->modalDescription('Bu işlem uygulamanızı GitHub\'a yükleyecek ve otomatik build başlatacaktır.')
                    ->action(function (App $record) {
                        $githubService = new GitHubActionsService();
                        $result = $githubService->createAndBuildApp($record);
                        
                        if ($result['success']) {
                            $record->update([
                                'status' => 'building',
                                'github_repo_url' => $result['repo_url']
                            ]);
                            
                            Notification::make()
                                ->title('Build Başlatıldı!')
                                ->body('GitHub Actions build başarıyla başlatıldı. İlerlemeyi takip edebilirsiniz.')
                                ->success()
                                ->actions([
                                    \Filament\Notifications\Actions\Action::make('view_repo')
                                        ->label('Repository\'yi Görüntüle')
                                        ->url($result['repo_url'], shouldOpenInNewTab: true),
                                    \Filament\Notifications\Actions\Action::make('view_actions')
                                        ->label('Build İlerlemesi')
                                        ->url($result['actions_url'], shouldOpenInNewTab: true),
                                ])
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Build Hatası!')
                                ->body($result['error'])
                                ->danger()
                                ->send();
                        }
                    }),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApps::route('/'),
            'create' => Pages\CreateApp::route('/create'),
            'edit' => Pages\EditApp::route('/{record}/edit'),
        ];
    }
}
