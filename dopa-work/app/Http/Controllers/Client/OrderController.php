<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Services\OrderService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private PdfService $pdfService
    ) {}

    public function index()
    {
        $orders = Order::forClient(Auth::id())
            ->with(['freelancer', 'service', 'package'])
            ->latest()
            ->paginate(10);

        return view('client.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorizeClient($order);

        $order->load(['freelancer.freelancerProfile', 'service', 'package', 'milestones', 'escrow', 'review', 'dispute']);

        return view('client.orders.show', compact('order'));
    }

    public function checkout(Service $service, Request $request)
    {
        $service->load(['user.freelancerProfile', 'packages', 'category']);

        $packageId = $request->input('package');
        $selectedPackage = $packageId
            ? $service->packages->firstWhere('id', $packageId)
            : $service->packages->first();

        return view('client.checkout', compact('service', 'selectedPackage'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'package_id' => 'required|exists:service_packages,id',
            'requirements' => 'required|string|min:20|max:5000',
        ]);

        $client = Auth::user();

        if (!$client->isVerified()) {
            return back()->withErrors(['error' => __('You must verify your identity before placing an order.')]);
        }

        $service = Service::active()->findOrFail($request->service_id);
        $package = ServicePackage::findOrFail($request->package_id);

        if ($package->service_id !== $service->id) {
            abort(422, 'Invalid package for this service.');
        }

        if ($client->wallet_balance < ($package->price * 1.15)) {
            return back()->withErrors(['error' => __('Insufficient wallet balance. Please top up your wallet.')]);
        }

        $order = $this->orderService->placeOrder($client, $service, $package, $request->requirements);

        return redirect()->route('client.orders.show', $order)
            ->with('success', __('Order placed successfully! The freelancer will be notified.'));
    }

    public function complete(Order $order)
    {
        $this->authorizeClient($order);

        if ($order->status !== 'delivered') {
            return back()->withErrors(['error' => __('Order must be delivered before it can be completed.')]);
        }

        $this->orderService->completeOrder($order);

        return redirect()->route('client.orders.show', $order)
            ->with('success', __('Order completed and payment released to the freelancer!'));
    }

    public function requestRevision(Request $request, Order $order)
    {
        $this->authorizeClient($order);

        $request->validate(['note' => 'required|string|max:2000']);

        $this->orderService->requestRevision($order, $request->note);

        return back()->with('success', __('Revision requested successfully.'));
    }

    public function cancel(Request $request, Order $order)
    {
        $this->authorizeClient($order);

        $request->validate(['reason' => 'required|string|max:1000']);

        if (!in_array($order->status, ['pending'])) {
            return back()->withErrors(['error' => __('This order cannot be cancelled at this stage.')]);
        }

        $this->orderService->cancelOrder($order, $request->reason, 'client');

        return redirect()->route('client.orders.index')
            ->with('success', __('Order cancelled and refund processed to your wallet.'));
    }

    public function submitReview(Request $request, Order $order)
    {
        $this->authorizeClient($order);

        if ($order->status !== 'completed') {
            return back()->withErrors(['error' => __('Reviews can only be submitted for completed orders.')]);
        }

        if ($order->review) {
            return back()->withErrors(['error' => __('You have already reviewed this order.')]);
        }

        $request->validate([
            'rating'               => 'required|integer|min:1|max:5',
            'communication_rating' => 'nullable|integer|min:1|max:5',
            'quality_rating'       => 'nullable|integer|min:1|max:5',
            'delivery_rating'      => 'nullable|integer|min:1|max:5',
            'comment'              => 'nullable|string|max:2000',
        ]);

        Review::create([
            'order_id'             => $order->id,
            'reviewer_id'          => Auth::id(),
            'reviewee_id'          => $order->freelancer_id,
            'service_id'           => $order->service_id,
            'rating'               => $request->rating,
            'communication_rating' => $request->communication_rating,
            'quality_rating'       => $request->quality_rating,
            'delivery_rating'      => $request->delivery_rating,
            'comment'              => $request->comment,
            'is_public'            => true,
        ]);

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'شكراً على تقييمك! سيساعد هذا المستقل والمجتمع.'
            : 'Thank you for your review! This helps the freelancer and community.');
    }

    public function downloadReceipt(Order $order, Request $request)
    {
        $this->authorizeClient($order);

        if (!in_array($order->status, ['completed'])) {
            abort(403, 'Receipt only available for completed orders.');
        }

        $locale = $request->input('lang', Auth::user()->locale ?? 'en');
        $pdf = $this->pdfService->generatePaymentProof($order, $locale);

        return $pdf->download("receipt-{$order->order_number}.pdf");
    }

    private function authorizeClient(Order $order): void
    {
        if ($order->client_id !== Auth::id()) {
            abort(403);
        }
    }
}
