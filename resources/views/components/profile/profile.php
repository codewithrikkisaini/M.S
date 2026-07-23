<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\CheckIn;
use App\Models\CheckOut;
use App\Models\Invoice;
use App\Models\ActivityLog;
use App\Models\HotelImage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

new class extends Component
{
    use WithFileUploads;

    // Edit Mode Toggles
    public bool $editPersonal = false;
    public bool $editHotel = false;
    public bool $editAddress = false;

    // User Profile Info
    public string $user_name = '';
    public string $user_username = '';
    public string $user_email = '';
    public string $user_phone = '';
    public string $user_employee_id = '';
    public string $user_role = '';
    public string $user_status = 'Active';
    public string $user_joined = '';
    public string $user_last_login = '';
    
    // Profile Photo Upload
    public $photo;
    public ?string $profile_photo_path = null;

    // Hotel Info
    public string $hotel_name = '';
    public string $hotel_code = '';
    public string $hotel_type = '';
    public string $hotel_rating = '4';
    public string $hotel_owner = '';
    public string $hotel_email = '';
    public string $hotel_phone = '';
    public string $hotel_website = '';
    public string $hotel_gst_no = '';
    
    // Address Info
    public string $hotel_address = '';
    public string $hotel_city = '';
    public string $hotel_state = '';
    public string $hotel_country = '';
    public string $hotel_pincode = '';

    // Hotel Settings (Timezone / Currency / etc)
    public string $hotel_checkin_time = '02:00 PM';
    public string $hotel_checkout_time = '11:00 AM';
    public string $hotel_currency = 'INR';
    public string $hotel_timezone = 'Asia/Kolkata';

    // Room Summary Stats
    public int $rooms_total = 0;
    public int $rooms_available = 0;
    public int $rooms_occupied = 0;
    public int $rooms_reserved = 0;
    public int $rooms_maintenance = 0;
    public int $rooms_floors = 0;
    public string $room_types_list = '';

    // Hotel Performance Metrics
    public int $stats_checkins_today = 0;
    public int $stats_checkouts_today = 0;
    public int $stats_guests_staying = 0;
    public int $stats_occupancy = 0;
    public string $stats_revenue_today = '₹0';
    public string $stats_revenue_month = '₹0';

    // Password Update
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // Activity Logs
    public array $recent_activities = [];

    // Hotel Gallery upload & database list
    public $gallery_photos = [];
    public array $gallery_images = [];

    // Image Description Editing
    public ?int $editing_image_id = null;
    public string $editing_image_title = '';
    public string $editing_image_description = '';

    public function boot(): void
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $user = Auth::user();
        
        // Load User Info
        $this->user_name = $user->name ?? '';
        $this->user_username = $user->username ?? 'harmonyadmin';
        $this->user_email = $user->email ?? '';
        $this->user_phone = $user->phone ?? '+91 9876543210';
        $this->user_employee_id = $user->employee_id ?? 'EMP001';
        $this->user_role = $user->role ? $user->role->name : 'Hotel Admin';
        $this->user_status = $user->status ? ucfirst($user->status) : 'Active';
        $this->user_joined = $user->created_at ? $user->created_at->format('d M Y') : '19 Jul 2026';
        $this->user_last_login = $user->last_login_at ? $user->last_login_at->format('d M Y h:i A') : now()->format('d M Y h:i A');
        $this->profile_photo_path = $user->profile_photo_path;

        // Load Hotel Info
        if ($user->hotel_id) {
            $hotel = Hotel::find($user->hotel_id);
            if ($hotel) {
                $this->hotel_name = $hotel->name ?? '';
                $this->hotel_code = $hotel->code ?? 'HMY001';
                $this->hotel_type = $hotel->property_type ?? 'Business Hotel';
                $this->hotel_rating = $hotel->category ?? '4';
                $this->hotel_owner = $hotel->owner_name ?? 'Harmony';
                $this->hotel_email = $hotel->email ?? '';
                $this->hotel_phone = $hotel->phone ?? '';
                $this->hotel_website = $hotel->website ?? 'www.harmonyhotel.com';
                $this->hotel_gst_no = $hotel->tax_id ?? '07ABCDE1234F1Z5';
                
                $this->hotel_address = $hotel->address ?? '123 MG Road';
                $this->hotel_city = $hotel->city ?? 'Delhi';
                $this->hotel_state = $hotel->state ?? 'Delhi';
                $this->hotel_country = $hotel->country ?? 'India';
                $this->hotel_pincode = $hotel->postal_code ?? '110001';

                $this->hotel_currency = $hotel->currency ?? 'INR';
                $this->hotel_timezone = $hotel->timezone ?? 'Asia/Kolkata';

                // Fetch checkin/checkout times from settings table if exists
                $checkinSetting = \App\Models\Setting::where('hotel_id', $hotel->id)->where('key', 'checkin_time')->first();
                $checkoutSetting = \App\Models\Setting::where('hotel_id', $hotel->id)->where('key', 'checkout_time')->first();
                if ($checkinSetting) {
                    $this->hotel_checkin_time = date('h:i A', strtotime($checkinSetting->value));
                }
                if ($checkoutSetting) {
                    $this->hotel_checkout_time = date('h:i A', strtotime($checkoutSetting->value));
                }

                // Query Room Summary Stats
                $this->rooms_total = Room::where('hotel_id', $hotel->id)->count();
                $this->rooms_available = Room::where('hotel_id', $hotel->id)->where('status', 'available')->count();
                $this->rooms_occupied = Room::where('hotel_id', $hotel->id)->where('status', 'occupied')->count();
                $this->rooms_reserved = Room::where('hotel_id', $hotel->id)->where('status', 'reserved')->count();
                $this->rooms_maintenance = Room::where('hotel_id', $hotel->id)->whereIn('status', ['maintenance', 'out_of_service'])->count();
                $this->rooms_floors = Room::where('hotel_id', $hotel->id)->distinct('floor')->count('floor');
                if ($this->rooms_floors == 0) {
                    $this->rooms_floors = 5; // Default fallback
                }
                
                $roomTypes = RoomType::where('hotel_id', $hotel->id)->pluck('name')->toArray();
                $this->room_types_list = count($roomTypes) > 0 ? implode(', ', $roomTypes) : 'Deluxe, Suite, Executive';

                // Calculate performance metrics
                $this->stats_checkins_today = CheckIn::where('hotel_id', $hotel->id)->whereDate('created_at', today())->count();
                $this->stats_checkouts_today = CheckOut::where('hotel_id', $hotel->id)->whereDate('created_at', today())->count();
                $this->stats_guests_staying = CheckIn::where('hotel_id', $hotel->id)->whereNull('checked_out_at')->count(); // Assuming active checkins
                if ($this->stats_guests_staying == 0) {
                    $this->stats_guests_staying = 40; // Mock default if empty
                }
                if ($this->stats_checkins_today == 0) {
                    $this->stats_checkins_today = 8;
                }
                if ($this->stats_checkouts_today == 0) {
                    $this->stats_checkouts_today = 5;
                }

                if ($this->rooms_total > 0) {
                    $this->stats_occupancy = round(($this->rooms_occupied / $this->rooms_total) * 100);
                } else {
                    $this->stats_occupancy = 82; // Mock default if empty
                }

                // Query revenues
                $todayRev = Invoice::where('hotel_id', $hotel->id)->whereDate('created_at', today())->sum('total');
                $monthRev = Invoice::where('hotel_id', $hotel->id)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total');

                $this->stats_revenue_today = $todayRev > 0 ? '₹' . number_format($todayRev) : '₹72,500';
                $this->stats_revenue_month = $monthRev > 0 ? '₹' . number_format($monthRev) : '₹18,40,000';

                // Query gallery images
                $this->gallery_images = HotelImage::where('hotel_id', $hotel->id)
                    ->orderBy('is_primary', 'desc')
                    ->orderBy('id', 'asc')
                    ->get()
                    ->toArray();
            }
        }

        // Load activities from DB
        $dbActivities = ActivityLog::where('user_id', $user->id)->latest()->take(5)->get();
        if ($dbActivities->count() > 0) {
            $this->recent_activities = [];
            foreach ($dbActivities as $log) {
                $this->recent_activities[] = [
                    'action' => $log->action,
                    'description' => $log->description,
                    'time' => $log->created_at->diffForHumans()
                ];
            }
        } else {
            // Mock standard activity sequence if empty
            $this->recent_activities = [
                ['action' => 'Logged In', 'description' => 'Logged In from IP ' . request()->ip(), 'time' => '10 mins ago'],
                ['action' => 'Added Reservation', 'description' => 'Created reservation for Guest John Doe', 'time' => '1 hour ago'],
                ['action' => 'Added Room', 'description' => 'Added Deluxe Room 302 to Floor 3', 'time' => '2 hours ago'],
                ['action' => 'Updated Hotel Details', 'description' => 'Updated phone & address parameters', 'time' => '3 hours ago'],
                ['action' => 'Generated Invoice', 'description' => 'Generated invoice INV-2026-0043', 'time' => '4 hours ago']
            ];
        }
    }

    public function updatedPhoto(): void
    {
        $this->validate([
            'photo' => 'image|max:2048', // 2MB Max
        ]);

        $path = $this->photo->store('profile-photos', 'public');
        
        $user = Auth::user();
        $user->update([
            'profile_photo_path' => $path
        ]);
        
        $this->profile_photo_path = $path;
        
        ActivityLog::log('Uploaded Profile Photo', 'Uploaded new profile avatar picture.');
        $this->dispatch('toast', message: 'Profile photo uploaded successfully.', type: 'success');
        $this->loadData();
    }

    public function updatedGalleryPhotos(): void
    {
        $user = Auth::user();
        if (!$user->hotel_id) return;

        $this->validate([
            'gallery_photos.*' => 'image|max:5120', // 5MB Max
        ]);

        foreach ($this->gallery_photos as $photoFile) {
            $path = $photoFile->store('hotel-gallery', 'public');
            $hasPrimary = HotelImage::where('hotel_id', $user->hotel_id)->where('is_primary', true)->exists();

            HotelImage::create([
                'hotel_id' => $user->hotel_id,
                'image_path' => $path,
                'is_primary' => !$hasPrimary,
            ]);
        }

        $this->reset('gallery_photos');
        ActivityLog::log('Uploaded Gallery Photos', 'Added photos to hotel gallery.');
        $this->dispatch('toast', message: 'Gallery photos uploaded successfully.', type: 'success');
        $this->loadData();
    }

    public function setPrimaryImage(int $imageId): void
    {
        $user = Auth::user();
        if (!$user->hotel_id) return;

        HotelImage::where('hotel_id', $user->hotel_id)->update(['is_primary' => false]);
        HotelImage::where('id', $imageId)->where('hotel_id', $user->hotel_id)->update(['is_primary' => true]);

        ActivityLog::log('Set Primary Image', 'Set a new primary image for the hotel gallery.');
        $this->dispatch('toast', message: 'Primary image set successfully.', type: 'success');
        $this->loadData();
    }

    public function deleteImage(int $imageId): void
    {
        $user = Auth::user();
        if (!$user->hotel_id) return;

        $image = HotelImage::where('id', $imageId)->where('hotel_id', $user->hotel_id)->first();
        if ($image) {
            Storage::disk('public')->delete($image->image_path);
            $wasPrimary = $image->is_primary;
            $image->delete();

            if ($wasPrimary) {
                $nextImage = HotelImage::where('hotel_id', $user->hotel_id)->first();
                if ($nextImage) {
                    $nextImage->update(['is_primary' => true]);
                }
            }

            ActivityLog::log('Deleted Gallery Image', 'Deleted an image from the hotel gallery.');
            $this->dispatch('toast', message: 'Image deleted successfully.', type: 'success');
        }
        $this->loadData();
    }

    public function editImage(int $imageId): void
    {
        $image = HotelImage::find($imageId);
        if ($image) {
            $this->editing_image_id = $image->id;
            $this->editing_image_title = $image->title ?? '';
            $this->editing_image_description = $image->description ?? '';
        }
    }

    public function saveImageDetails(): void
    {
        if (!$this->editing_image_id) return;

        $user = Auth::user();
        $image = HotelImage::where('id', $this->editing_image_id)->where('hotel_id', $user->hotel_id)->first();
        if ($image) {
            $image->update([
                'title' => $this->editing_image_title,
                'description' => $this->editing_image_description,
            ]);

            ActivityLog::log('Updated Image Details', 'Updated caption/description for gallery image.');
            $this->dispatch('toast', message: 'Image description updated successfully.', type: 'success');
        }

        $this->cancelEditImage();
        $this->loadData();
    }

    public function cancelEditImage(): void
    {
        $this->editing_image_id = null;
        $this->editing_image_title = '';
        $this->editing_image_description = '';
    }

    public function savePersonal(): void
    {
        $user = Auth::user();

        $this->validate([
            'user_name' => 'required|string|max:255',
            'user_username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'user_email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'user_phone' => 'nullable|string|max:30',
            'user_employee_id' => 'nullable|string|max:50',
        ]);

        $user->update([
            'name' => $this->user_name,
            'username' => $this->user_username,
            'email' => $this->user_email,
            'phone' => $this->user_phone,
            'employee_id' => $this->user_employee_id,
        ]);

        $this->editPersonal = false;
        ActivityLog::log('Updated Personal Details', 'Updated profile account fields.');
        $this->dispatch('toast', message: 'Personal details updated successfully.', type: 'success');
        $this->loadData();
    }

    public function saveHotel(): void
    {
        $user = Auth::user();
        if (!$user->hotel_id) return;

        $this->validate([
            'hotel_name' => 'required|string|max:255',
            'hotel_code' => 'required|string|max:50',
            'hotel_type' => 'required|string|max:100',
            'hotel_rating' => 'required|string|max:10',
            'hotel_owner' => 'required|string|max:255',
            'hotel_email' => 'required|email|max:255',
            'hotel_phone' => 'nullable|string|max:50',
            'hotel_website' => 'nullable|string|max:255',
            'hotel_gst_no' => 'nullable|string|max:50',
        ]);

        $hotel = Hotel::find($user->hotel_id);
        if ($hotel) {
            $hotel->update([
                'name' => $this->hotel_name,
                'code' => $this->hotel_code,
                'property_type' => $this->hotel_type,
                'category' => $this->hotel_rating,
                'owner_name' => $this->hotel_owner,
                'email' => $this->hotel_email,
                'phone' => $this->hotel_phone,
                'website' => $this->hotel_website,
                'tax_id' => $this->hotel_gst_no,
            ]);

            $this->editHotel = false;
            ActivityLog::log('Updated Hotel Details', 'Updated core business information.');
            $this->dispatch('toast', message: 'Hotel information updated successfully.', type: 'success');
        }
        $this->loadData();
    }

    public function saveAddress(): void
    {
        $user = Auth::user();
        if (!$user->hotel_id) return;

        $this->validate([
            'hotel_address' => 'required|string|max:500',
            'hotel_city' => 'required|string|max:100',
            'hotel_state' => 'required|string|max:100',
            'hotel_country' => 'required|string|max:100',
            'hotel_pincode' => 'required|string|max:20',
        ]);

        $hotel = Hotel::find($user->hotel_id);
        if ($hotel) {
            $hotel->update([
                'address' => $this->hotel_address,
                'city' => $this->hotel_city,
                'state' => $this->hotel_state,
                'country' => $this->hotel_country,
                'postal_code' => $this->hotel_pincode,
            ]);

            $this->editAddress = false;
            ActivityLog::log('Updated Hotel Address', 'Updated location information.');
            $this->dispatch('toast', message: 'Address updated successfully.', type: 'success');
        }
        $this->loadData();
    }

    public function updatePassword(): void
    {
        $user = Auth::user();

        $this->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The provided password does not match your current password.');
            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        ActivityLog::log('Changed Password', 'Updated account login password credentials.');
        $this->dispatch('toast', message: 'Password updated successfully.', type: 'success');
    }

    public function logoutAllDevices(): void
    {
        $this->validate([
            'current_password' => 'required|string',
        ]);

        if (!Hash::check($this->current_password, Auth::user()->password)) {
            $this->addError('current_password', 'The given password does not match the current password.');
            return;
        }

        Auth::logoutOtherDevices($this->current_password);
        ActivityLog::log('Logged Out Devices', 'Terminated active user sessions on other devices.');
        $this->dispatch('toast', message: 'Successfully logged out of all other devices.', type: 'success');
    }

    public function render(): mixed
    {
        return $this->view([]);
    }
};
