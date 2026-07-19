<?php

// Seed Gallery script
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Hotel;
use App\Models\HotelImage;
use Illuminate\Support\Facades\Storage;

try {
    $hotel = Hotel::first();
    if (!$hotel) {
        echo "No hotel found to seed gallery images.";
        exit(1);
    }

    $destDir = storage_path('app/public/hotel-gallery');
    if (!file_exists($destDir)) {
        mkdir($destDir, 0755, true);
    }

    // Source images
    $facadeSrc = '/home/rikki-saini/.gemini/antigravity/brain/35907c7b-c11a-495b-8af9-46d141d94b72/hotel_facade_1784451031026.png';
    $roomSrc = '/home/rikki-saini/.gemini/antigravity/brain/35907c7b-c11a-495b-8af9-46d141d94b72/hotel_room_1784451046943.png';
    $bathroomSrc = '/home/rikki-saini/.gemini/antigravity/brain/35907c7b-c11a-495b-8af9-46d141d94b72/hotel_bathroom_1784451062234.png';

    // Clear existing images in db
    HotelImage::where('hotel_id', $hotel->id)->delete();

    // Map files
    $files = [
        ['src' => $facadeSrc, 'name' => 'facade.png', 'primary' => true],
        ['src' => $roomSrc, 'name' => 'room.png', 'primary' => false],
        ['src' => $bathroomSrc, 'name' => 'bathroom.png', 'primary' => false],
        ['src' => $roomSrc, 'name' => 'lobby.png', 'primary' => false],
        ['src' => $bathroomSrc, 'name' => 'spa.png', 'primary' => false],
        ['src' => $roomSrc, 'name' => 'suite.png', 'primary' => false],
        ['src' => $bathroomSrc, 'name' => 'restaurant.png', 'primary' => false],
        ['src' => $facadeSrc, 'name' => 'exterior.png', 'primary' => false],
        ['src' => $roomSrc, 'name' => 'lounge.png', 'primary' => false],
    ];

    foreach ($files as $file) {
        if (file_exists($file['src'])) {
            copy($file['src'], $destDir . '/' . $file['name']);
            HotelImage::create([
                'hotel_id' => $hotel->id,
                'image_path' => 'hotel-gallery/' . $file['name'],
                'is_primary' => $file['primary']
            ]);
            echo "Copied and registered: " . $file['name'] . "\n";
        } else {
            echo "Source not found: " . $file['src'] . "\n";
        }
    }

    echo "Gallery seeded successfully.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
