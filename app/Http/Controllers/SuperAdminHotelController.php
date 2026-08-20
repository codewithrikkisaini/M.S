<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\SubscriptionInvoice;
use App\Models\ActivityLog;
use App\Services\SubscriptionService;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeActivationMail;
use App\Mail\PaidInvoiceCreatedMail;

class SuperAdminHotelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::with(['subscriptions.plan', 'subscriptionInvoices', 'users']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('hotel_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($status === 'pending_approval') {
                $query->where('account_status', 'pending_approval');
            } elseif ($status === 'active') {
                $query->where('account_status', 'active');
            } elseif ($status === 'suspended') {
                $query->where('account_status', 'suspended');
            }
        }

        $hotels = $query->orderBy('created_at', 'desc')->paginate(15);
        $pendingCount = Hotel::where('account_status', 'pending_approval')->count();

        return view('superadmin.hotels.index', compact('hotels', 'pendingCount'));
    }

    public function show(Hotel $hotel)
    {
        $hotel->load(['subscriptions.plan', 'subscriptionInvoices', 'users']);
        $auditLogs = ActivityLog::where('hotel_id', $hotel->id)->orderBy('created_at', 'desc')->get();

        return view('superadmin.hotels.show', compact('hotel', 'auditLogs'));
    }

    public function approve7DayTrial(Hotel $hotel, SubscriptionService $subscriptionService)
    {
        try {
            $subscriptionService->approve7DayTrial($hotel, request('notes'));
            return redirect()->back()->with('success', "Hotel {$hotel->name} approved with 7-Day Free Trial!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed approving trial: " . $e->getMessage());
        }
    }

    public function approve15DayTrial(Hotel $hotel, SubscriptionService $subscriptionService)
    {
        try {
            $subscriptionService->approve15DayTrial($hotel, request('notes'));
            return redirect()->back()->with('success', "Hotel {$hotel->name} approved with 15-Day Free Trial!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed approving trial: " . $e->getMessage());
        }
    }

    public function createPaidSubscription(Request $request, Hotel $hotel, SubscriptionService $subscriptionService)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'size:3'],
            'billing_cycle' => ['required', 'string', 'in:monthly,quarterly,annual'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $invoice = $subscriptionService->createPaidSubscription(
                $hotel,
                (float) $validated['amount'],
                strtoupper($validated['currency']),
                $validated['billing_cycle'],
                $validated['notes'] ?? null
            );

            return redirect()->back()->with('success', "Paid Subscription Invoice #{$invoice->invoice_number} created and sent to {$hotel->email}!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed creating subscription: " . $e->getMessage());
        }
    }

    public function extendTrial(Request $request, Hotel $hotel, SubscriptionService $subscriptionService)
    {
        $validated = $request->validate([
            'additional_days' => ['required', 'integer', 'min:1', 'max:365'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $subscriptionService->extendTrial($hotel, (int)$validated['additional_days'], $validated['notes'] ?? null);
            return redirect()->back()->with('success', "Trial for {$hotel->name} extended by {$validated['additional_days']} days!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed extending trial: " . $e->getMessage());
        }
    }

    public function suspend(Request $request, Hotel $hotel, SubscriptionService $subscriptionService)
    {
        try {
            $subscriptionService->suspendHotel($hotel, $request->input('reason'));
            return redirect()->back()->with('success', "Hotel {$hotel->name} has been suspended.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed suspending hotel: " . $e->getMessage());
        }
    }

    public function activate(Request $request, Hotel $hotel, SubscriptionService $subscriptionService)
    {
        try {
            $subscriptionService->activateHotel($hotel, $request->input('notes'));
            return redirect()->back()->with('success', "Hotel {$hotel->name} has been activated.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed activating hotel: " . $e->getMessage());
        }
    }

    public function resendInvoice(Hotel $hotel, SubscriptionInvoice $invoice)
    {
        try {
            Mail::to($hotel->email)->send(new PaidInvoiceCreatedMail($hotel, $invoice));
            ActivityLog::logAdminAction($hotel, 'Resend Invoice Link', null, null, "Resent Invoice #{$invoice->invoice_number}");
            return redirect()->back()->with('success', "Invoice #{$invoice->invoice_number} link resent to {$hotel->email}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed resending invoice: " . $e->getMessage());
        }
    }

    public function resendWelcome(Hotel $hotel)
    {
        try {
            Mail::to($hotel->email)->send(new WelcomeActivationMail($hotel));
            ActivityLog::logAdminAction($hotel, 'Resend Welcome Email', null, null, "Resent Welcome email");
            return redirect()->back()->with('success', "Welcome email resent to {$hotel->email}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed resending welcome mail: " . $e->getMessage());
        }
    }

    public function update(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'rooms_count' => ['nullable', 'integer', 'min:1'],
            'account_status' => ['required', 'string', 'in:pending_approval,active,suspended'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        try {
            // Check if email is already taken by another hotel's user or superadmin
            $existingUser = \App\Models\User::where('email', $validated['email'])
                ->where(function($q) use ($hotel) {
                    $q->whereNull('hotel_id')->orWhere('hotel_id', '!=', $hotel->id);
                })->first();

            if ($existingUser) {
                return redirect()->back()->with('error', "The email address '{$validated['email']}' is already in use by another account.");
            }

            $oldEmail = $hotel->email;
            $hotel->update($validated);

            if ($validated['account_status'] === 'active') {
                $hotel->status = 'approved';
            } elseif ($validated['account_status'] === 'suspended') {
                $hotel->status = 'suspended';
            } elseif ($validated['account_status'] === 'pending_approval') {
                $hotel->status = 'pending';
            }
            $hotel->save();

            if ($validated['account_status'] === 'suspended') {
                $hotel->users()->update(['status' => 'inactive']);
            } elseif ($validated['account_status'] === 'active') {
                $hotel->users()->update(['status' => 'active']);
            }

            // Sync Hotel Admin User Login Credentials (Email & Password)
            $adminUser = \App\Models\User::where('hotel_id', $hotel->id)
                ->whereHas('role', fn($q) => $q->where('slug', 'admin'))
                ->first();

            if (!$adminUser) {
                $adminUser = \App\Models\User::where('hotel_id', $hotel->id)
                    ->where('email', $oldEmail)
                    ->first() 
                    ?? \App\Models\User::where('hotel_id', $hotel->id)->first();
            }

            if ($adminUser) {
                $adminUser->email = $validated['email'];
                if ($request->filled('password')) {
                    $adminUser->password = \Illuminate\Support\Facades\Hash::make($request->password);
                }
                $adminUser->save();
            }

            ActivityLog::logAdminAction($hotel, 'Update Hotel Record', null, null, "Updated hotel profile & login credentials (Email: {$validated['email']}) by SuperAdmin");

            return redirect()->back()->with('success', "Hotel '{$hotel->name}' record and login credentials updated successfully! New Email: {$validated['email']}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed updating hotel: " . $e->getMessage());
        }
    }

    public function destroy(Hotel $hotel)
    {
        try {
            $name = $hotel->name;
            $code = $hotel->hotel_code;

            \Illuminate\Support\Facades\DB::transaction(function () use ($hotel) {
                // Delete document files from storage
                $documents = \App\Models\HotelDocument::where('hotel_id', $hotel->id)->get();
                foreach ($documents as $doc) {
                    $path = $doc->storage_path . '/' . $doc->stored_filename;
                    if (\Illuminate\Support\Facades\Storage::disk($doc->disk)->exists($path)) {
                        \Illuminate\Support\Facades\Storage::disk($doc->disk)->delete($path);
                    }
                }

                // Delete associated relations
                \App\Models\DocumentAuditLog::where('hotel_id', $hotel->id)->delete();
                $hotel->documents()->delete();
                $hotel->subscriptions()->delete();
                $hotel->subscriptionInvoices()->delete();
                ActivityLog::where('hotel_id', $hotel->id)->delete();
                $hotel->delete();
            });

            return redirect()->route('superadmin.hotels.index')->with('success', "Hotel '{$name}' ({$code}) has been permanently deleted.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed deleting hotel: " . $e->getMessage());
        }
    }
}
