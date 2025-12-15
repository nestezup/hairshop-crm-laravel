<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = '시술 메뉴';

    protected static ?string $modelLabel = '시술 메뉴';

    protected static ?string $pluralModelLabel = '시술 메뉴';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('시술 정보')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('시술명')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label('카테고리')
                            ->options([
                                '커트' => '커트',
                                '펌' => '펌',
                                '염색' => '염색',
                                '클리닉' => '클리닉',
                                '스타일링' => '스타일링',
                                '기타' => '기타',
                            ])
                            ->default('기타')
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->label('가격')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('₩')
                            ->suffix('원'),
                        Forms\Components\TextInput::make('duration')
                            ->label('소요시간')
                            ->required()
                            ->numeric()
                            ->default(60)
                            ->suffix('분'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('사용')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('시술명')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('카테고리')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '커트' => 'info',
                        '펌' => 'success',
                        '염색' => 'warning',
                        '클리닉' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->label('가격')
                    ->formatStateUsing(fn ($state) => number_format($state) . '원')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('소요시간')
                    ->formatStateUsing(fn ($state) => $state . '분')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('사용')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('카테고리')
                    ->options([
                        '커트' => '커트',
                        '펌' => '펌',
                        '염색' => '염색',
                        '클리닉' => '클리닉',
                        '스타일링' => '스타일링',
                        '기타' => '기타',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('사용 여부'),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
