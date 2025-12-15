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
        $monthTreatments = Treatment::where('treatment_date', '>=', $thisMonth)->count();

        $totalCustomers = Customer::count();
        $newCustomersThisMonth = Customer::where('created_at', '>=', $thisMonth)->count();

        $activeDesigners = Designer::where('is_active', true)->count();

        return [
            Stat::make('오늘 매출', number_format($todayRevenue) . '원')
                ->description('오늘 완료된 시술')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('이번 달 매출', number_format($monthRevenue) . '원')
                ->description($monthTreatments . '건 시술')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('오늘 시술 건수', $todayTreatments . '건')
                ->description('예약 포함')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('warning'),

            Stat::make('총 고객 수', number_format($totalCustomers) . '명')
                ->description('이번 달 신규 ' . $newCustomersThisMonth . '명')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('활성 디자이너', $activeDesigners . '명')
                ->description('현재 근무 중')
                ->descriptionIcon('heroicon-m-scissors')
                ->color('gray'),
        ];
    }
}
