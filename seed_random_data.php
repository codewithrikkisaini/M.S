<?php

// Seed Random Data Script for Hotel Gallery & User Profile
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

try {
    echo "Starting seeding random hotel images and profile photos...\n";

    $destDir = storage_path('app/public/hotel-gallery');
    $profileDir = storage_path('app/public/profile-photos');

    if (!file_exists($destDir)) {
        mkdir($destDir, 0755, true);
    }
    if (!file_exists($profileDir)) {
        mkdir($profileDir, 0755, true);
    }

    $hotels = Hotel::all();
    if ($hotels->isEmpty()) {
        echo "No hotels found.\n";
        exit(1);
    }

    $sampleImages = [
        [
            'url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'facade.jpg',
            'is_primary' => true,
        ],
        [
            'url' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'luxury_suite.jpg',
            'is_primary' => false,
        ],
        [
            'url' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'bathroom.jpg',
            'is_primary' => false,
        ],
        [
            'url' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'lobby.jpg',
            'is_primary' => false,
        ],
        [
            'url' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'infinity_pool.jpg',
            'is_primary' => false,
        ],
        [
            'url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'restaurant.jpg',
            'is_primary' => false,
        ],
        [
            'url' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'lounge_bar.jpg',
            'is_primary' => false,
        ],
        [
            'url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=80',
            'filename' => 'spa_wellness.jpg',
            'is_primary' => false,
        ],
    ];

    foreach ($hotels as $hotel) {
        HotelImage::where('hotel_id', $hotel->id)->delete();

        foreach ($sampleImages as $img) {
            HotelImage::create([
                'hotel_id' => $hotel->id,
                'image_path' => $img['url'],
                'is_primary' => $img['is_primary'],
            ]);
            echo "Registered gallery image for hotel ID {$hotel->id}: {$img['filename']}\n";
        }
    }

    // Also update users profile photo if empty
    $users = User::all();
    $avatars = [
        'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=600&q=80',
        'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600&q=80',
    ];

    foreach ($users as $index => $u) {
        $avatarUrl = $avatars[$index % count($avatars)];
        $u->update(['profile_photo_path' => $avatarUrl]);
        echo "Updated profile photo for user {$u->name} ({$u->email})\n";
    }

    echo "Seeding completed successfully!\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
