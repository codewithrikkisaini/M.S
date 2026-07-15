<?php

use Livewire\Component;
use App\Models\RoomType;

new class extends Component
{
    public string $name = '';

    public ?int $editingId = null;
    public string $editingName = '';

    public function addType(): void
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:room_types,name',
        ]);

        RoomType::create(['name' => $this->name]);

        $this->name = '';
        $this->dispatch('toast', message: 'Room type added successfully!', type: 'success');
    }

    public function editType(int $id): void
    {
        $this->resetValidation();
        $type = RoomType::findOrFail($id);
        $this->editingId = $type->id;
        $this->editingName = $type->name;
    }

    public function updateType(): void
    {
        $this->validate([
            'editingName' => 'required|string|max:100|unique:room_types,name,' . $this->editingId,
        ]);

        RoomType::findOrFail($this->editingId)->update(['name' => $this->editingName]);

        $this->editingId = null;
        $this->editingName = '';
        $this->dispatch('toast', message: 'Room type updated successfully!', type: 'success');
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editingName = '';
        $this->resetValidation();
    }

    public function deleteType(int $id): void
    {
        $type = RoomType::findOrFail($id);

        if ($type->rooms()->exists()) {
            $this->dispatch('toast', message: "Cannot delete \"{$type->name}\" — rooms are still using this type.", type: 'error');
            return;
        }

        $type->delete();
        $this->dispatch('toast', message: 'Room type deleted.', type: 'success');
    }

    public function render(): mixed
    {
        $roomTypes = RoomType::withCount('rooms')->orderBy('name')->get();

        return $this->view(compact('roomTypes'));
    }
};
