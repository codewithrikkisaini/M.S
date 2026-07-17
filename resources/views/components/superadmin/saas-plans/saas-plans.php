<?php

use Livewire\Component;
use App\Models\SubscriptionPlan;

new class extends Component
{
    public $plans;
    public $showModal = false;
    public $isEdit = false;
    public $planId;

    // Form fields
    public $name;
    public $slug;
    public $price;
    public $billing_cycle = 'monthly';
    public $trial_days = 0;
    public $max_rooms;
    public $max_users;
    public $description;
    public $status = 'active';

    public function mount(): void
    {
        $this->loadPlans();
    }

    public function loadPlans(): void
    {
        $this->plans = SubscriptionPlan::latest()->get();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function openEditModal($id): void
    {
        $this->resetForm();
        $plan = SubscriptionPlan::findOrFail($id);
        $this->planId = $plan->id;
        $this->name = $plan->name;
        $this->slug = $plan->slug;
        $this->price = $plan->price;
        $this->billing_cycle = $plan->billing_cycle;
        $this->trial_days = $plan->trial_days;
        $this->max_rooms = $plan->max_rooms;
        $this->max_users = $plan->max_users;
        $this->description = $plan->description;
        $this->status = $plan->status;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function resetForm(): void
    {
        $this->planId = null;
        $this->name = '';
        $this->slug = '';
        $this->price = 0.00;
        $this->billing_cycle = 'monthly';
        $this->trial_days = 0;
        $this->max_rooms = null;
        $this->max_users = null;
        $this->description = '';
        $this->status = 'active';
    }

    public function savePlan(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug,' . ($this->planId ?? 'NULL'),
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|string|in:trial,monthly,yearly,lifetime',
            'trial_days' => 'required|integer|min:0',
            'max_rooms' => 'nullable|integer|min:1',
            'max_users' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'price' => $this->price,
            'billing_cycle' => $this->billing_cycle,
            'trial_days' => $this->trial_days,
            'max_rooms' => $this->max_rooms ?: null,
            'max_users' => $this->max_users ?: null,
            'description' => $this->description,
            'status' => $this->status,
        ];

        if ($this->isEdit) {
            $plan = SubscriptionPlan::findOrFail($this->planId);
            $plan->update($data);
            $message = "Subscription plan updated successfully!";
        } else {
            SubscriptionPlan::create($data);
            $message = "Subscription plan created successfully!";
        }

        $this->closeModal();
        $this->loadPlans();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    public function deletePlan($id): void
    {
        $plan = SubscriptionPlan::findOrFail($id);

        if ($plan->subscriptions()->count() > 0) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => "Cannot delete plan. It has active subscribers!"
            ]);
            return;
        }

        $plan->delete();
        $this->loadPlans();

        $this->dispatch('toast', [
            'type' => 'info',
            'message' => "Subscription plan has been deleted."
        ]);
    }

    public function render(): mixed
    {
        return $this->view();
    }
};
