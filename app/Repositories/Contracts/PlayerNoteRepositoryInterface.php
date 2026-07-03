<?php

namespace App\Repositories\Contracts;

use App\Models\PlayerNote;
use Illuminate\Database\Eloquent\Collection;

interface PlayerNoteRepositoryInterface
{
    public function create(int $playerId, int $authorId, string $content): PlayerNote;

    public function findById(int $id): ?PlayerNote;

    public function update(PlayerNote $playerNote, array $data): bool;

    public function delete(PlayerNote $playerNote): bool;

    public function getByPlayer(int $playerId): Collection;
}
