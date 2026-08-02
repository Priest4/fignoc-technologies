<?php

namespace App\Filament\Resources\PortfolioItems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PortfolioItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Select::make('type')
                    ->options([
                        'Web App'    => 'Web App',
                        'Mobile App' => 'Mobile App',
                        'E-commerce' => 'E-commerce',
                        'Dashboard'  => 'Dashboard',
                        'Branding'   => 'Branding',
                        'API'        => 'API',
                    ]),

                Textarea::make('description')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                TagsInput::make('technologies')
                    ->columnSpanFull(),

                FileUpload::make('image_path')
                    ->image()
                    ->imageResizeMode('cover')
                    ->disk('public')
                    ->directory('uploads')
                    ->columnSpanFull(),

                TextInput::make('project_url')
                    ->url()
                    ->maxLength(255),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_featured'),

                Toggle::make('is_coming_soon'),
            ]);
    }
}
