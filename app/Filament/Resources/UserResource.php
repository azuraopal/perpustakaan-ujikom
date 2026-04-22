<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Kelola Anggota';
    protected static ?string $modelLabel = 'Anggota';
    protected static ?string $pluralModelLabel = 'Anggota';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Data Akun')
                ->description('Identitas utama dan akses login.')
                ->icon('heroicon-o-identification')
                ->columnSpanFull()
                ->schema([
                    TextInput::make('nomor_induk')
                        ->label('Nomor Induk (NIS/NIP)')
                        ->placeholder('Contoh: 24100123')
                        ->maxLength(50),
                    TextInput::make('nama_lengkap')
                        ->required()
                        ->placeholder('Nama lengkap anggota')
                        ->maxLength(150),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->placeholder('nama@sekolah.sch.id')
                        ->maxLength(100),
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->placeholder('Isi saat membuat akun baru')
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->helperText('Kosongkan saat edit jika tidak ingin ganti.')
                        ->maxLength(255),
                    Select::make('role')
                        ->options([
                            'admin' => 'Admin',
                            'siswa' => 'Siswa',
                        ])
                        ->native(false)
                        ->required()
                        ->default('siswa'),
                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->helperText('Siswa baru harus diaktifkan admin.'),
                ])
                ->columns(2),

            Section::make('Data Pribadi')
                ->description('Informasi profil tambahan.')
                ->icon('heroicon-o-user-circle')
                ->columnSpanFull()
                ->schema([
                    TextInput::make('kelas')
                        ->placeholder('Contoh: XII RPL 1')
                        ->maxLength(20),
                    TextInput::make('no_telepon')
                        ->tel()
                        ->placeholder('08xxxxxxxxxx')
                        ->maxLength(20),
                    FileUpload::make('foto')
                        ->image()
                        ->avatar()
                        ->imageEditor()
                        ->imagePreviewHeight('160')
                        ->panelLayout('compact')
                        ->directory('foto-user')
                        ->helperText('JPG/PNG maks 2MB.')
                        ->maxSize(2048),
                    Textarea::make('alamat')
                        ->rows(4)
                        ->placeholder('Alamat lengkap anggota'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')->circular(),
                TextColumn::make('nomor_induk')->label('NIS/NIP')->searchable()->sortable(),
                TextColumn::make('nama_lengkap')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'siswa' => 'info',
                    }),
                TextColumn::make('kelas')->searchable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options(['admin' => 'Admin', 'siswa' => 'Siswa']),
                TernaryFilter::make('is_active')->label('Status Aktif'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
