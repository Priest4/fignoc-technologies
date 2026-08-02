<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('role')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),

                Textarea::make('specialisms')
                    ->rows(3)
                    ->columnSpanFull(),

                FileUpload::make('photo_path')
                    ->image()
                    ->imageResizeMode('cover')
                    ->disk('public')
                    ->directory('uploads')
                    ->columnSpanFull(),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
