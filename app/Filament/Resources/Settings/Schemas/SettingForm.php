<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Must be unique. Use snake_case convention, e.g. site_name.'),

                Textarea::make('value')
                    ->rows(4)
                    ->columnSpanFull(),

                TextInput::make('group')
                    ->maxLength(255),
            ]);
    }
}
