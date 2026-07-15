<?php

namespace App\Http\Controllers;

use App\Services\DailyCashSheetService;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DailyCashSheetController extends Controller
{
    public function download(Request $request, DailyCashSheetService $service)
    {
        $date = $request->query('date', now()->toDateString());
        $sheets = [$service->build($date)];
        $hotelName = Setting::get('hotel_name', 'Merahkie PMS Lite');

        $pdf = Pdf::loadView('reports.daily-cash-sheet-pdf', compact('sheets', 'hotelName'));

        return $pdf->download('daily-cash-sheet-' . $date . '.pdf');
    }

    public function downloadRange(Request $request, DailyCashSheetService $service)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date',
        ]);

        $sheets = $service->buildRange($request->query('from'), $request->query('to'));
        $hotelName = Setting::get('hotel_name', 'Merahkie PMS Lite');

        $pdf = Pdf::loadView('reports.daily-cash-sheet-pdf', compact('sheets', 'hotelName'));

        return $pdf->download('daily-cash-sheet-' . $request->query('from') . '-to-' . $request->query('to') . '.pdf');
    }
}
