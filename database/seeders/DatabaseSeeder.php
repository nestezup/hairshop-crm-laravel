<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Designer;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Treatment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 관리자 계정 생성
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@hairshop.com',
            'password' => bcrypt('password'),
        ]);

        // 디자이너 생성
        $designers = [
            ['name' => '김민수', 'phone' => '010-1234-5678', 'position' => '원장', 'is_active' => true],
            ['name' => '이지연', 'phone' => '010-2345-6789', 'position' => '시니어', 'is_active' => true],
            ['name' => '박서윤', 'phone' => '010-3456-7890', 'position' => '디자이너', 'is_active' => true],
            ['name' => '최준호', 'phone' => '010-4567-8901', 'position' => '주니어', 'is_active' => true],
            ['name' => '정하나', 'phone' => '010-5678-9012', 'position' => '인턴', 'is_active' => true],
        ];

        foreach ($designers as $designer) {
            Designer::create($designer);
        }

        // 시술 메뉴 생성
        $services = [
            ['name' => '남성 커트', 'category' => '커트', 'price' => 20000, 'duration' => 30],
            ['name' => '여성 커트', 'category' => '커트', 'price' => 30000, 'duration' => 45],
            ['name' => '디자인 커트', 'category' => '커트', 'price' => 50000, 'duration' => 60],
            ['name' => '일반 펌', 'category' => '펌', 'price' => 80000, 'duration' => 120],
            ['name' => '디지털 펌', 'category' => '펌', 'price' => 120000, 'duration' => 150],
            ['name' => '매직 스트레이트', 'category' => '펌', 'price' => 150000, 'duration' => 180],
            ['name' => '새치 염색', 'category' => '염색', 'price' => 50000, 'duration' => 60],
            ['name' => '전체 염색', 'category' => '염색', 'price' => 80000, 'duration' => 90],
            ['name' => '탈색', 'category' => '염색', 'price' => 70000, 'duration' => 60],
            ['name' => '두피 클리닉', 'category' => '클리닉', 'price' => 30000, 'duration' => 30],
            ['name' => '모발 클리닉', 'category' => '클리닉', 'price' => 40000, 'duration' => 45],
            ['name' => '스타일링', 'category' => '스타일링', 'price' => 15000, 'duration' => 20],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // 고객 생성
        $customers = [
            ['name' => '홍길동', 'phone' => '010-1111-2222', 'gender' => 'male'],
            ['name' => '김영희', 'phone' => '010-2222-3333', 'gender' => 'female'],
            ['name' => '이철수', 'phone' => '010-3333-4444', 'gender' => 'male'],
            ['name' => '박미영', 'phone' => '010-4444-5555', 'gender' => 'female'],
            ['name' => '최동훈', 'phone' => '010-5555-6666', 'gender' => 'male'],
            ['name' => '정수민', 'phone' => '010-6666-7777', 'gender' => 'female'],
            ['name' => '강지훈', 'phone' => '010-7777-8888', 'gender' => 'male'],
            ['name' => '윤서연', 'phone' => '010-8888-9999', 'gender' => 'female'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }

        $allServices = Service::all();
        $allDesigners = Designer::all();
        $allCustomers = Customer::all();

        // 오늘 시술 내역 생성 - 다양한 상태
        $currentHour = Carbon::now()->hour;

        // 완료된 시술 (과거 시간)
        for ($i = 0; $i < 5; $i++) {
            $service = $allServices->random();
            $hour = rand(9, max(9, $currentHour - 1));

            Treatment::create([
                'customer_id' => $allCustomers->random()->id,
                'designer_id' => $allDesigners->random()->id,
                'service_id' => $service->id,
                'treatment_date' => Carbon::today()->setHour($hour)->setMinute(rand(0, 59)),
                'price' => $service->price,
                'status' => 'completed',
            ]);
        }

        // 현재 시술중
        for ($i = 0; $i < 2; $i++) {
            $service = $allServices->random();
            Treatment::create([
                'customer_id' => $allCustomers->random()->id,
                'designer_id' => $allDesigners->random()->id,
                'service_id' => $service->id,
                'treatment_date' => Carbon::now()->subMinutes(rand(10, 30)),
                'price' => $service->price,
                'status' => 'in_progress',
            ]);
        }

        // 대기중
        for ($i = 0; $i < 2; $i++) {
            $service = $allServices->random();
            Treatment::create([
                'customer_id' => $allCustomers->random()->id,
                'designer_id' => $allDesigners->random()->id,
                'service_id' => $service->id,
                'treatment_date' => Carbon::now(),
                'price' => $service->price,
                'status' => 'waiting',
            ]);
        }

        // 예약 (미래 시간)
        for ($i = 0; $i < 3; $i++) {
            $service = $allServices->random();
            $hour = rand(max($currentHour + 1, 14), 19);

            Treatment::create([
                'customer_id' => $allCustomers->random()->id,
                'designer_id' => $allDesigners->random()->id,
                'service_id' => $service->id,
                'treatment_date' => Carbon::today()->setHour($hour)->setMinute(0),
                'price' => $service->price,
                'status' => 'reserved',
            ]);
        }

        // 최근 2주간 시술 내역 생성
        for ($i = 0; $i < 40; $i++) {
            $treatmentDate = Carbon::now()->subDays(rand(1, 14))->setHour(rand(10, 19))->setMinute(rand(0, 59));
            $service = $allServices->random();

            Treatment::create([
                'customer_id' => $allCustomers->random()->id,
                'designer_id' => $allDesigners->random()->id,
                'service_id' => $service->id,
                'treatment_date' => $treatmentDate,
                'price' => $service->price,
                'status' => 'completed',
            ]);
        }
    }
}
