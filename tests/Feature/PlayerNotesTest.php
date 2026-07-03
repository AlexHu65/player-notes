<?php

use App\Livewire\PlayerNotes;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

test('authenticated support agent can create a player note from livewire component', function () {
    Permission::firstOrCreate(['name' => 'create-player-note']);

    $author = User::factory()->create();
    $player = User::factory()->create();

    $author->givePermissionTo('create-player-note');

    $this->actingAs($author);

    Livewire::test(PlayerNotes::class)
        ->set('playerId', $player->id)
        ->set('content', 'Nota de prueba')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('player_notes', [
        'player_id' => $player->id,
        'author_id' => $author->id,
        'content' => 'Nota de prueba',
    ]);
});
