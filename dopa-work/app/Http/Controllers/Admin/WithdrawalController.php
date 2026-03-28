<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');

        $withdrawals = WalletTransaction::with('user')
            ->where('type', 'withdrawal')
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.withdrawals.index', compact('withdrawals', 'status'));
    }

    public function process(Request $request, WalletTransaction $withdrawal)
    {
        $request->validate([
            'action' => 'required|in:completed,failed',
            'notes'  => 'nullable|string|max:300',
        ]);

        abort_if($withdrawal->status !== 'pending', 422, 'Already processed.');

        DB::transaction(function () use ($request, $withdrawal) {
            if ($request->action === 'completed') {
                // Deduct balance only now that admin confirms transfer
                $user   = $withdrawal->user;
                $before = $user->wallet_balance;
                $user->decrement('wallet_balance', $withdrawal->amount);

                $withdrawal->update([
                    'status'         => 'completed',
                    'balance_before' => $before,
                    'balance_after'  => $before - $withdrawal->amount,
                    'notes'          => $request->notes,
                ]);
            } else {
                // Rejected — balance was never touched, just mark failed
                $withdrawal->update([
                    'status' => 'failed',
                    'notes'  => $request->notes,
                ]);
            }
        });

        $label = $request->action === 'completed'
            ? (app()->getLocale() === 'ar' ? 'تم تأكيد التحويل وخصم الرصيد' : 'Transfer confirmed and balance deducted')
            : (app()->getLocale() === 'ar' ? 'تم رفض طلب السحب' : 'Withdrawal request rejected');

        return back()->with('success', $label);
    }
}
