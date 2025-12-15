<?php

namespace App\Filament\Widgets;

use App\Models\Designer;
use App\Models\Treatment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class DesignerRevenueChart extends ChartWidget
{
    protected static ?string $heading = '디자이너별 이번 달 매출';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $thisMonth = Carbon::now()->startOfMonth();

        $designers = Designer::where('is_active', true)->get();

        $data = [];
        $labels = [];
        $colors = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4'];

        foreach ($designers as $index => $designer) {
            $revenue = Treatment::where('designer_id', $designer->id)
                ->where('status', 'completed')
                ->where('treatment_date', '>=', $thisMonth)
                ->sum('price');

            $labels[] = $designer->name;
            $data[] = $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => '매출 (원)',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
