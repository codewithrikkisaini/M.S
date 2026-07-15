<?php

use Livewire\Component;
use App\Models\Guest;

new class extends Component
{
    public string $guest_id = '', $name = '', $email = '', $phone = '',
                  $nationality = '', $passport_number = '', $address = '';

    public function mount(): void
    {
        $this->guest_id = 'G-' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT);
    }

    public function save(): void
    {
        $this->validate([
            'guest_id'        => 'required|unique:guests,guest_id',
            'name'            => 'required|string|max:255',
            'email'           => 'nullable|email|unique:guests,email',
            'phone'           => 'nullable|string|max:20',
            'nationality'     => 'nullable|string|max:100',
            'passport_number' => 'nullable|string|max:100',
            'address'         => 'nullable|string',
        ]);

        Guest::create([
            'guest_id'        => $this->guest_id,
            'name'            => $this->name,
            'email'           => $this->email ?: null,
            'phone'           => $this->phone ?: null,
            'nationality'     => $this->nationality ?: null,
            'passport_number' => $this->passport_number ?: null,
            'address'         => $this->address ?: null,
        ]);

        session()->flash('toast', ['message' => 'Guest added successfully!', 'type' => 'success']);
        $this->redirect(route('guests.index'), navigate: true);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
