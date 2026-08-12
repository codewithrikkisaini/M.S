<?php

use Livewire\Component;
use App\Models\Hotel;
use App\Models\User;
use App\Models\Role;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\SubscriptionInvoice;

new class extends Component
{
    public function approveHotel($id): void
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->update([
            'status' => 'approved',
            'account_status' => 'active',
        ]);
        
        // Find the owner/admin user of this hotel and activate them
        $adminUser = User::withoutGlobalScope('tenant')
            ->where('hotel_id', $hotel->id)
            ->first();

        if ($adminUser) {
            $adminUser->update(['status' => 'active']);
        }

        // Send Approval Email to Hotel Owner
        try {
            $recipientEmail = $adminUser?->email ?: $hotel->email;
            if ($recipientEmail) {
                \Illuminate\Support\Facades\Mail::to($recipientEmail)->send(new \App\Mail\HotelApproved($hotel, $adminUser));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send hotel approval email: ' . $e->getMessage());
        }

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Hotel '{$hotel->name}' approved successfully and notification email sent."
        ]);
    }

    public function rejectHotel($id): void
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->update([
            'status' => 'rejected',
            'account_status' => 'rejected',
        ]);
        
        $adminUser = User::withoutGlobalScope('tenant')
            ->where('hotel_id', $hotel->id)
            ->first();

        if ($adminUser) {
            $adminUser->update(['status' => 'inactive']);
        }

        try {
            $recipientEmail = $adminUser?->email ?: $hotel->email;
            if ($recipientEmail) {
                \Illuminate\Support\Facades\Mail::to($recipientEmail)->send(new \App\Mail\HotelRejected($hotel, $adminUser));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send hotel rejection email: ' . $e->getMessage());
        }

        $this->dispatch('toast', [
            'type' => 'warning',
            'message' => "Hotel '{$hotel->name}' rejected and email notification sent."
        ]);
    }

    public function suspendHotel($id): void
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->update(['status' => 'suspended']);

        User::withoutGlobalScope('tenant')
            ->where('hotel_id', $hotel->id)
            ->update(['status' => 'inactive']);

        $this->dispatch('toast', [
            'type' => 'warning',
            'message' => "Hotel '{$hotel->name}' suspended."
        ]);
    }

    public function render(): mixed
    {
        // 1. Hotels Status Counters
        $totalHotels     = Hotel::count();
        $approvedHotels  = Hotel::where('status', 'approved')->count();
        $pendingHotels   = Hotel::where('status', 'pending')->count();
        $suspendedHotels = Hotel::where('status', 'suspended')->count();
        $rejectedHotels  = Hotel::where('status', 'rejected')->count();

        // 2. Revenue Metrics (from SaaS Subscription Invoices)
        $totalRevenue        = SubscriptionInvoice::where('status', 'paid')->sum('amount');
        $monthlyRevenue      = SubscriptionInvoice::where('status', 'paid')
            ->where('billing_date', '>=', now()->startOfMonth()->format('Y-m-d'))
            ->sum('amount');
        $subscriptionRevenue = $totalRevenue; // Same in Phase 1 billing context

        // 3. Hotel Room Statistics (Global aggregates)
        $totalRooms        = Room::count();
        $occupiedRooms     = Room::where('status', 'Occupied')->count();
        $vacantRooms       = Room::where('status', 'Available')->count();
        $totalReservations = Reservation::count();

        // 4. Activity Lists
        $recentHotels   = Hotel::latest()->limit(5)->get();
        $recentBookings = Reservation::with('hotel')->latest()->limit(5)->get();
        
        // Fetch recent payments (both SaaS billing or hotel direct)
        $recentPayments = Payment::with('hotel')->latest()->limit(5)->get();

        return $this->view([
            'totalHotels'         => $totalHotels,
            'approvedHotels'      => $approvedHotels,
            'pendingHotels'       => $pendingHotels,
            'suspendedHotels'     => $suspendedHotels,
            'rejectedHotels'      => $rejectedHotels,
            'totalRevenue'        => $totalRevenue,
            'monthlyRevenue'      => $monthlyRevenue,
            'subscriptionRevenue' => $subscriptionRevenue,
            'totalRooms'          => $totalRooms,
            'occupiedRooms'       => $occupiedRooms,
            'vacantRooms'         => $vacantRooms,
            'totalReservations'   => $totalReservations,
            'recentHotels'        => $recentHotels,
            'recentBookings'      => $recentBookings,
            'recentPayments'      => $recentPayments,
        ]);
    }
};
