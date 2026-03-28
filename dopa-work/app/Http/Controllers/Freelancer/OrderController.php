<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index()
    {
        $orders = Order::forFreelancer(Auth::id())
            ->with(['client', 'service', 'package'])
            ->latest()
            ->paginate(10);

        return view('freelancer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorizeFreelancer($order);

        $order->load(['client', 'service', 'package', 'milestones', 'escrow', 'review', 'dispute']);

        return view('freelancer.orders.show', compact('order'));
    }

    public function start(Order $order)
    {
        $this->authorizeFreelancer($order);

        if ($order->status !== 'pending') {
            return back()->withErrors(['error' => __('Order must be pending to start.')]);
        }

        $this->orderService->startOrder($order);

        return back()->with('success', __('Order started! You can now begin working.'));
    }

    public function deliver(Request $request, Order $order)
    {
        $this->authorizeFreelancer($order);

        $request->validate([
            'note' => 'required|string|min:20|max:3000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:20480',
        ]);

        if (!in_array($order->status, ['in_progress', 'revision'])) {
            return back()->withErrors(['error' => __('Order cannot be delivered at this stage.')]);
        }

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachments[] = $file->store("orders/{$order->id}/deliveries", 'private');
            }
        }

        $this->orderService->deliverOrder($order, $request->note, $attachments);

        return back()->with('success', __('Delivery submitted! The client will review it.'));
    }

    private function authorizeFreelancer(Order $order): void
    {
        if ($order->freelancer_id !== Auth::id()) {
            abort(403);
        }
    }
}
