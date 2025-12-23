<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Treatment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // 오늘 상태별 건수
        $todayWaiting = Treatment::whereDate('treatment_date', $today)
            ->where('status', 'waiting')
            ->count();

        $todayInProgress = Treatment::whereDate('treatment_date', $today)
            ->where('status', 'in_progress')
            ->count();

        $todayCompleted = Treatment::whereDate('treatment_date', $today)
            ->where('status', 'completed')
            ->count();

        $todayReserved = Treatment::whereDate('treatment_date', $today)
            ->where('status', 'reserved')
            ->count();

        // 매출
        $todayRevenue = Treatment::where('status', 'completed')
            ->whereDate('treatment_date', $today)
            ->sum('price');

        $monthRevenue = Treatment::where('status', 'completed')
            ->where('treatment_date', '>=', $thisMonth)
            ->sum('price');

        return [
            Stat::make('대기', $todayWaiting . '명')
                ->description('고객 대기중')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('시술중', $todayInProgress . '명')
                ->description('현재 시술')
                ->descriptionIcon('heroicon-m-scissors')
                ->color('info'),

            Stat::make('완료', $todayCompleted . '건')
                ->description('₩' . number_format($todayRevenue))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('예약', $todayReserved . '건')
                ->description('오늘 남은 예약')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('purple'),

            Stat::make('이번 달', '₩' . number_format($monthRevenue))
                ->description(date('n') . '월 누적 매출')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
        ];
    }
}
