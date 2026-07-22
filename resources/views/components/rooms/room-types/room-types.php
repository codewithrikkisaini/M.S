<?php

use Livewire\Component;
use App\Models\RoomType;

new class extends Component
{
    public string $name = '';
    public string $daily_rate = '0.00';
    public string $weekly_rate = '0.00';
    public string $monthly_rate = '0.00';
    public string $tax_percentage = '15.00';
    public string $status = 'Active';

    public ?int $editingId = null;
    public string $editingName = '';
    public string $editingDailyRate = '0.00';
    public string $editingWeeklyRate = '0.00';
    public string $editingMonthlyRate = '0.00';
    public string $editingTaxPercentage = '15.00';
    public string $editingStatus = 'Active';

    public function addType(): void
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:room_types,name',
            'daily_rate' => 'required|numeric|min:0',
            'weekly_rate' => 'required|numeric|min:0',
            'monthly_rate' => 'required|numeric|min:0',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:Active,Inactive',
        ]);

        RoomType::create([
            'name' => $this->name,
            'daily_rate' => $this->daily_rate,
            'weekly_rate' => $this->weekly_rate,
            'monthly_rate' => $this->monthly_rate,
            'tax_percentage' => $this->tax_percentage,
            'status' => $this->status,
        ]);

        $this->name = '';
        $this->daily_rate = '0.00';
        $this->weekly_rate = '0.00';
        $this->monthly_rate = '0.00';
        $this->tax_percentage = '15.00';
        $this->status = 'Active';

        $this->dispatch('toast', message: 'Room type added successfully!', type: 'success');
    }

    public function editType(int $id): void
    {
        $this->resetValidation();
        $type = RoomType::findOrFail($id);

        $this->editingId = $type->id;
        $this->editingName = $type->name;
        $this->editingDailyRate = (string) $type->daily_rate;
        $this->editingWeeklyRate = (string) $type->weekly_rate;
        $this->editingMonthlyRate = (string) $type->monthly_rate;
        $this->editingTaxPercentage = (string) $type->tax_percentage;
        $this->editingStatus = $type->status;
    }

    public function updateType(): void
    {
        $this->validate([
            'editingName' => 'required|string|max:100|unique:room_types,name,' . $this->editingId,
            'editingDailyRate' => 'required|numeric|min:0',
            'editingWeeklyRate' => 'required|numeric|min:0',
            'editingMonthlyRate' => 'required|numeric|min:0',
            'editingTaxPercentage' => 'required|numeric|min:0|max:100',
            'editingStatus' => 'required|in:Active,Inactive',
        ]);

        RoomType::findOrFail($this->editingId)->update([
            'name' => $this->editingName,
            'daily_rate' => $this->editingDailyRate,
            'weekly_rate' => $this->editingWeeklyRate,
            'monthly_rate' => $this->editingMonthlyRate,
            'tax_percentage' => $this->editingTaxPercentage,
            'status' => $this->editingStatus,
        ]);

        $this->cancelEdit();
        $this->dispatch('toast', message: 'Room type updated successfully!', type: 'success');
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editingName = '';
        $this->editingDailyRate = '0.00';
        $this->editingWeeklyRate = '0.00';
        $this->editingMonthlyRate = '0.00';
        $this->editingTaxPercentage = '15.00';
        $this->editingStatus = 'Active';
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
