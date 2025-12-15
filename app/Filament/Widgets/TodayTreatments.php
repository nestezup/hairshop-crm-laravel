<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Designer;
use App\Models\Service;
use App\Models\Treatment;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class TodayTreatments extends BaseWidget
{
    protected static ?string $heading = '오늘 시술 현황';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Treatment::query()
                    ->with(['customer', 'designer', 'service'])
                    ->whereDate('treatment_date', Carbon::today())
            )
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('시술 등록')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->modalHeading('시술 등록')
                    ->modalWidth('2xl')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_new_customer')
                                    ->label('신규 고객')
                                    ->live()
                                    ->columnSpanFull(),

                                Forms\Components\Select::make('customer_id')
                                    ->label('고객 선택')
                                    ->options(fn () => Customer::orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(fn (Get $get) => !$get('is_new_customer'))
                                    ->visible(fn (Get $get) => !$get('is_new_customer')),

                                Forms\Components\TextInput::make('new_customer_name')
                                    ->label('고객 이름')
                                    ->required(fn (Get $get) => $get('is_new_customer'))
                                    ->visible(fn (Get $get) => $get('is_new_customer')),

                                Forms\Components\TextInput::make('new_customer_phone')
                                    ->label('전화번호')
                                    ->tel()
                                    ->placeholder('010-0000-0000')
                                    ->visible(fn (Get $get) => $get('is_new_customer')),

                                Forms\Components\Select::make('designer_id')
                                    ->label('담당 디자이너')
                                    ->options(Designer::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('service_id')
                                    ->label('시술')
                                    ->options(Service::where('is_active', true)->pluck('name', 'id'))
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

                                Forms\Components\TextInput::make('price')
                                    ->label('금액')
                                    ->numeric()
                                    ->prefix('₩')
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->label('상태')
                                    ->options([
                                        'waiting' => '대기',
                                        'in_progress' => '시술중',
                                        'completed' => '완료',
                                        'reserved' => '예약',
                                    ])
                                    ->default('in_progress')
                                    ->required(),

                                Forms\Components\DateTimePicker::make('treatment_date')
                                    ->label('시술 일시')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\TextInput::make('memo')
                                    ->label('메모')
                                    ->placeholder('선택사항')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->action(function (array $data): void {
                        $customerId = $data['customer_id'] ?? null;

                        // 신규 고객 처리
                        if ($data['is_new_customer'] ?? false) {
                            $customer = Customer::create([
                                'name' => $data['new_customer_name'],
                                'phone' => $data['new_customer_phone'] ?? null,
                            ]);
                            $customerId = $customer->id;

                            Notification::make()
                                ->title("신규 고객 \"{$customer->name}\"님 등록")
                                ->info()
                                ->send();
                        }

                        Treatment::create([
                            'customer_id' => $customerId,
                            'designer_id' => $data['designer_id'],
                            'service_id' => $data['service_id'],
                            'treatment_date' => $data['treatment_date'],
                            'price' => $data['price'],
                            'status' => $data['status'],
                            'memo' => $data['memo'] ?? null,
                        ]);

                        Notification::make()
                            ->title('시술이 등록되었습니다')
                            ->success()
                            ->send();
                    }),
            ])
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
                        'in_progress' => 'primary',
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
                        ->color('primary')
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
                        ->visible(fn (Treatment $record) => !in_array($record->status, ['cancelled', 'completed']))
                        ->requiresConfirmation()
                        ->action(fn (Treatment $record) => $record->update(['status' => 'cancelled'])),
                ])->button()->label('변경')->size('sm'),
            ])
            ->emptyStateHeading('오늘 시술 내역이 없습니다')
            ->emptyStateDescription('상단의 "시술 등록" 버튼을 클릭하세요')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->defaultSort('treatment_date', 'asc')
            ->poll('30s')
            ->striped()
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
}
