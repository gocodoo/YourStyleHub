<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Permission;
use App\Models\Role;
use DB;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'User Management';
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                            ->label('Name'),
                Section::make('Permissions')
                    ->schema(static::getPermissionFormSchema())
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('type'),
                TextColumn::make('permissions')
                    ->label('សិទ្ធ')
                    ->formatStateUsing(function($state, $record){
                        $state = '['.$state.']';
                        $permissions = json_decode($state, true);
                        $permissions_parents = collect($permissions)->whereNotNull('parent_id')->pluck('parent_id')->unique()->toArray();
                        $permissions_parents = Permission::whereIn('id', $permissions_parents)->get();
                        $permissions_parents = collect($permissions_parents);

                         // Start building the HTML string
                        $html = '';

                        foreach ($permissions_parents as $parent) {
                            $html .= '-<strong>' . $parent['description'] . '</strong> ( ';

                            // Get child permissions for the current parent
                            $childPermissions = collect($permissions)->where('parent_id', $parent['id'])->pluck('description')->implode(' , ');

                            $html .= $childPermissions . ' ) <br>';
                        }

                        return $html;
                    })
                    ->html(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getPermissionFormSchema(): array
    {
        $permissions = Permission::query()->whereNull('parent_id')->orderBy('id','asc')->get();
        // dd($permissions);
        $arr = [];
        foreach($permissions as  $permission){
            // dd($permission);
            $arr[] = CheckboxList::make('permissions')
                ->label($permission->description)
                ->options($permission->children()->pluck('description','name')->toArray())
                ->formatStateUsing(function ($record, $set) {
                    if (isset($record) && !empty($record)) {
                        $permission_ids = DB::table('role_has_permissions')->where('role_id', $record->id)->pluck('permission_id')->toArray() ?? [];
                        $permission_names = Permission::query()->whereIn('id', $permission_ids)->pluck('name')->toArray() ?? [];
                        if (!empty($permission_names)) {
                            return $permission_names;
                        }
                        return [];
                    }
                    return [];
                });
        }
        return $arr;
    }
}
