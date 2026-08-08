<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public string $title;
    public string $hotelName;

    public function __construct(string $title = 'Dashboard', ?string $hotelName = null)
    {
        $this->title = $title;
        $this->hotelName = $hotelName ?? (auth()->user()?->hotel?->name ?? 'Lodgiko Admin');
    }

    public function render(): View
    {
        return view('layouts.app');
    }
}
