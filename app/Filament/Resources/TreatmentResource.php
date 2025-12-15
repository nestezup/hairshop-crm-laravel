<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TreatmentResource\Pages;
use App\Models\Treatment;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TreatmentResource extends Resource
{
    protected static ?string $model = Treatment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = '시술 내역';

    protected static ?string $modelLabel = '시술';

    protected static ?string $pluralModelLabel = '시술 내역';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('시술 정보')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->label('고객')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('이름')
                                    ->required(),
                                Forms\Components\TextInput::make('phone')
                                    ->label('전화번호')
                                    ->tel(),
                            ]),
                        Forms\Components\Select::make('designer_id')
                            ->label('담당 디자이너')
                            ->relationship('designer', 'name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('service_id')
                            ->label('시술')
                            ->relationship('service', 'name', fn ($query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if ($state) {
                                    $service = Service::find($state);
                                    if ($service) {
                                        $set('price', $service->price);
                                    }
                                }
                            }),
                        Forms\Components\DateTimePicker::make('treatment_date')
                            ->label('시술 일시')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('price')
                            ->label('결제 금액')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('₩'),
                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                'reserved' => '예약',
                                'waiting' => '대기',
                                'in_progress' => '시술중',
                                'completed' => '완료',
                                'cancelled' => '취소',
                            ])
                            ->default('in_progress')
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make('메모')
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
                Tables\Columns\TextColumn::make('treatment_date')
                    ->label('시술일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('고객')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('designer.name')
                    ->label('디자이너')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('시술')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('금액')
                    ->formatStateUsing(fn ($state) => number_format($state) . '원')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'reserved' => '예약',
                        'waiting' => '대기',
                        'in_progress' => '시술중',
                        'completed' => '완료',
                        'cancelled' => '취소',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'reserved' => 'info',
                        'waiting' => 'warning',
                        'in_progress' => 'orange',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('treatment_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('designer_id')
                    ->label('디자이너')
                    ->relationship('designer', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        'reserved' => '예약',
                        'waiting' => '대기',
                        'in_progress' => '시술중',
                        'completed' => '완료',
                        'cancelled' => '취소',
                    ]),
                Tables\Filters\Filter::make('treatment_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('시작일'),
                        Forms\Components\DatePicker::make('until')
                            ->label('종료일'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('treatment_date', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('treatment_date', '<=', $data['until']));
                    }),
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
            'index' => Pages\ListTreatments::route('/'),
            'create' => Pages\CreateTreatment::route('/create'),
            'edit' => Pages\EditTreatment::route('/{record}/edit'),
        ];
    }
}
