<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('client_name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('company')
                    ->maxLength(255),

                TextInput::make('role')
                    ->maxLength(255),

                Textarea::make('testimonial_text')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),

                TextInput::make('avatar_initials')
                    ->maxLength(4),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
