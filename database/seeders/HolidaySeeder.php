<?php

namespace Database\Seeders;

use App\Models\Holidays;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holiday = [
            [
                'name' => '*Eid ul Fitr (Day 0)',
                'date' => '2025-03-01',
                'day' => 'Sunday'
            ],
            [
                'name' => '*Eid ul Fitr (Day 1)',
                'date' => '2025-03-31',
                'day' => 'Monday'
            ],
            [
                'name' => '*Eid ul Fitr (Day 2)',
                'date' => '2025-04-01',
                'day' => 'Tuesday'
            ],
            [
                'name' => 'Memorial Day',
                'date' => '2025-05-26',
                'day' => 'Monday'
            ],
            [
                'name' => '*Eid ul Azha (Day 0)',
                'date' => '2025-06-06',
                'day' => 'Friday'
            ],
            [
                'name' => '*Eid ul Azha (Day 1)',
                'date' => '2025-06-07',
                'day' => 'Saturday'
            ],
            [
                'name' => '*Eid ul Azha (Day 2)',
                'date' => '2025-06-08',
                'day' => 'Sunday'
            ],
            [
                'name' => 'Independence Day (USA)',
                'date' => '2025-07-04',
                'day' => 'Friday'
            ],
            [
                'name' => "Labor's Day (USA)",
                'date' => '2025-09-01',
                'day' => 'Monday'
            ],
            [
                'name' => 'Thanksgiving Day',
                'date' => '2025-11-27',
                'day' => 'Thursday'
            ],
            [
                'name' => 'Christmas Day',
                'date' => '2025-12-25',
                'day' => 'Thursday'
            ],

        ];
        foreach ($holiday as $data) {
            Holidays::create($data);
        }
    }
}
