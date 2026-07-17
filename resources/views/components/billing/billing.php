<?php

use Livewire\Component;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionInvoice;
use App\Models\Room;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public $activeSub;
    public $plans;
    public $invoices;

    // Usage metrics
    public $roomsCount = 0;
    public $usersCount = 0;

    // Simulation Upgrade Modal
    public $showUpgradeModal = false;
    public $selectedPlan;
    
    // Card simulation fields
    public $cardNumber;
    public $cardExpiry;
    public $cardCvc;
    public $cardName;

    // Selected invoice details for print view modal
    public $selectedInvoice;
    public $showInvoiceModal = false;

    public function mount(): void
    {
        $this->loadBillingData();
    }

    public function loadBillingData(): void
    {
        $hotelId = Auth::user()->hotel_id;

        // Load active subscription (trialing or active)
        $this->activeSub = Subscription::where('hotel_id', $hotelId)
            ->whereIn('status', ['active', 'trialing'])
            ->with('plan')
            ->latest()
            ->first();

        // Load all available plans for upgrade
        $this->plans = SubscriptionPlan::where('status', 'active')->get();

        // Load past subscription invoices
        $this->invoices = SubscriptionInvoice::where('hotel_id', $hotelId)
            ->with('plan')
            ->latest()
            ->get();

        // Calculate usage metrics
        $this->roomsCount = Room::count();
        $this->usersCount = User::count();
    }

    public function selectPlan($planId): void
    {
        $this->selectedPlan = SubscriptionPlan::findOrFail($planId);
        $this->cardName = Auth::user()->name;
        $this->cardNumber = '';
        $this->cardExpiry = '';
        $this->cardCvc = '';
        $this->showUpgradeModal = true;
    }

    public function closeUpgradeModal(): void
    {
        $this->showUpgradeModal = false;
    }

    public function processUpgrade(): void
    {
        $this->validate([
            'cardName' => 'required|string|max:255',
            'cardNumber' => 'required|string|min:16|max:19',
            'cardExpiry' => 'required|string|min:5|max:7',
            'cardCvc' => 'required|string|min:3|max:4',
        ]);

        $hotelId = Auth::user()->hotel_id;
        $now = now();

        // 1. Expire/cancel any previous active subscriptions
        Subscription::where('hotel_id', $hotelId)
            ->whereIn('status', ['active', 'trialing'])
            ->update([
                'status' => 'cancelled',
                'cancelled_at' => $now,
            ]);

        // 2. Determine end date based on billing cycle
        $endsAt = null;
        if ($this->selectedPlan->billing_cycle === 'monthly') {
            $endsAt = $now->copy()->addMonth();
        } elseif ($this->selectedPlan->billing_cycle === 'yearly') {
            $endsAt = $now->copy()->addYear();
        } elseif ($this->selectedPlan->billing_cycle === 'trial') {
            $endsAt = $now->copy()->addDays($this->selectedPlan->trial_days);
        } // lifetime stays null

        // 3. Create new subscription
        Subscription::create([
            'hotel_id' => $hotelId,
            'subscription_plan_id' => $this->selectedPlan->id,
            'status' => 'active',
            'starts_at' => $now,
            'ends_at' => $endsAt,
            'trial_ends_at' => $this->selectedPlan->billing_cycle === 'trial' ? $endsAt : null,
        ]);

        // 4. Generate Paid Subscription Invoice
        $invoiceNumber = 'SUB-' . date('Ymd') . '-' . rand(1000, 9999);
        SubscriptionInvoice::create([
            'hotel_id' => $hotelId,
            'subscription_plan_id' => $this->selectedPlan->id,
            'invoice_number' => $invoiceNumber,
            'amount' => $this->selectedPlan->price,
            'status' => 'paid',
            'billing_date' => $now->format('Y-m-d'),
            'due_date' => $now->format('Y-m-d'),
            'paid_at' => $now,
            'payment_method' => 'Credit Card',
        ]);

        $this->closeUpgradeModal();
        $this->loadBillingData();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Upgrade to '{$this->selectedPlan->name}' successful! Your card was charged."
        ]);
    }

    public function viewInvoiceDetails($id): void
    {
        $this->selectedInvoice = SubscriptionInvoice::where('hotel_id', Auth::user()->hotel_id)
            ->with(['hotel', 'plan'])
            ->findOrFail($id);
        $this->showInvoiceModal = true;
    }

    public function closeInvoiceModal(): void
    {
        $this->showInvoiceModal = false;
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
