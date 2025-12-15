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
            'status' => 'completed',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('시술 등록')
                    ->icon('heroicon-o-plus-circle')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\Select::make('customer_id')
                                    ->label('고객')
                                    ->options(Customer::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('이름')
                                            ->required(),
                                        Forms\Components\TextInput::make('phone')
                                            ->label('전화번호')
                                            ->tel(),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return Customer::create($data)->id;
                                    }),
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
                            ]),
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\DateTimePicker::make('treatment_date')
                                    ->label('시술 일시')
                                    ->required()
                                    ->default(now()),
                                Forms\Components\Select::make('status')
                                    ->label('상태')
                                    ->options([
                                        'completed' => '완료',
                                        'reserved' => '예약',
                                    ])
                                    ->default('completed')
                                    ->required(),
                                Forms\Components\TextInput::make('memo')
                                    ->label('메모')
                                    ->columnSpan(2),
                            ]),
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

        Treatment::create($data);

        Notification::make()
            ->title('시술이 등록되었습니다')
            ->success()
            ->send();

        $this->form->fill([
            'treatment_date' => now(),
            'status' => 'completed',
            'customer_id' => null,
            'designer_id' => null,
            'service_id' => null,
            'price' => null,
            'memo' => null,
        ]);
    }
}
