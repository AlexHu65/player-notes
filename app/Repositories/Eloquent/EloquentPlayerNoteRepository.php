<?php

namespace App\Repositories\Eloquent;

use App\Models\PlayerNote;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\PlayerNoteRepositoryInterface;

class EloquentPlayerNoteRepository implements PlayerNoteRepositoryInterface
{
    public function create(int $playerId, int $authorId, string $content): PlayerNote
    {
        return PlayerNote::create([
            'player_id' => $playerId,
            'author_id' => $authorId,
            'content'   => $content,
        ]);
    }

    public function findById(int $id): ?PlayerNote
    {
        return PlayerNote::find($id);
    }

    public function update(PlayerNote $playerNote, array $data): bool
    {
        return $playerNote->update($data);
    }

    public function delete(PlayerNote $playerNote): bool
    {
        return $playerNote->delete();
    }

    public function getByPlayer(int $playerId): Collection
    {
        return PlayerNote::query()
            ->with('author')
            ->where('player_id', $playerId)
            ->latest()
            ->get();
    }
}
