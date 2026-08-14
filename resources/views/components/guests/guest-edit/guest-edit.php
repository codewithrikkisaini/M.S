<?php

use Livewire\Component;
use App\Models\Guest;

new class extends Component
{
    public Guest $guest;
    public string $guest_id = '', $name = '', $email = '', $phone = '',
                  $nationality = '', $id_type = '', $id_number = '', $passport_number = '', $address = '';

    public function mount(Guest $guest): void
    {
        $this->guest            = $guest;
        $this->guest_id         = $guest->guest_id;
        $this->name             = $guest->name;
        $this->email            = $guest->email ?? '';
        $this->phone            = $guest->phone ?? '';
        $this->nationality      = $guest->nationality ?? '';
        $this->id_type          = $guest->id_type ?? '';
        $this->id_number        = $guest->id_number ?? $guest->passport_number ?? '';
        $this->passport_number  = $guest->passport_number ?? '';
        $this->address          = $guest->address ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'guest_id'        => 'required|unique:guests,guest_id,' . $this->guest->id,
            'name'            => 'required|string|max:255',
            'email'           => 'nullable|email|unique:guests,email,' . $this->guest->id,
            'phone'           => 'nullable|string|max:20',
            'nationality'     => 'nullable|string|max:100',
            'id_type'         => 'nullable|string|max:100',
            'id_number'       => 'nullable|string|max:100',
            'passport_number' => 'nullable|string|max:100',
            'address'         => 'nullable|string',
        ]);

        $this->guest->update([
            'guest_id'        => $this->guest_id,
            'name'            => $this->name,
            'email'           => $this->email ?: null,
            'phone'           => $this->phone ?: null,
            'nationality'     => $this->nationality ?: null,
            'id_type'         => $this->id_type ?: null,
            'id_number'       => $this->id_number ?: null,
            'passport_number' => $this->id_number ?: ($this->passport_number ?: null),
            'address'         => $this->address ?: null,
        ]);

        session()->flash('toast', ['message' => 'Guest updated successfully!', 'type' => 'success']);
        $this->redirect(route('guests.index'), navigate: true);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
