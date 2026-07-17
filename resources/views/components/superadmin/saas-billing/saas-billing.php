<?php

use Livewire\Component;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Hotel;

new class extends Component
{
    public $subscriptions;
    public $plans;
    public $hotels;
    public $showModal = false;
    public $isEdit = false;
    public $subId;

    // Form fields
    public $hotel_id;
    public $subscription_plan_id;
    public $status = 'active';
    public $starts_at;
    public $ends_at;
    public $trial_ends_at;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        // Disable tenant scope because Superadmin sees all subscriptions
        $this->subscriptions = Subscription::withoutGlobalScope('tenant')
            ->with(['hotel', 'plan'])
            ->latest()
            ->get();
            
        $this->plans = SubscriptionPlan::where('status', 'active')->get();
        $this->hotels = Hotel::all();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->starts_at = now()->format('Y-m-d H:i');
        $this->showModal = true;
    }

    public function openEditModal($id): void
    {
        $this->resetForm();
        $sub = Subscription::withoutGlobalScope('tenant')->findOrFail($id);
        $this->subId = $sub->id;
        $this->hotel_id = $sub->hotel_id;
        $this->subscription_plan_id = $sub->subscription_plan_id;
        $this->status = $sub->status;
        $this->starts_at = $sub->starts_at ? $sub->starts_at->format('Y-m-d\TH:i') : '';
        $this->ends_at = $sub->ends_at ? $sub->ends_at->format('Y-m-d\TH:i') : '';
        $this->trial_ends_at = $sub->trial_ends_at ? $sub->trial_ends_at->format('Y-m-d\TH:i') : '';

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function resetForm(): void
    {
        $this->subId = null;
        $this->hotel_id = '';
        $this->subscription_plan_id = '';
        $this->status = 'active';
        $this->starts_at = '';
        $this->ends_at = '';
        $this->trial_ends_at = '';
    }

    public function saveSubscription(): void
    {
        $rules = [
            'hotel_id' => 'required|exists:hotels,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'status' => 'required|string|in:trialing,active,expired,cancelled',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'trial_ends_at' => 'nullable|date|after_or_equal:starts_at',
        ];

        $this->validate($rules);

        $data = [
            'hotel_id' => $this->hotel_id,
            'subscription_plan_id' => $this->subscription_plan_id,
            'status' => $this->status,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at ?: null,
            'trial_ends_at' => $this->trial_ends_at ?: null,
        ];

        if ($this->status === 'cancelled') {
            $data['cancelled_at'] = now();
        } else {
            $data['cancelled_at'] = null;
        }

        if ($this->isEdit) {
            $sub = Subscription::withoutGlobalScope('tenant')->findOrFail($this->subId);
            $sub->update($data);
            $message = "Subscription updated successfully!";
        } else {
            Subscription::create($data);
            $message = "Subscription created successfully!";
        }

        $this->closeModal();
        $this->loadData();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    public function deleteSubscription($id): void
    {
        $sub = Subscription::withoutGlobalScope('tenant')->findOrFail($id);
        $sub->delete();
        $this->loadData();

        $this->dispatch('toast', [
            'type' => 'info',
            'message' => "Subscription has been deleted."
        ]);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
