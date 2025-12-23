<?php

namespace App\Filament\Widgets;

use App\Models\Designer;
use App\Models\Treatment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class DesignerRevenue extends BaseWidget
{
    protected static ?string $heading = '디자이너별 매출';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return $table
            ->query(
                Designer::query()
                    ->where('is_active', true)
                    ->withCount([
                        'treatments as today_count' => function ($query) use ($today) {
                            $query->whereDate('treatment_date', $today)
                                ->where('status', 'completed');
                        },
                        'treatments as today_in_progress' => function ($query) use ($today) {
                            $query->whereDate('treatment_date', $today)
                                ->whereIn('status', ['waiting', 'in_progress']);
                        },
                        'treatments as month_count' => function ($query) use ($thisMonth) {
                            $query->where('treatment_date', '>=', $thisMonth)
                                ->where('status', 'completed');
                        },
                    ])
                    ->withSum([
                        'treatments as today_revenue' => function ($query) use ($today) {
                            $query->whereDate('treatment_date', $today)
                                ->where('status', 'completed');
                        },
                    ], 'price')
                    ->withSum([
                        'treatments as month_revenue' => function ($query) use ($thisMonth) {
                            $query->where('treatment_date', '>=', $thisMonth)
                                ->where('status', 'completed');
                        },
                    ], 'price')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('디자이너')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('position')
                    ->label('직급')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '원장' => 'purple',
                        '시니어' => 'info',
                        '디자이너' => 'gray',
                        '주니어' => 'teal',
                        '인턴' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('today_in_progress')
                    ->label('진행중')
                    ->badge()
                    ->color('warning')
                    ->suffix('건')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('today_count')
                    ->label('오늘 완료')
                    ->badge()
                    ->color('success')
                    ->suffix('건')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('today_revenue')
                    ->label('오늘 매출')
                    ->formatStateUsing(fn ($state) => '₩' . number_format($state ?? 0))
                    ->color('success')
                    ->weight('bold')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('month_count')
                    ->label('이달 완료')
                    ->suffix('건')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('month_revenue')
                    ->label('이달 매출')
                    ->formatStateUsing(fn ($state) => '₩' . number_format($state ?? 0))
                    ->color('primary')
                    ->weight('bold')
                    ->alignEnd(),
            ])
            ->defaultSort('today_revenue', 'desc')
            ->paginated(false);
    }
}
