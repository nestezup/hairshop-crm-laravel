<?php

namespace App\Filament\Widgets;

use App\Models\Treatment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlyTrendChart extends ChartWidget
{
    protected static ?string $heading = '일별 매출 추이 (이번 달)';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $today = Carbon::today();

        $data = [];
        $labels = [];

        $current = $startOfMonth->copy();
        while ($current <= $today) {
            $dailyRevenue = Treatment::where('status', 'completed')
                ->whereDate('treatment_date', $current)
                ->sum('price');

            $labels[] = $current->format('m/d');
            $data[] = $dailyRevenue;

            $current->addDay();
        }

        return [
            'datasets' => [
                [
                    'label' => '일별 매출',
                    'data' => $data,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
