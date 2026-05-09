<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('action')
                    ->required(),
                TextInput::make('table_name')
                    ->required(),
                TextInput::make('record_id')
                    ->required()
                    ->numeric(),
                TextInput::make('old_data'),
                TextInput::make('new_data'),
            ]);
    }
}
