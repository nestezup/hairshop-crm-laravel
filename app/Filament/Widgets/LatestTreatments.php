<?php

namespace App\Filament\Widgets;

use App\Models\Treatment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTreatments extends BaseWidget
{
    protected static ?string $heading = '최근 시술 내역';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Treatment::query()
                    ->with(['customer', 'designer', 'service'])
                    ->latest('treatment_date')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('treatment_date')
                    ->label('시술일')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('고객'),
                Tables\Columns\TextColumn::make('designer.name')
                    ->label('디자이너'),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('시술'),
                Tables\Columns\TextColumn::make('price')
                    ->label('금액')
                    ->formatStateUsing(fn ($state) => number_format($state) . '원'),
                Tables\Columns\TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'reserved' => '예약',
                        'completed' => '완료',
                        'cancelled' => '취소',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'reserved' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->paginated(false);
    }
}
