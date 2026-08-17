<?php

namespace App\Services;

use App\Models\GuestBlacklist;
use App\Models\Guest;
use Illuminate\Support\Facades\Auth;

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
     * Remove (soft-delete) a blacklist entry.
     */
    public function removeBlacklist(int $blacklistId): GuestBlacklist
    {
        $blacklist = GuestBlacklist::findOrFail($blacklistId);
        $blacklist->update([
            'status' => 'removed',
            'removed_by' => Auth::id(),
            'removed_at' => now(),
        ]);

        return $blacklist;
    }

    /**
     * Restore a removed blacklist entry.
     */
    public function restoreBlacklist(int $blacklistId): GuestBlacklist
    {
        $blacklist = GuestBlacklist::findOrFail($blacklistId);
        $blacklist->update([
            'status' => 'active',
            'removed_by' => null,
            'removed_at' => null,
        ]);

        return $blacklist;
    }
}
