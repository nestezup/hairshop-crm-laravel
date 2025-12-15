<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Designer;
use App\Models\Service;
use App\Models\Treatment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class QuickTreatmentForm extends Widget implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.widgets.quick-treatment-form';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'treatment_date' => now(),
            'status' => 'in_progress',
            'is_new_customer' => false,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('시술 등록')
                    ->icon('heroicon-o-plus-circle')
                    ->schema([
                        Forms\Components\Grid::make(5)
                            ->schema([
                                Forms\Components\Toggle::make('is_new_customer')
                                    ->label('신규 고객')
                                    ->inline(false)
                                    ->live()
                                    ->columnSpan(1),

                                // 기존 고객 선택 (신규가 아닐 때)
                                Forms\Components\Select::make('customer_id')
                                    ->label('고객 선택')
                                    ->options(fn () => Customer::orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required(fn (Get $get) => !$get('is_new_customer'))
                                    ->visible(fn (Get $get) => !$get('is_new_customer'))
                                    ->columnSpan(2),

                                // 신규 고객 입력 (신규일 때)
                                Forms\Components\TextInput::make('new_customer_name')
                                    ->label('고객 이름')
                                    ->required(fn (Get $get) => $get('is_new_customer'))
                                    ->visible(fn (Get $get) => $get('is_new_customer'))
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('new_customer_phone')
                                    ->label('전화번호')
                                    ->tel()
                                    ->placeholder('010-0000-0000')
                                    ->visible(fn (Get $get) => $get('is_new_customer'))
                                    ->columnSpan(1),

                                Forms\Components\Select::make('designer_id')
                                    ->label('담당 디자이너')
                                    ->options(Designer::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(fn (Get $get) => $get('is_new_customer') ? 1 : 2),
                            ]),

                        Forms\Components\Grid::make(4)
                            ->schema([
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
                                Forms\Components\DateTimePicker::make('treatment_date')
                                    ->label('시술 일시')
                                    ->required()
                                    ->default(now()),
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
                            ]),

                        Forms\Components\TextInput::make('memo')
                            ->label('메모')
                            ->placeholder('시술 관련 메모 (선택사항)')
                            ->columnSpanFull(),
                    ])
                    ->footerActions([
                        Forms\Components\Actions\Action::make('save')
                            ->label('시술 등록')
                            ->icon('heroicon-o-check')
                            ->submit('save'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // 신규 고객인 경우 먼저 고객 등록
        if ($data['is_new_customer']) {
            $customer = Customer::create([
                'name' => $data['new_customer_name'],
                'phone' => $data['new_customer_phone'] ?? null,
            ]);
            $data['customer_id'] = $customer->id;

            Notification::make()
                ->title('신규 고객 "' . $customer->name . '"님이 등록되었습니다')
                ->info()
                ->send();
        }

        // 시술 등록
        Treatment::create([
            'customer_id' => $data['customer_id'],
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

        // 폼 초기화
        $this->form->fill([
            'treatment_date' => now(),
            'status' => 'in_progress',
            'is_new_customer' => false,
            'customer_id' => null,
            'designer_id' => null,
            'service_id' => null,
            'price' => null,
            'memo' => null,
            'new_customer_name' => null,
            'new_customer_phone' => null,
        ]);
    }
}
