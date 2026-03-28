<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\EscrowTransaction;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DisputeController extends Controller
{
    public function index(Request $request)
    {
        $disputes = Dispute::with(['order.client', 'order.freelancer', 'raisedBy'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.disputes.index', compact('disputes'));
    }

    public function show(Dispute $dispute)
    {
        $dispute->load(['order.client', 'order.freelancer', 'order.escrow', 'raisedBy', 'resolvedBy']);

        return view('admin.disputes.show', compact('dispute'));
    }

    public function resolve(Request $request, Dispute $dispute)
    {
        $request->validate([
            'resolution'       => 'required|in:refund_client,release_freelancer,partial_split,no_action',
            'resolution_notes' => 'required|string|max:1000',
            'client_refund_amount'     => 'nullable|numeric|min:0',
            'freelancer_release_amount' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $dispute) {
            $walletService = new WalletService();
            $order = $dispute->order;
            $escrow = $order->escrow;

            if ($escrow && $escrow->isHeld()) {
                if ($request->resolution === 'refund_client') {
                    $walletService->credit($order->client, $escrow->amount, "Dispute refund - {$order->order_number}", "استرداد نزاع - {$order->order_number}", $escrow);
                    $escrow->update(['status' => 'refunded', 'refunded_at' => now()]);
                } elseif ($request->resolution === 'release_freelancer') {
                    $walletService->credit($order->freelancer, $escrow->freelancer_amount, "Dispute resolved - {$order->order_number}", "تم حل النزاع - {$order->order_number}", $escrow);
                    $escrow->update(['status' => 'released', 'released_at' => now()]);
                } elseif ($request->resolution === 'partial_split') {
                    $clientAmount = (float) $request->client_refund_amount;
                    $freelancerAmount = (float) $request->freelancer_release_amount;
                    if ($clientAmount > 0) {
                        $walletService->credit($order->client, $clientAmount, "Partial refund - {$order->order_number}", "استرداد جزئي - {$order->order_number}", $escrow);
                    }
                    if ($freelancerAmount > 0) {
                        $walletService->credit($order->freelancer, $freelancerAmount, "Partial release - {$order->order_number}", "صرف جزئي - {$order->order_number}", $escrow);
                    }
                    $escrow->update(['status' => 'released', 'released_at' => now()]);
                }
            }

            $dispute->update([
                'status'                    => 'resolved',
                'resolution'                => $request->resolution,
                'resolution_notes'          => $request->resolution_notes,
                'client_refund_amount'      => $request->client_refund_amount,
                'freelancer_release_amount' => $request->freelancer_release_amount,
                'resolved_by'               => auth()->id(),
                'resolved_at'               => now(),
            ]);

            $order->update(['status' => 'completed']);
        });

        return redirect()->route('admin.disputes.index')
            ->with('success', 'Dispute resolved successfully.');
    }
}
