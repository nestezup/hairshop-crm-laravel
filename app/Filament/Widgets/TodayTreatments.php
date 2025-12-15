<?php

namespace App\Filament\Widgets;

use App\Models\Treatment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class TodayTreatments extends BaseWidget
{
    protected static ?string $heading = '오늘 시술 현황';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Treatment::query()
                    ->with(['customer', 'designer', 'service'])
                    ->whereDate('treatment_date', Carbon::today())
                    ->latest('treatment_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('treatment_date')
                    ->label('시간')
                    ->dateTime('H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('고객')
                    ->searchable(),
                Tables\Columns\TextColumn::make('designer.name')
                    ->label('디자이너')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('시술'),
                Tables\Columns\TextColumn::make('price')
                    ->label('금액')
                    ->formatStateUsing(fn ($state) => number_format($state) . '원')
                    ->alignEnd(),
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
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('waiting')
                        ->label('대기')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->visible(fn (Treatment $record) => $record->status === 'reserved')
                        ->action(fn (Treatment $record) => $record->update(['status' => 'waiting'])),
                    Tables\Actions\Action::make('start')
                        ->label('시술 시작')
                        ->icon('heroicon-o-play')
                        ->color('orange')
                        ->visible(fn (Treatment $record) => in_array($record->status, ['reserved', 'waiting']))
                        ->action(fn (Treatment $record) => $record->update(['status' => 'in_progress'])),
                    Tables\Actions\Action::make('complete')
                        ->label('완료')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn (Treatment $record) => in_array($record->status, ['waiting', 'in_progress']))
                        ->action(fn (Treatment $record) => $record->update(['status' => 'completed'])),
                    Tables\Actions\Action::make('cancel')
                        ->label('취소')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->visible(fn (Treatment $record) => $record->status !== 'cancelled' && $record->status !== 'completed')
                        ->requiresConfirmation()
                        ->action(fn (Treatment $record) => $record->update(['status' => 'cancelled'])),
                ])->button()->label('상태 변경'),
            ])
            ->emptyStateHeading('오늘 시술 내역이 없습니다')
            ->emptyStateDescription('위에서 시술을 등록해주세요')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->defaultSort('treatment_date', 'asc')
            ->poll('30s');
    }
}
