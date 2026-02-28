<?php

namespace Database\Seeders;

use App\Models\YearAndSection;
use App\Models\Event;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // No default sections seeded, user manages them manually

        // Seed default events
        $events = [
            ['name' => 'Alay sa Paaralan (Week 1)', 'date' => '2023-10-01', 'type' => 'Mandatory', 'fine' => 50],
            ['name' => 'Alay sa Paaralan (Week 2)', 'date' => '2023-10-08', 'type' => 'Mandatory', 'fine' => 50],
            ['name' => 'College Week Opening', 'date' => '2023-10-15', 'type' => 'Major', 'fine' => 50],
        ];
        foreach ($events as $event) {
            Event::firstOrCreate(['name' => $event['name']], $event);
        }
    }
}
