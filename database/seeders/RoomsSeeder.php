<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Housekeeping;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoomsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get all hotels or create demo hotel if none exist
        $hotels = Hotel::all();

        if ($hotels->isEmpty()) {
            $defaultHotel = Hotel::create([
                'name' => 'The Imperial Hotel',
                'slug' => 'the-imperial-hotel',
                'code' => 'IMPERIAL01',
                'hotel_code' => 'LDG-000001',
                'email' => 'contact@imperialhotel.com',
                'phone' => '+91 98765 43210',
                'address' => '15 Royal Palm Avenue, Marine Drive',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'postal_code' => '400020',
                'status' => 'approved',
                'account_status' => 'active',
            ]);
            $hotels = collect([$defaultHotel]);
        }

        // Room photos library (Unsplash high quality interior & suites)
        $photoPool = [
            [
                'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80'
            ],
            [
                'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1000&q=80'
            ],
            [
                'https://images.unsplash.com/photo-1591088398332-8a7791972843?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=1000&q=80'
            ],
            [
                'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1540518614846-7ede433c4550?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1000&q=80'
            ],
            [
                'https://images.unsplash.com/photo-1507652313519-d4e9174996dd?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=1000&q=80',
                'https://images.unsplash.com/photo-1576671081837-49000212a370?auto=format&fit=crop&w=1000&q=80'
            ]
        ];

        // Template room types with bed types & options
        $roomBlueprints = [
            [
                'type_name' => 'Single Room',
                'base_price' => 1800,
                'bed_types' => ['Single Bed', 'Single Cozy Bed', 'Solo Executive Bed'],
                'capacities' => [1],
                'options' => ['Non-Smoking', 'Free High-Speed Wi-Fi', 'City View'],
                'description' => 'Cozy modern single room featuring ergonomic workspace, high-speed Wi-Fi, air conditioning, and en-suite rainfall shower.'
            ],
            [
                'type_name' => 'Standard Double',
                'base_price' => 2800,
                'bed_types' => ['Double Bed', 'Queen Bed', 'Twin Beds'],
                'capacities' => [2],
                'options' => ['Non-Smoking', 'Breakfast Included', 'Flat TV', 'Free Wi-Fi'],
                'description' => 'Spacious double accommodation designed with acoustic soundproofing, plush orthopedic mattress, HD Smart TV, and luxury tea/coffee station.'
            ],
            [
                'type_name' => 'Deluxe King',
                'base_price' => 3900,
                'bed_types' => ['King Bed', 'California King', 'King Bed and Rolling Bed for Extra Guest'],
                'capacities' => [2, 3],
                'options' => ['Handicap Non-Smoking', 'Balcony Sea View', 'AC', 'Breakfast Included'],
                'description' => 'Premium deluxe king suite featuring panoramic floor-to-ceiling windows, private balcony, marble bathroom, and 24/7 room service.'
            ],
            [
                'type_name' => 'Twin Beds Suite',
                'base_price' => 3200,
                'bed_types' => ['Twin Beds', '2 Double / Twin Beds', 'Triple Single Beds'],
                'capacities' => [2, 3],
                'options' => ['Non-Smoking', 'Pool View', 'Free Wi-Fi', 'Mini Bar'],
                'description' => 'Perfect for friends or colleagues, offering two premium twin mattresses, individual reading lights, mini refrigerator, and electronic safe.'
            ],
            [
                'type_name' => 'Junior Suite with Jacuzzi',
                'base_price' => 5500,
                'bed_types' => ['Junior Suite with Sofa and jacuzzi', 'King Bed with Jacuzzi', 'Luxury Honeymoon Bed'],
                'capacities' => [2, 3],
                'options' => ['Suites with Jacuzzi Hot Tub', 'Handicap Non-Smoking', 'Private Balcony', 'Welcome Wine'],
                'description' => 'Romantic and opulent suite featuring an en-suite whirlpool Jacuzzi tub, plush sectional sofa, designer vanity, and mood ambient lighting.'
            ],
            [
                'type_name' => 'Family Suite',
                'base_price' => 6200,
                'bed_types' => ['2 Double / Twin Beds', 'King Bed and Bunk Beds', 'Family Triple Beds'],
                'capacities' => [4, 5],
                'options' => ['Non-Smoking', 'Breakfast Included', 'Connecting Rooms', 'Kids Play Corner'],
                'description' => 'Expansive family room with separate living space, two large double beds, kids amenities, dual bathrooms, and streaming smart TV.'
            ],
            [
                'type_name' => 'Executive Suite',
                'base_price' => 7800,
                'bed_types' => ['Executive King Suite', 'California King', 'King Bed and Rolling Bed for Extra Guest'],
                'capacities' => [2, 3],
                'options' => ['Executive Lounge Access', 'Espresso Machine', 'High Floor City Skyline', 'Bathtub'],
                'description' => 'High-floor executive suite with private boardroom desk, Nespresso machine, luxury bathtub, complimentary lounge cocktails, and express check-in.'
            ],
            [
                'type_name' => 'Apartment Suite',
                'base_price' => 8500,
                'bed_types' => ['California King', '2 Double Beds', 'Twin Beds with Kitchenette'],
                'capacities' => [3, 4],
                'options' => ['Kitchenette', 'Washing Machine', 'Dining Area', 'Non-Smoking'],
                'description' => 'Fully furnished luxury serviced apartment with induction kitchenette, dining counter, washer-dryer, living lounge, and master bedroom.'
            ],
            [
                'type_name' => 'Presidential Suite',
                'base_price' => 14500,
                'bed_types' => ['Presidential Suite Bed', 'Royal Grand King', 'Master California King'],
                'capacities' => [4, 6],
                'options' => ['Private Butler 24/7', 'Private Jacuzzi Hot Tub', 'Terrace Panoramic View', 'VIP Airport Transfer'],
                'description' => 'The pinnacle of luxury hospitality. Features 180-degree skyline views, grand master bedroom, walk-in dressing room, dining for 6, and dedicated butler.'
            ]
        ];

        foreach ($hotels as $hotel) {
            $this->command?->info("Seeding 50+ rooms for hotel: {$hotel->name} (ID: {$hotel->id})");

            // 1. Create or retrieve Room Types for this hotel
            $createdTypes = [];
            foreach ($roomBlueprints as $bp) {
                $type = RoomType::withoutGlobalScope('tenant')->updateOrCreate(
                    [
                        'hotel_id' => $hotel->id,
                        'name' => $bp['type_name'],
                    ],
                    [
                        'base_price' => $bp['base_price'],
                        'description' => $bp['description'],
                    ]
                );
                $createdTypes[$bp['type_name']] = [
                    'model' => $type,
                    'blueprint' => $bp,
                ];
            }

            // 2. Generate 52 dynamic rooms (across Floors 1 to 6)
            // Rooms: 101-110 (Floor 1), 201-210 (Floor 2), 301-310 (Floor 3), 401-410 (Floor 4), 501-512 (Floor 5)
            $floors = [
                1 => range(101, 110),
                2 => range(201, 210),
                3 => range(301, 310),
                4 => range(401, 410),
                5 => range(501, 512),
            ];

            $roomIndex = 0;
            $blueprintsList = array_values($createdTypes);

            foreach ($floors as $floorNum => $roomNumbers) {
                foreach ($roomNumbers as $rNum) {
                    $bpItem = $blueprintsList[$roomIndex % count($blueprintsList)];
                    $typeModel = $bpItem['model'];
                    $bp = $bpItem['blueprint'];

                    $bedType = $bp['bed_types'][$roomIndex % count($bp['bed_types'])];
                    $capacity = $bp['capacities'][$roomIndex % count($bp['capacities'])];
                    $photos = $photoPool[$roomIndex % count($photoPool)];

                    // Options variation
                    $optSlice = array_slice($bp['options'], 0, min(3, count($bp['options'])));
                    if ($roomIndex % 2 === 0 && !in_array('Breakfast Included', $optSlice)) {
                        $optSlice[] = 'Breakfast Included';
                    }
                    $optionsStr = implode(', ', array_unique($optSlice));

                    // Price with slight realistic variance per floor
                    $price = $typeModel->base_price + (($floorNum - 1) * 200);

                    // Dynamic description customized with room number
                    $desc = "Experience world-class hospitality in Room {$rNum}. Designed with contemporary luxury aesthetics, acoustic soundproof windows, premium {$bedType}, en-suite bathroom with luxury toiletries, and 24/7 room service.";

                    $room = Room::withoutGlobalScope('tenant')->updateOrCreate(
                        [
                            'hotel_id' => $hotel->id,
                            'room_number' => (string) $rNum,
                        ],
                        [
                            'room_type_id' => $typeModel->id,
                            'floor' => $floorNum,
                            'bed_type' => $bedType,
                            'capacity' => $capacity,
                            'price' => $price,
                            'status' => 'Available',
                            'room_option' => $optionsStr,
                            'description' => $desc,
                            'image_path' => json_encode($photos),
                        ]
                    );

                    // Ensure Clean housekeeping status
                    Housekeeping::withoutGlobalScope('tenant')->updateOrCreate(
                        [
                            'hotel_id' => $hotel->id,
                            'room_id' => $room->id,
                        ],
                        [
                            'status' => 'Clean',
                            'priority' => 'Normal',
                            'notes' => 'Routine sanitization and housekeeping inspection completed.',
                            'inspected_at' => now(),
                        ]
                    );

                    $roomIndex++;
                }
            }

            // Update hotel rooms_count
            $hotel->update(['rooms_count' => Room::withoutGlobalScope('tenant')->where('hotel_id', $hotel->id)->count()]);
        }
    }
}


