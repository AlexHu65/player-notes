<?php

namespace App\Livewire;

use App\Models\PlayerNote;
use App\Models\User;
use App\Repositories\Contracts\PlayerNoteRepositoryInterface;
use Illuminate\Support\Collection;
use Livewire\Component;

class PlayerNotes extends Component
{

    public User $player;

    public string $content = '';

    protected function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'The note content is required.',
            'content.string' => 'The note content must be a string.',
            'content.max' => 'The note content may not be greater than 1000 characters.',
        ];
    }

    public function mount(User $player): void
    {
        $this->player = $player;
    }

}
