<?php

namespace App\Services;

use App\Models\EscrowTransaction;
use App\Models\Order;
use App\Models\Milestone;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EscrowService
{
    public function __construct(private WalletService $walletService) {}

    /**
     * Hold funds in escrow when an order is placed.
     */
    public function holdFunds(Order $order, string $paymentMethod = 'wallet'): EscrowTransaction
    {
        return DB::transaction(function () use ($order, $paymentMethod) {
            // Deduct from client wallet
            $this->walletService->debit(
                $order->client,
                $order->total_amount,
                "Order payment - {$order->order_number}",
                "دفع طلب - {$order->order_number}",
                $order
            );

            $escrow = EscrowTransaction::create([
                'reference' => EscrowTransaction::generateReference(),
                'order_id' => $order->id,
                'client_id' => $order->client_id,
                'freelancer_id' => $order->freelancer_id,
                'amount' => $order->total_amount,
                'platform_fee' => $order->platform_fee,
                'freelancer_amount' => $order->freelancer_earnings,
                'status' => 'held',
                'payment_method' => $paymentMethod,
                'held_at' => now(),
                'auto_release_at' => now()->addDays((int) config('platform.escrow_release_days', 7)),
            ]);

            return $escrow;
        });
    }

    /**
     * Release escrow funds to freelancer upon order completion.
     */
    public function releaseFunds(Order $order, ?User $releasedBy = null): EscrowTransaction
    {
        return DB::transaction(function () use ($order, $releasedBy) {
            $escrow = $order->escrow;

            if (!$escrow || !$escrow->isHeld()) {
                throw new \Exception('No held escrow found for this order.');
            }

            // Credit freelancer wallet
            $this->walletService->credit(
                $order->freelancer,
                $escrow->freelancer_amount,
                "Payment received - {$order->order_number}",
                "استلام دفعة - {$order->order_number}",
                $escrow
            );

            // Credit platform fee to admin wallet
            if ($escrow->platform_fee > 0) {
                $admin = \App\Models\User::where('role', 'super_admin')->first();
                if ($admin) {
                    $this->walletService->credit(
                        $admin,
                        $escrow->platform_fee,
                        "Platform fee - {$order->order_number}",
                        "رسوم المنصة - {$order->order_number}",
                        $escrow
                    );
                }
            }

            $escrow->update([
                'status' => 'released',
                'released_at' => now(),
                'released_by' => $releasedBy?->id,
            ]);

            return $escrow;
        });
    }

    /**
     * Refund escrow funds to client (on cancellation/dispute resolution).
     */
    public function refundFunds(Order $order, float $refundAmount = null): EscrowTransaction
    {
        return DB::transaction(function () use ($order, $refundAmount) {
            $escrow = $order->escrow;

            if (!$escrow || !$escrow->isHeld()) {
                throw new \Exception('No held escrow found for this order.');
            }

            $amount = $refundAmount ?? $escrow->amount;

            $this->walletService->credit(
                $order->client,
                $amount,
                "Refund - {$order->order_number}",
                "استرداد - {$order->order_number}",
                $escrow
            );

            $escrow->update([
                'status' => 'refunded',
                'refunded_at' => now(),
            ]);

            return $escrow;
        });
    }

    /**
     * Process auto-release of funds for overdue escrows.
     */
    public function processAutoReleases(): int
    {
        $released = 0;

        EscrowTransaction::where('status', 'held')
            ->where('auto_release_at', '<=', now())
            ->with('order')
            ->chunk(50, function ($escrows) use (&$released) {
                foreach ($escrows as $escrow) {
                    try {
                        $this->releaseFunds($escrow->order);
                        $released++;
                    } catch (\Exception $e) {
                        \Log::error("Auto-release failed for escrow {$escrow->id}: " . $e->getMessage());
                    }
                }
            });

        return $released;
    }
}
