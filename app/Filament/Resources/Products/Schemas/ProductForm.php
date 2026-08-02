<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),

                TextInput::make('tag')
                    ->maxLength(255),

                Textarea::make('description')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                Repeater::make('features')
                    ->schema([
                        TextInput::make('feature')->required(),
                    ])
                    ->reorderable()
                    ->addActionLabel('Add Feature')
                    ->columnSpanFull(),

                FileUpload::make('screenshot_path')
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
