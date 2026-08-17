<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    /**
     * Display admin settings page.
     */
    public function index()
    {
        $webcam_enabled = (bool) Setting::get('webcam_enabled', true);
        
        return view('admin.settings.index', [
            'webcam_enabled' => $webcam_enabled,
        ]);
    }

    /**
     * Update admin settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'webcam_enabled' => 'required|boolean',
        ]);

        Setting::set('webcam_enabled', (bool) $request->input('webcam_enabled'));

        return back()->with('toast', [
            'message' => 'Settings updated successfully!',
            'type' => 'success'
        ]);
    }
}
