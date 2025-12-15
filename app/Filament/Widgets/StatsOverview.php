<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Designer;
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

        $todayRevenue = Treatment::where('status', 'completed')
            ->whereDate('treatment_date', $today)
            ->sum('price');

        $monthRevenue = Treatment::where('status', 'completed')
            ->where('treatment_date', '>=', $thisMonth)
            ->sum('price');

        $todayTreatments = Treatment::whereDate('treatment_date', $today)->count();
        $todayCompleted = Treatment::where('status', 'completed')
            ->whereDate('treatment_date', $today)
            ->count();

        $totalCustomers = Customer::count();

        return [
            Stat::make('오늘 매출', '₩' . number_format($todayRevenue))
                ->description($todayCompleted . '건 완료')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('이번 달 매출', '₩' . number_format($monthRevenue))
                ->description(date('n') . '월 누적')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),

            Stat::make('오늘 시술', $todayTreatments . '건')
                ->description('예약 포함')
                ->descriptionIcon('heroicon-m-scissors')
                ->color('warning'),

            Stat::make('전체 고객', number_format($totalCustomers) . '명')
                ->description('등록된 고객')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
}
