<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolResource\Pages;
use App\Filament\Resources\SchoolResource\RelationManagers;
use App\Models\School;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\SchoolResource\Pages\InviteUser;


class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
           ->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('slug')->searchable()->sortable(),
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
        'index' => Pages\ListSchools::route('/'),
        'create' => Pages\CreateSchool::route('/create'),
        'edit' => Pages\EditSchool::route('/{record}/edit'),
        'invite' => InviteUser::route('/{record}/invite'), 
        // 'bulk-invite' => Pages\BulkInviteUsers::route('/{record}/bulk-invite'),
    ];
}

public static function canViewAny(): bool
{
    /** @var \App\Models\User|null $user */
    $user = Auth::user();

    return $user?->hasRole('owner') ?? false;
}

 public static function canAccess(): bool
    {
        return Auth::check();
    }

    
}
