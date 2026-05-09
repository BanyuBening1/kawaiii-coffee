<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Section 1: Identitas Pengguna ──────────────────────────
                Section::make('Identitas Pengguna')
                    ->description('Informasi dasar akun pengguna.')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Contoh: Budi Santoso')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->placeholder('Contoh: budi@email.com')
                            ->email()
                            ->required(),
                    ]),

                // ── Section 2: Kata Sandi ───────────────────────────────────
                Section::make('Kata Sandi')
                    ->description('Hanya diisi saat membuat akun baru.')
                    ->icon('heroicon-o-lock-closed')
                    ->columns(2)
                    ->visible(fn (string $operation): bool => $operation === 'create')
                    ->schema([
                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->confirmed()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),

                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Kata Sandi')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create'),
                    ]),

                // ── Section 3: Hak Akses ────────────────────────────────────
                Section::make('Hak Akses')
                    ->description('Tentukan peran pengguna dalam sistem.')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Select::make('role_id')
                            ->label('Peran')
                            ->relationship('role', 'roles_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
            ]);
    }
}