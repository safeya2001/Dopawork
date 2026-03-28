<?php

namespace App\Http\Controllers;

use App\Mail\PaymentProcessedMail;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class WalletController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        $user = Auth::user();
        $balance = $user->wallet_balance;
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('wallet.index', compact('transactions', 'balance'));
    }

    public function showDeposit()
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return view('wallet.deposit');
    }

    public function processDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
            'method' => 'required|in:cliq,bank_transfer,ewallet',
            'proof'  => 'required_if:method,cliq|nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user   = Auth::user();
        $amount = round((float) $request->amount, 3);
        $method = $request->method;

        // CliQ → pending until admin approves
        if ($method === 'cliq') {
            $proofPath = $request->file('proof')->store('deposits/proofs', 'public');

            WalletTransaction::create([
                'reference'      => WalletTransaction::generateReference(),
                'user_id'        => $user->id,
                'type'           => 'deposit',
                'amount'         => $amount,
                'balance_before' => $user->wallet_balance,
                'balance_after'  => $user->wallet_balance, // unchanged until approved
                'description'    => "CliQ deposit request — pending review",
                'description_ar' => "طلب إيداع كليك — بانتظار المراجعة",
                'proof_path'     => $proofPath,
                'status'         => 'pending',
            ]);

            return redirect()->route('wallet.index')
                ->with('success', app()->getLocale() === 'ar'
                    ? "تم استلام طلب الإيداع بقيمة " . number_format($amount, 3) . " JOD. سيتم مراجعته وإضافة الرصيد خلال ساعات العمل."
                    : "Deposit request of " . number_format($amount, 3) . " JOD received. Funds will be added after admin review.");
        }

        // Other methods → instant (for demo / future integration)
        DB::transaction(function () use ($user, $amount, $method) {
            $before = $user->wallet_balance;
            $user->increment('wallet_balance', $amount);

            $tx = WalletTransaction::create([
                'reference'      => WalletTransaction::generateReference(),
                'user_id'        => $user->id,
                'type'           => 'deposit',
                'amount'         => $amount,
                'balance_before' => $before,
                'balance_after'  => $before + $amount,
                'description'    => "Wallet deposit via {$method}",
                'description_ar' => "إيداع في المحفظة عبر {$method}",
                'status'         => 'completed',
            ]);

            try { Mail::to($user->email)->queue(new PaymentProcessedMail($tx)); } catch (\Throwable) {}
        });

        return redirect()->route('wallet.index')
            ->with('success', app()->getLocale() === 'ar'
                ? "تم إيداع " . number_format($amount, 3) . " JOD بنجاح."
                : "Successfully deposited " . number_format($amount, 3) . " JOD.");
    }

    public function showWithdraw()
    {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::user()->isClient()) {
            return redirect()->route('wallet.deposit')
                ->with('info', app()->getLocale() === 'ar'
                    ? 'السحب متاح للمستقلين فقط. يمكنك إيداع رصيد لاستخدامه في طلباتك.'
                    : 'Withdrawals are only available for freelancers. You can deposit funds to use for orders.');
        }
        $user    = Auth::user();
        $balance = $user->wallet_balance;

        $pendingWithdrawals = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->sum('amount');
        $available = max(0, round($balance - $pendingWithdrawals, 3));

        return view('wallet.withdraw', compact('balance', 'available', 'pendingWithdrawals'));
    }

    public function requestWithdrawal(Request $request)
    {
        $user = Auth::user();

        if ($user->isClient()) {
            abort(403, 'Withdrawals are only available for freelancers.');
        }

        // Available = actual balance minus any pending withdrawal requests
        $pendingWithdrawals = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->sum('amount');
        $available = max(0, round($user->wallet_balance - $pendingWithdrawals, 3));

        $request->validate([
            'amount'     => ['required', 'numeric', 'min:5', 'max:' . $available],
            'method'     => 'required|in:cliq,bank_transfer',
            'cliq_alias' => 'required_if:method,cliq|nullable|string|max:50',
            'iban'       => 'required_if:method,bank_transfer|nullable|string|max:34',
        ]);

        if ($available < 5) {
            return back()->withErrors(['amount' => app()->getLocale() === 'ar'
                ? 'لا يوجد رصيد كافٍ للسحب (بعد احتساب طلبات السحب المعلقة)'
                : 'Insufficient available balance after pending withdrawals']);
        }

        $amount = round((float) $request->amount, 3);

        // Only lock the balance — do NOT deduct yet.
        // Admin will deduct when they confirm the transfer.
        WalletTransaction::create([
            'reference'      => WalletTransaction::generateReference(),
            'user_id'        => $user->id,
            'type'           => 'withdrawal',
            'amount'         => $amount,
            'balance_before' => $user->wallet_balance,
            'balance_after'  => $user->wallet_balance, // unchanged until admin approves
            'description'    => "Withdrawal request via {$request->method}",
            'description_ar' => "طلب سحب عبر {$request->method}",
            'status'         => 'pending',
            'meta'           => [
                'method'     => $request->method,
                'cliq_alias' => $request->cliq_alias,
                'iban'       => $request->iban,
            ],
        ]);

        return redirect()->route('wallet.index')
            ->with('success', app()->getLocale() === 'ar'
                ? "تم إرسال طلب السحب بقيمة " . number_format($amount, 3) . " JOD. سيتم المعالجة خلال 1-3 أيام عمل."
                : "Withdrawal request of " . number_format($amount, 3) . " JOD submitted. Processing in 1-3 business days.");
    }
}
