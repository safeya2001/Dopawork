<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\User;
use App\Mail\OrderCreatedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    public function __construct(
        private EscrowService $escrowService,
        private NotificationService $notificationService
    ) {}

    /**
     * Place a new order.
     */
    public function placeOrder(User $client, Service $service, ServicePackage $package, string $requirements): Order
    {
        return DB::transaction(function () use ($client, $service, $package, $requirements) {
            $platformFeeRate = (float) config('platform.fee_percentage', 15) / 100;
            $subtotal = $package->price;
            $platformFee = round($subtotal * $platformFeeRate, 3);
            $total = round($subtotal + $platformFee, 3);
            $freelancerEarnings = round($subtotal - $platformFee, 3);

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'client_id' => $client->id,
                'freelancer_id' => $service->user_id,
                'service_id' => $service->id,
                'service_package_id' => $package->id,
                'title' => $service->title,
                'requirements' => $requirements,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'platform_fee' => $platformFee,
                'total_amount' => $total,
                'freelancer_earnings' => $freelancerEarnings,
                'delivery_days' => $package->delivery_days,
                'deadline' => now()->addDays($package->delivery_days),
                'revisions_allowed' => $package->revisions,
            ]);

            // Hold funds in escrow
            $this->escrowService->holdFunds($order, 'wallet');

            // Notify freelancer
            $this->notificationService->send(
                $service->user,
                'order.placed',
                'New Order Received',
                'طلب جديد تم استلامه',
                "You have a new order #{$order->order_number}",
                "لديك طلب جديد رقم #{$order->order_number}",
                ['order_id' => $order->id],
                "/freelancer/orders/{$order->id}"
            );

            // Email: client confirmation + freelancer notification
            try {
                Mail::to($client->email)->queue(new OrderCreatedMail($order->load(['client','freelancer','service']), 'client'));
                Mail::to($service->user->email)->queue(new OrderCreatedMail($order, 'freelancer'));
            } catch (\Throwable) {}

            return $order;
        });
    }

    /**
     * Mark an order as in progress (freelancer accepts).
     */
    public function startOrder(Order $order): Order
    {
        $order->update(['status' => 'in_progress']);

        $this->notificationService->send(
            $order->client,
            'order.started',
            'Work Has Begun',
            'بدأ العمل على طلبك',
            "Freelancer has started working on order #{$order->order_number}",
            "بدأ المستقل العمل على طلبك رقم #{$order->order_number}",
            ['order_id' => $order->id],
            "/client/orders/{$order->id}"
        );

        return $order->fresh();
    }

    /**
     * Deliver an order.
     */
    public function deliverOrder(Order $order, string $note, array $attachments = []): Order
    {
        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        $this->notificationService->send(
            $order->client,
            'order.delivered',
            'Order Delivered',
            'تم تسليم طلبك',
            "Your order #{$order->order_number} has been delivered. Please review it.",
            "تم تسليم طلبك رقم #{$order->order_number}. يرجى مراجعته.",
            ['order_id' => $order->id],
            "/client/orders/{$order->id}"
        );

        return $order->fresh();
    }

    /**
     * Client accepts delivery and completes the order.
     */
    public function completeOrder(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Release escrow to freelancer
            $this->escrowService->releaseFunds($order);

            // Update stats
            $order->service->increment('orders_count');
            $order->freelancer->freelancerProfile?->increment('completed_orders');
            $order->freelancer->freelancerProfile?->increment('total_orders');

            $this->notificationService->send(
                $order->freelancer,
                'order.completed',
                'Order Completed & Payment Released',
                'اكتمل الطلب وتم صرف المبلغ',
                "Order #{$order->order_number} completed. Payment of {$order->freelancer_earnings} JOD released to your wallet.",
                "اكتمل الطلب رقم #{$order->order_number}. تم صرف {$order->freelancer_earnings} دينار أردني إلى محفظتك.",
                ['order_id' => $order->id],
                "/freelancer/orders/{$order->id}"
            );

            return $order->fresh();
        });
    }

    /**
     * Cancel an order.
     */
    public function cancelOrder(Order $order, string $reason, string $cancelledBy): Order
    {
        return DB::transaction(function () use ($order, $reason, $cancelledBy) {
            $order->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_by' => $cancelledBy,
            ]);

            // Refund client if order was in escrow
            if ($order->escrow && $order->escrow->isHeld()) {
                $this->escrowService->refundFunds($order);
            }

            return $order->fresh();
        });
    }

    /**
     * Request a revision.
     */
    public function requestRevision(Order $order, string $note): Order
    {
        if ($order->revisions_used >= $order->revisions_allowed) {
            throw new \Exception('No revisions remaining for this order.');
        }

        $order->update([
            'status' => 'revision',
            'revisions_used' => $order->revisions_used + 1,
        ]);

        $this->notificationService->send(
            $order->freelancer,
            'order.revision',
            'Revision Requested',
            'طلب تعديل',
            "Client requested a revision on order #{$order->order_number}",
            "طلب العميل تعديلاً على الطلب رقم #{$order->order_number}",
            ['order_id' => $order->id],
            "/freelancer/orders/{$order->id}"
        );

        return $order->fresh();
    }
}
