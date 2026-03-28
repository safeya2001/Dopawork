<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['freelancerProfile', 'identityVerification', 'walletTransactions' => fn($q) => $q->latest()->limit(10)]);

        $orderStats = [
            'as_client'     => $user->ordersAsClient()->count(),
            'as_freelancer' => $user->ordersAsFreelancer()->count(),
        ];

        $recentOrders = $user->isFreelancer()
            ? $user->ordersAsFreelancer()->with('client', 'service')->latest()->limit(5)->get()
            : $user->ordersAsClient()->with('freelancer', 'service')->latest()->limit(5)->get();

        return view('admin.users.show', compact('user', 'orderStats', 'recentOrders'));
    }

    public function suspend(User $user)
    {
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return back()->withErrors(['error' => 'Cannot suspend admin accounts.']);
        }

        $newStatus = $user->status === 'suspended' ? 'active' : 'suspended';
        $user->update(['status' => $newStatus]);

        return back()->with('success', $newStatus === 'suspended'
            ? "تم تعليق حساب {$user->name}."
            : "تم تفعيل حساب {$user->name} من جديد.");
    }

    public function ban(User $user)
    {
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return back()->withErrors(['error' => 'Cannot ban admin accounts.']);
        }

        $user->update(['status' => 'suspended']);
        $user->tokens()->delete();

        \App\Models\PlatformNotification::create([
            'user_id'  => $user->id,
            'type'     => 'account_banned',
            'title'    => 'Account Banned',
            'title_ar' => 'تم حظر حسابك',
            'body'     => 'Your account has been permanently banned due to a violation of our terms.',
            'body_ar'  => 'تم حظر حسابك بشكل دائم بسبب انتهاك شروط الاستخدام.',
        ]);

        return back()->with('success', "تم حظر حساب {$user->name} بشكل دائم.");
    }

    public function destroy(User $user)
    {
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return back()->withErrors(['error' => 'Cannot delete admin accounts.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "تم حذف حساب {$user->name}.");
    }

    // ─── Clients ───────────────────────────────────────────────────────────

    public function clients(Request $request)
    {
        $clients = User::where('role', 'client')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->withCount(['ordersAsClient as orders_count'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    // ─── Freelancers ────────────────────────────────────────────────────────

    public function freelancers(Request $request)
    {
        $freelancers = User::where('role', 'freelancer')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->withCount(['ordersAsFreelancer as orders_count'])
            ->with('freelancerProfile')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.freelancers.index', compact('freelancers'));
    }

    public function createFreelancer()
    {
        return view('admin.freelancers.create');
    }

    public function storeFreelancer(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'name_ar'  => 'nullable|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20|unique:users,phone',
            'password' => ['required', Password::min(8)],
            'city'     => 'nullable|string|max:100',
            'bio'      => 'nullable|string|max:1000',
            'bio_ar'   => 'nullable|string|max:1000',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'name_ar'  => $data['name_ar'] ?? null,
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role'     => 'freelancer',
            'status'   => 'active',
            'city'     => $data['city'] ?? null,
            'bio'      => $data['bio'] ?? null,
            'bio_ar'   => $data['bio_ar'] ?? null,
            'country'  => 'JO',
            'locale'   => 'ar',
        ]);

        return redirect()->route('admin.freelancers.index')
            ->with('success', "تم إنشاء حساب الفريلانسر {$user->name} بنجاح.");
    }
}
