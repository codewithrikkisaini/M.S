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
<<<<<<< HEAD
        $sheets = [$service->build($date)];
        $hotelName = Setting::get('hotel_name', 'Lodgiko PMS Lite');
=======
        $sheet = $service->build($date);
        $sheets = [$sheet];
        $hotelName = $sheet['hotel_name'];
>>>>>>> 778cef2fa38fae58f56eb158ee6abf9a801ce96e

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
<<<<<<< HEAD
        $hotelName = Setting::get('hotel_name', 'Lodgiko PMS Lite');
=======
        $hotelName = Setting::get('hotel_name', 'Merahkie Hotel & Resort');
>>>>>>> 778cef2fa38fae58f56eb158ee6abf9a801ce96e

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
