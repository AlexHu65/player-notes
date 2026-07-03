<?php

namespace App\Livewire;

use App\Models\User;
use App\Repositories\Contracts\PlayerNoteRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class PlayerNotes extends Component
{
    use WithPagination;

    public ?int $playerId = null;

    public string $content = '';

    protected function rules(): array
    {
        return [
            'playerId' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::notIn([(int) auth()->id()]),
            ],
            'content' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'playerId.required' => 'Debes seleccionar un jugador.',
            'playerId.exists' => 'El jugador seleccionado no es válido.',
            'content.required' => 'La nota no puede estar vacía.',
            'content.string' => 'El contenido de la nota debe ser una cadena de texto.',
            'content.max' => 'El contenido de la nota no puede tener más de 1000 caracteres.',
        ];
    }

    public function mount(): void
    {
        $this->playerId = $this->availablePlayers()->first()?->id;
    }

    public function updatedPlayerId(): void
    {
        $this->resetPage();
    }

    public function save(PlayerNoteRepositoryInterface $playerNoteRepository): void
    {
        $this->validate();

        if ($this->playerId === null) {
            return;
        }

        $playerNoteRepository->create(
            $this->playerId,
            (int) auth()->id(),
            $this->content,
        );

        $this->reset('content');
        $this->resetPage();

        $this->dispatch('note-added', ['message' => 'Note added successfully.']);
    }

    public function render(PlayerNoteRepositoryInterface $repository)
    {
        $players = $this->availablePlayers();
        $selectedPlayer = $players->firstWhere('id', $this->playerId);

        return view('livewire.player-notes', [
            'players' => $players,
            'selectedPlayer' => $selectedPlayer,
            'notes' => $repository->getByAuthorPaginated((int) auth()->id()),
        ]);
    }

    private function availablePlayers(): Collection
    {
        return User::query()
            ->whereKeyNot(auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function deleteNote(PlayerNoteRepositoryInterface $repository, int $noteId): void
    {
        $note = $repository->findById($noteId);
        if ($note && $note->author_id === auth()->id()) {
            $repository->delete($note);
        }
    }
}
