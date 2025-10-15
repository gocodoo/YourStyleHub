<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                            ->label('ឈ្មោះ'),
                TextInput::make('email')
                            ->label('អុីម៉ែល'),
                TextInput::make('password')
                            ->label('លេខសម្ងាត់'),
            ]);
    }
}
