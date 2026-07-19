<?php

use Livewire\Component;
use App\Models\ChannelConnection;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $channelsList = [];

    public function mount(): void
    {
        $this->loadConnections();
    }

    public function loadConnections(): void
    {
        $hotelId = Auth::user()->hotel_id;
        
        // Define default OTA channels
        $defaultChannels = ['Booking.com', 'Expedia', 'Airbnb'];
        foreach ($defaultChannels as $c) {
            ChannelConnection::firstOrCreate(
                ['hotel_id' => $hotelId, 'channel_name' => $c],
                ['status' => 'disconnected', 'sync_status' => 'pending']
            );
        }

        $this->channelsList = ChannelConnection::where('hotel_id', $hotelId)->get();
    }

    public function toggleConnection($id): void
    {
        $conn = ChannelConnection::findOrFail($id);
        $newStatus = $conn->status === 'connected' ? 'disconnected' : 'connected';
        
        $conn->update([
            'status' => $newStatus,
            'sync_status' => $newStatus === 'connected' ? 'synced' : 'pending',
            'last_sync_at' => $newStatus === 'connected' ? now() : null,
        ]);

        ActivityLog::create([
            'hotel_id' => Auth::user()->hotel_id,
            'action' => 'Channel Status',
            'description' => "Channel '{$conn->channel_name}' connection status updated to {$newStatus}.",
            'ip_address' => request()->ip(),
            'user_id' => Auth::id(),
        ]);

        $this->loadConnections();
        
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Channel '{$conn->channel_name}' has been " . ($newStatus === 'connected' ? 'connected' : 'disconnected') . "."
        ]);
    }

    public function syncChannel($id): void
    {
        $conn = ChannelConnection::findOrFail($id);
        
        if ($conn->status !== 'connected') {
            $this->dispatch('toast', [
                'type' => 'warning',
                'message' => "Connect this channel first to enable syncing."
            ]);
            return;
        }

        $conn->update([
            'sync_status' => 'synced',
            'last_sync_at' => now(),
        ]);

        ActivityLog::create([
            'hotel_id' => Auth::user()->hotel_id,
            'action' => 'Channel Sync',
            'description' => "Synchronized inventory & pricing metrics with {$conn->channel_name}.",
            'ip_address' => request()->ip(),
            'user_id' => Auth::id(),
        ]);

        $this->loadConnections();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Room rates & availability synced with '{$conn->channel_name}'!"
        ]);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
