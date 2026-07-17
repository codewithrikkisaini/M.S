<?php

use Livewire\Component;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use App\Models\Hotel;

new class extends Component
{
    public $invoices;
    public $plans;
    public $hotels;
    
    public $showCreateModal = false;
    
    // Form fields for manually creating a SaaS invoice
    public $hotel_id;
    public $subscription_plan_id;
    public $amount;
    public $invoice_number;
    public $status = 'unpaid';
    public $billing_date;
    public $due_date;
    public $payment_method = 'Manual';

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->invoices = SubscriptionInvoice::withoutGlobalScope('tenant')
            ->with(['hotel', 'plan'])
            ->latest()
            ->get();
            
        $this->plans = SubscriptionPlan::all();
        $this->hotels = Hotel::all();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->invoice_number = 'SUB-' . date('Ymd') . '-' . rand(1000, 9999);
        $this->billing_date = date('Y-m-d');
        $this->due_date = date('Y-m-d', strtotime('+7 days'));
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
    }

    public function resetForm(): void
    {
        $this->hotel_id = '';
        $this->subscription_plan_id = '';
        $this->amount = '';
        $this->invoice_number = '';
        $this->status = 'unpaid';
        $this->billing_date = '';
        $this->due_date = '';
        $this->payment_method = 'Manual';
    }

    public function updatedSubscriptionPlanId($value): void
    {
        if ($value) {
            $plan = SubscriptionPlan::find($value);
            if ($plan) {
                $this->amount = $plan->price;
            }
        }
    }

    public function saveInvoice(): void
    {
        $rules = [
            'hotel_id' => 'required|exists:hotels,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'invoice_number' => 'required|string|unique:subscription_invoices,invoice_number',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:paid,unpaid,pending,refunded',
            'billing_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:billing_date',
            'payment_method' => 'required|string',
        ];

        $this->validate($rules);

        $data = [
            'hotel_id' => $this->hotel_id,
            'subscription_plan_id' => $this->subscription_plan_id,
            'invoice_number' => $this->invoice_number,
            'amount' => $this->amount,
            'status' => $this->status,
            'billing_date' => $this->billing_date,
            'due_date' => $this->due_date,
            'payment_method' => $this->payment_method,
            'paid_at' => $this->status === 'paid' ? now() : null,
        ];

        SubscriptionInvoice::create($data);

        $this->closeCreateModal();
        $this->loadData();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Subscription invoice created successfully!"
        ]);
    }

    public function markAsPaid($id): void
    {
        $invoice = SubscriptionInvoice::withoutGlobalScope('tenant')->findOrFail($id);
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
        
        $this->loadData();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "Invoice #{$invoice->invoice_number} marked as Paid."
        ]);
    }

    public function deleteInvoice($id): void
    {
        $invoice = SubscriptionInvoice::withoutGlobalScope('tenant')->findOrFail($id);
        $invoice->delete();
        $this->loadData();

        $this->dispatch('toast', [
            'type' => 'info',
            'message' => "Invoice has been deleted."
        ]);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
