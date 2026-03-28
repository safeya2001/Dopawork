<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $deposits = WalletTransaction::where('type', 'deposit')
            ->where('status', $status)
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.deposits.index', compact('deposits', 'status'));
    }

    public function approve(WalletTransaction $transaction)
    {
        if ($transaction->status !== 'pending' || $transaction->type !== 'deposit') {
            return back()->with('error', 'هذا الطلب غير قابل للمراجعة.');
        }

        DB::transaction(function () use ($transaction) {
            $user   = $transaction->user;
            $before = $user->wallet_balance;
            $user->increment('wallet_balance', $transaction->amount);

            $transaction->update([
                'status'         => 'completed',
                'balance_before' => $before,
                'balance_after'  => $before + $transaction->amount,
                'reviewed_by'    => auth()->id(),
                'reviewed_at'    => now(),
                'admin_note'     => 'تم القبول والإضافة للمحفظة.',
            ]);
        });

        return back()->with('success',
            "تم قبول إيداع " . number_format($transaction->amount, 3) . " JOD وإضافته لمحفظة " . $transaction->user->name . "."
        );
    }

    public function reject(Request $request, WalletTransaction $transaction)
    {
        if ($transaction->status !== 'pending' || $transaction->type !== 'deposit') {
            return back()->with('error', 'هذا الطلب غير قابل للمراجعة.');
        }

        $request->validate(['admin_note' => 'required|string|max:500']);

        $transaction->update([
            'status'      => 'cancelled',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'admin_note'  => $request->admin_note,
        ]);

        return back()->with('success',
            "تم رفض طلب الإيداع بقيمة " . number_format($transaction->amount, 3) . " JOD."
        );
    }
}
