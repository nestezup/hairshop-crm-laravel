<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = '고객';

    protected static ?string $modelLabel = '고객';

    protected static ?string $pluralModelLabel = '고객';

    protected static ?int $navigationSort = 2;

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
                        Forms\Components\Select::make('gender')
                            ->label('성별')
                            ->options([
                                'male' => '남성',
                                'female' => '여성',
                                'other' => '기타',
                            ]),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('생년월일'),
                    ])->columns(2),
                Forms\Components\Section::make('추가 정보')
                    ->schema([
                        Forms\Components\Textarea::make('memo')
                            ->label('메모')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
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
                Tables\Columns\TextColumn::make('gender')
                    ->label('성별')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'male' => '남성',
                        'female' => '여성',
                        'other' => '기타',
                        default => '-',
                    }),
                Tables\Columns\TextColumn::make('treatments_count')
                    ->label('시술 횟수')
                    ->counts('treatments')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('등록일')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->label('성별')
                    ->options([
                        'male' => '남성',
                        'female' => '여성',
                        'other' => '기타',
                    ]),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
