<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download($id)
    {
        $invoice      = Invoice::with(['checkout.reservation.guest', 'checkout.reservation.rooms.roomType', 'checkout.reservation.payments'])->findOrFail($id);
        $hotelName    = Setting::get('hotel_name',    'Merahkie PMS Lite');
        $invoiceFooter = Setting::get('invoice_footer', 'Thank you for staying with us!');

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'hotelName', 'invoiceFooter'));
        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    public function view($id)
    {
        $invoice      = Invoice::with(['checkout.reservation.guest', 'checkout.reservation.rooms.roomType', 'checkout.reservation.payments'])->findOrFail($id);
        $hotelName    = Setting::get('hotel_name',    'Merahkie PMS Lite');
        $invoiceFooter = Setting::get('invoice_footer', 'Thank you for staying with us!');

        return view('invoices.pdf', compact('invoice', 'hotelName', 'invoiceFooter'));
    }
}
