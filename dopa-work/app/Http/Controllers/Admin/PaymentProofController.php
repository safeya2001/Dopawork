<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PdfService;
use Illuminate\Http\Request;

class PaymentProofController extends Controller
{
    public function __construct(private PdfService $pdfService) {}

    /**
     * Download payment proof PDF for a single order.
     */
    public function download(Request $request, Order $order)
    {
        $locale = $request->get('locale', 'ar');
        $order->load(['client', 'freelancer', 'service', 'package', 'payment']);

        $pdf = $this->pdfService->generatePaymentProof($order, $locale);

        $filename = 'payment-proof-' . $order->order_number . '-' . $locale . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Stream payment proof PDF inline (preview).
     */
    public function preview(Request $request, Order $order)
    {
        $locale = $request->get('locale', 'ar');
        $order->load(['client', 'freelancer', 'service', 'package', 'payment']);

        $pdf = $this->pdfService->generatePaymentProof($order, $locale);

        $filename = 'payment-proof-' . $order->order_number . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Bulk payment report for a date range.
     */
    public function bulkReport(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        $orders = Order::with(['client', 'freelancer', 'service'])
            ->where('status', 'completed')
            ->whereBetween('created_at', [
                $request->from . ' 00:00:00',
                $request->to   . ' 23:59:59',
            ])
            ->get();

        $locale = $request->get('locale', 'ar');
        $isAr   = $locale === 'ar';

        $data = [
            'orders'      => $orders,
            'from'        => $request->from,
            'to'          => $request->to,
            'total'       => $orders->sum('total_amount'),
            'commission'  => $orders->sum('platform_fee'),
            'locale'      => $locale,
            'isAr'        => $isAr,
            'generatedAt' => now(),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.bulk-report', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'bulk-report-' . $request->from . '-to-' . $request->to . '.pdf';

        return $pdf->download($filename);
    }
}
