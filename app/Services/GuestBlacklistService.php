<?php

namespace App\Services;

use App\Models\GuestBlacklist;
use App\Models\Guest;
use App\Models\GuestBlacklistDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuestBlacklistService
{
    /**
     * Check if a guest is currently blacklisted.
     * Uses 3-level identity matching.
     */
    public function isGuestBlacklisted(Guest $guest): ?GuestBlacklist
    {
        $hotelId = Auth::user()->hotel_id;
        if (!$hotelId) {
            return null;
        }

        // Level 1: Direct guest_id match
        $match = GuestBlacklist::active()
            ->where('hotel_id', $hotelId)
            ->where('guest_id', $guest->id)
            ->first();
        if ($match) {
            return $match;
        }

        // Level 2: ID number match
        $idNumber = $guest->id_number ?? $guest->passport_number ?? null;
        if ($idNumber) {
            $match = GuestBlacklist::active()
                ->where('hotel_id', $hotelId)
                ->where('id_number', 'like', trim($idNumber))
                ->first();
            if ($match) {
                return $match;
            }
        }

        // Level 3: Name + DOB match (case-insensitive)
        $nameParts = explode(' ', trim($guest->name), 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        if ($firstName && $lastName) {
            $match = GuestBlacklist::active()
                ->where('hotel_id', $hotelId)
                ->whereRaw('LOWER(first_name) = ?', [strtolower($firstName)])
                ->whereRaw('LOWER(last_name) = ?', [strtolower($lastName)])
                ->first();
            if ($match) {
                return $match;
            }
        }

        return null;
    }

    /**
     * Check if identity data matches any active blacklist.
     * Used for new guests during reservation creation.
     */
    public function isIdentityBlacklisted(
        string $firstName,
        string $lastName,
        ?string $idNumber = null,
        ?string $dateOfBirth = null
    ): ?GuestBlacklist {
        $hotelId = Auth::user()->hotel_id;
        if (!$hotelId) {
            return null;
        }

        // Level 1: ID number match
        if ($idNumber) {
            $match = GuestBlacklist::active()
                ->where('hotel_id', $hotelId)
                ->where('id_number', 'like', trim($idNumber))
                ->first();
            if ($match) {
                return $match;
            }
        }

        // Level 2: Name + DOB match
        if ($firstName && $lastName) {
            $query = GuestBlacklist::active()
                ->where('hotel_id', $hotelId)
                ->whereRaw('LOWER(first_name) = ?', [strtolower(trim($firstName))])
                ->whereRaw('LOWER(last_name) = ?', [strtolower(trim($lastName))]);

            if ($dateOfBirth) {
                $query->where('date_of_birth', $dateOfBirth);
            }

            $match = $query->first();
            if ($match) {
                return $match;
            }
        }

        return null;
    }

    /**
     * Check for duplicate active blacklist for the same identity.
     */
    public function hasActiveBlacklist(
        ?int $guestId = null,
        ?string $idNumber = null,
        ?string $firstName = null,
        ?string $lastName = null,
        ?int $excludeId = null
    ): bool {
        $hotelId = Auth::user()->hotel_id;
        if (!$hotelId) {
            return false;
        }

        $query = GuestBlacklist::active()->where('hotel_id', $hotelId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($guestId) {
            if ($query->clone()->where('guest_id', $guestId)->exists()) {
                return true;
            }
        }

        if ($idNumber) {
            if ($query->clone()->where('id_number', 'like', trim($idNumber))->exists()) {
                return true;
            }
        }

        if ($firstName && $lastName) {
            if ($query->clone()
                ->whereRaw('LOWER(first_name) = ?', [strtolower(trim($firstName))])
                ->whereRaw('LOWER(last_name) = ?', [strtolower(trim($lastName))])
                ->exists()
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Release a blacklist entry with reason and optional documents.
     */
    public function releaseBlacklist(
        int $blacklistId,
        string $releaseReason,
        ?string $releaseNotes = null,
        ?array $releaseDocuments = null
    ): GuestBlacklist {
        $blacklist = GuestBlacklist::findOrFail($blacklistId);

        DB::transaction(function () use ($blacklist, $releaseReason, $releaseNotes, $releaseDocuments) {
            $blacklist->update([
                'status' => 'released',
                'release_reason' => $releaseReason,
                'release_notes' => $releaseNotes,
                'released_by' => Auth::id(),
                'released_at' => now(),
            ]);

            // Handle release document uploads
            if ($releaseDocuments) {
                foreach ($releaseDocuments as $file) {
                    if ($file) {
                        $originalName = $file->getClientOriginalName();
                        $storedName = Str::random(20) . '.' . $file->getClientOriginalExtension();
                        $file->storeAs('blacklist-documents', $storedName, 'local');

                        GuestBlacklistDocument::create([
                            'guest_blacklist_id' => $blacklist->id,
                            'hotel_id'           => $blacklist->hotel_id,
                            'original_filename'  => $originalName,
                            'stored_filename'    => $storedName,
                            'disk'               => 'local',
                            'storage_path'       => 'blacklist-documents',
                            'mime_type'          => $file->getMimeType(),
                            'file_size'          => $file->getSize(),
                            'category'           => 'Release Evidence',
                            'uploaded_by'        => Auth::id(),
                        ]);
                    }
                }
            }

            \App\Models\ActivityLog::log(
                'Blacklist Released',
                "Blacklist case {$blacklist->case_number} for {$blacklist->first_name} {$blacklist->last_name} has been released. Reason: {$releaseReason}"
            );
        });

        return $blacklist->fresh();
    }

    /**
     * Re-blacklist a previously released guest (creates new case).
     */
    public function reBlacklist(int $previousBlacklistId): GuestBlacklist
    {
        $previous = GuestBlacklist::findOrFail($previousBlacklistId);

        return GuestBlacklist::create([
            'hotel_id'       => $previous->hotel_id,
            'guest_id'       => $previous->guest_id,
            'first_name'     => $previous->first_name,
            'last_name'      => $previous->last_name,
            'id_type'        => $previous->id_type,
            'id_number'      => $previous->id_number,
            'date_of_birth'  => $previous->date_of_birth,
            'reason'         => $previous->reason,
            'status'         => 'active',
            'blacklisted_by' => Auth::id(),
        ]);
    }

    /**
     * Get blacklist history for a guest.
     */
    public function getGuestBlacklistHistory(int $guestId, int $hotelId): \Illuminate\Database\Eloquent\Collection
    {
        return GuestBlacklist::where('hotel_id', $hotelId)
            ->where('guest_id', $guestId)
            ->with(['blacklister', 'releaser', 'documents'])
            ->latest()
            ->get();
    }

    /**
     * Get blacklist summary statistics for dashboard.
     */
    public function getBlacklistStats(int $hotelId): array
    {
        $now = now();
        return [
            'active' => GuestBlacklist::active()->where('hotel_id', $hotelId)->count(),
            'released_this_month' => GuestBlacklist::released()
                ->where('hotel_id', $hotelId)
                ->whereMonth('released_at', $now->month)
                ->whereYear('released_at', $now->year)
                ->count(),
            'total_cases' => GuestBlacklist::where('hotel_id', $hotelId)->count(),
            'blacklisted_this_month' => GuestBlacklist::where('hotel_id', $hotelId)
                ->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->count(),
        ];
    }
}
