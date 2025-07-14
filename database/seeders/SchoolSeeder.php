<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        // Create owner user if not exists
        $owner = User::firstOrCreate(
            ['email' => 'adnan.muhammad1290@gmail.com'],
            ['name' => 'Muhammad Adnan']
        );

        $schools = [
            [
                'name' => 'Beaconhouse Achool Systems',
                'slug' => 'beacnhouse-school-sys',
            ],
            [
                'name' => 'Shelbyville Academy',
                'slug' => 'shelbyville-academy',
            ],
        ];

        foreach ($schools as $data) {
            $school = School::where('slug', $data['slug'])->first();

            if (! $school) {
                $school = School::create($data);
            }

            // Attach user with role, without detaching others
            $school->users()->syncWithoutDetaching([
                $owner->id => [
                    'role' => 'owner',
                    'invited_by' => null,
                ]
            ]);
        }

        $this->command->info('Schools and owner seeded successfully!');
    }
}
