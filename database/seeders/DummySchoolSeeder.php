<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Str;

class DummySchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
{
    $owner = User::where('email', 'adnan.muhammad1290@gmail.com')->firstOrFail();

    $school = School::create([
        'name' => 'Springfield High',
        'slug' => Str::slug('Springfield High'),
    ]);

    $school->users()->attach($owner->id, [
        'role' => 'owner',
        'invited_by' => null,
    ]);
}
}
