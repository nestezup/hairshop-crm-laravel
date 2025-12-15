<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DesignerResource\Pages;
use App\Models\Designer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DesignerResource extends Resource
{
    protected static ?string $model = Designer::class;

    protected static ?string $navigationIcon = 'heroicon-o-scissors';

    protected static ?string $navigationLabel = '디자이너';

    protected static ?string $modelLabel = '디자이너';

    protected static ?string $pluralModelLabel = '디자이너';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('기본 정보')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('이름')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('전화번호')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('이메일')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Select::make('position')
                            ->label('직급')
                            ->options([
                                '인턴' => '인턴',
                                '주니어' => '주니어',
                                '디자이너' => '디자이너',
                                '시니어' => '시니어',
                                '원장' => '원장',
                            ])
                            ->default('디자이너')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('활성')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('이름')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('전화번호')
                    ->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('직급')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '원장' => 'danger',
                        '시니어' => 'warning',
                        '디자이너' => 'success',
                        '주니어' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('활성')
                    ->boolean(),
                Tables\Columns\TextColumn::make('treatments_count')
                    ->label('시술 건수')
                    ->counts('treatments')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('position')
                    ->label('직급')
                    ->options([
                        '인턴' => '인턴',
                        '주니어' => '주니어',
                        '디자이너' => '디자이너',
                        '시니어' => '시니어',
                        '원장' => '원장',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('활성 상태'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListDesigners::route('/'),
            'create' => Pages\CreateDesigner::route('/create'),
            'edit' => Pages\EditDesigner::route('/{record}/edit'),
        ];
    }
}
