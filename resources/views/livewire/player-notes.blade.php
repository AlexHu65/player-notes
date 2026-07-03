<div>
    <h3 class="text-lg font-semibold mb-3">Historial de Notas</h3>

    <div class="mb-4">
        <label for="playerId" class="block text-sm font-medium text-gray-700 mb-1">Jugador</label>
        <select
            id="playerId"
            wire:model.live="playerId"
            class="w-full border rounded p-2"
        >
            <option value="">Selecciona un jugador</option>
            @foreach ($players as $player)
                <option value="{{ $player->id }}">{{ $player->name }}</option>
            @endforeach
        </select>
        @error('playerId')
            <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    @if ($selectedPlayer)
        <p class="text-sm text-gray-600 mb-3">Nueva nota para: <span class="font-semibold">{{ $selectedPlayer->name }}</span></p>
    @endif

    <h4 class="text-base font-semibold mb-2">Notas que has dejado</h4>
    <table class="w-full text-sm border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 text-left">Fecha</th>
                <th class="p-2 text-left">Jugador</th>
                <th class="p-2 text-left">Autor</th>
                <th class="p-2 text-left">Nota</th>
                <th class="p-2 text-left">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($notes as $note)
                <tr class="border-t" wire:key="note-{{ $note->id }}">
                    <td class="p-2 whitespace-nowrap">{{ $note->created_at->format('d/m/Y H:i') }}</td>
                    <td class="p-2">{{ $note->player->name }}</td>
                    <td class="p-2">{{ $note->author->name }}</td>
                    <td class="p-2">{{ $note->content }}</td>
                    <td class="p-2">
                        <button
                            wire:click="deleteNote({{ $note->id }})"
                            class="text-red-600 hover:underline"
                        >
                            Eliminar
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                        <td colspan="4" class="p-2 text-center text-gray-500">
                        Aún no has registrado notas.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $notes->links() }}
    </div>

    @can('create-player-note')
        <form wire:submit.prevent="save" class="mt-6">
            <textarea
                wire:model="content"
                maxlength="1000"
                rows="3"
                class="w-full border rounded p-2"
                placeholder="Escribe una nota interna sobre este jugador..."
            ></textarea>

            @error('content')
                <span class="text-red-600 text-sm">{{ $message }}</span>
            @enderror

            <button
                type="submit"
                class="mt-2 bg-blue-600 text-white px-4 py-2 rounded"
                wire:loading.attr="disabled"
                @disabled(!$playerId)
            >
                Agregar Nota
            </button>
        </form>
    @endcan
</div>
