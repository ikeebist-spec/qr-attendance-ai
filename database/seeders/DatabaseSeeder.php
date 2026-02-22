<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\Event;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed default sections
        $sections = ['1A', '1B', '1C', '1D', '1E', '1F', '1G', '2A', '2B', '2C', '2D', '2E', '3A', '3B', '3C', '3D', '4A', '4B', '4C'];
        foreach ($sections as $name) {
            Section::firstOrCreate(['name' => $name]);
        }

        // Seed default events
        $events = [
            ['name' => 'Alay sa Paaralan (Week 1)', 'date' => '2023-10-01', 'type' => 'Mandatory'],
            ['name' => 'Alay sa Paaralan (Week 2)', 'date' => '2023-10-08', 'type' => 'Mandatory'],
            ['name' => 'College Week Opening', 'date' => '2023-10-15', 'type' => 'Major'],
        ];
        foreach ($events as $event) {
            Event::firstOrCreate(['name' => $event['name']], $event);
        }
    }
}
