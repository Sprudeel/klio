<?php

namespace Database\Seeders;

use App\Models\Assignment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignments = [
            ['name' => 'Mathe Hausaufgabe 1', 'deadline' => now()->addDays(7)],
            ['name' => 'Deutsch Aufsatz', 'deadline' => now()->addDays(10)],
            ['name' => 'Geschichte Referat', 'deadline' => now()->addDays(14)],
            ['name' => 'Physik Aufgabenblatt', 'deadline' => now()->addDays(5)],
            ['name' => 'Chemie Laborbericht', 'deadline' => now()->addDays(12)],
            ['name' => 'Biologie Projektarbeit', 'deadline' => now()->addDays(20)],
            ['name' => 'Englisch Essay', 'deadline' => now()->addDays(9)],
            ['name' => 'Informatik Programmieraufgabe', 'deadline' => now()->addDays(15)],
        ];

        // Distribute to users
        foreach ($assignments as $index => $data) {
            Assignment::create([
                'name' => $data['name'],
                'code' => strtoupper(\Str::random(8)),
                'deadline' => $data['deadline'],
                'color' => '#'.dechex(rand(0x000000, 0xFFFFFF)),
                'icon' => '📘',
                'description' => 'Beschreibung für '.$data['name'],
                'author_id' => match ($index % 3) {
                    0 => 1,
                    1 => 2,
                    2 => 3,
                },
            ]);
        }
    }
}
