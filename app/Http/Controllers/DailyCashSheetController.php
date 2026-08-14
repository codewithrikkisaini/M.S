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
        $sheet = $service->build($date);
        $sheets = [$sheet];
        $userHotel = auth()->user()?->hotel;
        $hotelName = $sheet['hotel_name'] ?? ($userHotel?->name ?? Setting::get('hotel_name', 'Lodgiko PMS'));

        $pdf = Pdf::loadView('reports.daily-cash-sheet-pdf', compact('sheets', 'sheet', 'hotelName'))
            ->setPaper('a4', 'portrait');

        if ($request->query('view') === '1') {
            return $pdf->stream('daily-cash-sheet-' . $date . '.pdf');
        }

        return $pdf->download('daily-cash-sheet-' . $date . '.pdf');
    }

    public function downloadRange(Request $request, DailyCashSheetService $service)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date',
        ]);

        $sheets = $service->buildRange($request->query('from'), $request->query('to'));
        $userHotel = auth()->user()?->hotel;
        $hotelName = $sheets[0]['hotel_name'] ?? ($userHotel?->name ?? Setting::get('hotel_name', 'Lodgiko PMS'));

        $pdf = Pdf::loadView('reports.daily-cash-sheet-pdf', compact('sheets', 'hotelName'))
            ->setPaper('a4', 'portrait');

        if ($request->query('view') === '1') {
            return $pdf->stream('daily-cash-sheet-' . $request->query('from') . '-to-' . $request->query('to') . '.pdf');
        }

        return $pdf->download('daily-cash-sheet-' . $request->query('from') . '-to-' . $request->query('to') . '.pdf');
    }

    public function downloadCustomerPdf(Request $request, $reservationId, DailyCashSheetService $service)
    {
        $customer = $service->getCustomerData($reservationId);

        if (!$customer) {
            abort(404, 'Customer reservation record not found.');
        }

        $pdf = Pdf::loadView('reports.customer-cash-sheet-pdf', compact('customer'))
            ->setPaper('a4', 'portrait');

        if ($request->query('view') === '1') {
            return $pdf->stream('customer-receipt-' . $customer['pnr'] . '.pdf');
        }

        return $pdf->download('customer-receipt-' . $customer['pnr'] . '.pdf');
    }
}
