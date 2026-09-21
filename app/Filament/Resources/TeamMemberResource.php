<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Models\TeamMember;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static string|UnitEnum|null $navigationGroup = 'About Page';
    protected static ?string $navigationLabel = 'Team';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('role')
                ->required()
                ->helperText('e.g. "Social Work" or "Post Treasurer & Senior Operations Manager"'),
            Forms\Components\Textarea::make('bio')
                ->rows(3)
                ->required(false),
            Forms\Components\SpatieMediaLibraryFileUpload::make('photo')
                ->collection('photo')
                ->image()
                ->maxSize(10240)
                ->helperText(
                    'IMPORTANT: only upload a real photo of this real person (with their consent). '
                    . 'Never use an AI-generated image here — it would misrepresent a fabricated face '
                    . 'as documentation of a named individual, which undermines the transparency this '
                    . 'org is built on. Leave blank to show a neutral placeholder avatar instead. '
                    . 'Max file size: 10 MB.'
                )
                ->required(false),
            Forms\Components\TextInput::make('photo_alt')
                ->label('Photo alt text')
                ->helperText('Describes the photo for screen readers and search engines, e.g. "Portrait of [Name], [Role]."'),
            Forms\Components\Select::make('group')
                ->options(TeamMember::GROUPS)
                ->default('team')
                ->required()
                ->helperText('Which section this person appears in on the About page.'),
            Forms\Components\TextInput::make('order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_published')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('photo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('role'),
                Tables\Columns\TextColumn::make('group')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => TeamMember::GROUPS[$state] ?? $state),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
            ])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }
}
