<?php

namespace Database\Seeders;

use App\Models\PlayerNote;
use Illuminate\Database\Seeder;

class PlayerNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $playerNotes = PlayerNote::factory()->count(10)->create();
    }
}
