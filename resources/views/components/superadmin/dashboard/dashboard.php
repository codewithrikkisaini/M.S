<?php

use Livewire\Component;
use App\Models\Hotel;
use App\Models\User;
use App\Models\Role;

new class extends Component
{
    public function approveHotel($id): void
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->update(['status' => 'approved']);
        
        // Find the owner/admin user of this hotel and activate them
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            User::where('hotel_id', $hotel->id)
                ->where('role_id', $adminRole->id)
                ->update(['status' => 'active']);
        }

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Hotel '{$hotel->name}' approved successfully."
        ]);
    }

    public function rejectHotel($id): void
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->update(['status' => 'rejected']);
        
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            User::where('hotel_id', $hotel->id)
                ->where('role_id', $adminRole->id)
                ->update(['status' => 'suspended']);
        }

        $this->dispatch('toast', [
            'type' => 'warning',
            'message' => "Hotel '{$hotel->name}' rejected."
        ]);
    }

    public function render(): mixed
    {
        $totalHotels    = Hotel::count();
        $approvedHotels = Hotel::where('status', 'approved')->count();
        $pendingHotels  = Hotel::where('status', 'pending')->count();
        $rejectedHotels = Hotel::where('status', 'rejected')->count();

        $recentHotels = Hotel::latest()->limit(8)->get();

        return $this->view([
            'totalHotels'    => $totalHotels,
            'approvedHotels' => $approvedHotels,
            'pendingHotels'  => $pendingHotels,
            'rejectedHotels' => $rejectedHotels,
            'recentHotels'   => $recentHotels,
        ]);
    }
};
