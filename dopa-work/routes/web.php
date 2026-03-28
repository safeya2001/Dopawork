<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\PaymentProofController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Client\OrderController as ClientOrderController;
use App\Http\Controllers\Freelancer\OrderController as FreelancerOrderController;
use App\Http\Controllers\Freelancer\FreelancerProfileController;
use App\Http\Controllers\Freelancer\ServiceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lang/{locale}', [HomeController::class, 'setLocale'])->name('set.locale');

// Services / Marketplace
Route::get('/services', [MarketplaceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [MarketplaceController::class, 'show'])->name('services.show');
Route::get('/freelancers', [MarketplaceController::class, 'freelancers'])->name('freelancers.index');
Route::get('/freelancers/{user}', [MarketplaceController::class, 'freelancerProfile'])->name('freelancers.show');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Notifications
    Route::get('/notifications',                        [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/fetch',                  [\App\Http\Controllers\NotificationController::class, 'fetch'])->name('notifications.fetch');
    Route::post('/notifications/mark-all-read',         [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications/{notification}',          [\App\Http\Controllers\NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/{notification}/read',   [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.markRead');

    // Identity Verification
    Route::get('/verify-identity', [VerificationController::class, 'showUploadForm'])->name('verification.upload');
    Route::post('/verify-identity', [VerificationController::class, 'upload'])->name('verification.submit');
    Route::get('/verify-identity/pending', [VerificationController::class, 'pending'])->name('verification.pending');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Wallet
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/deposit', [WalletController::class, 'showDeposit'])->name('deposit');
        Route::post('/deposit', [WalletController::class, 'processDeposit'])->name('deposit.process');
        Route::get('/withdraw', [WalletController::class, 'showWithdraw'])->name('withdraw');
        Route::post('/withdraw', [WalletController::class, 'requestWithdrawal'])->name('withdraw.request');
    });

    // Messages
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/{conversation}', [MessageController::class, 'show'])->name('show');
        Route::post('/{conversation}', [MessageController::class, 'send'])->name('send');
        Route::post('/start/{service}', [MessageController::class, 'startConversation'])->name('start');
    });
});

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:client,admin,super_admin'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [ClientOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [ClientOrderController::class, 'show'])->name('show');
            Route::post('/{order}/complete', [ClientOrderController::class, 'complete'])->name('complete');
            Route::post('/{order}/revision', [ClientOrderController::class, 'requestRevision'])->name('revision');
            Route::post('/{order}/cancel', [ClientOrderController::class, 'cancel'])->name('cancel');
            Route::post('/{order}/review', [ClientOrderController::class, 'submitReview'])->name('review');
            Route::get('/{order}/receipt', [ClientOrderController::class, 'downloadReceipt'])->name('receipt');
        });

        // Checkout
        Route::get('/checkout/{service}', [ClientOrderController::class, 'checkout'])->name('checkout');
        Route::post('/orders', [ClientOrderController::class, 'placeOrder'])->name('orders.place');

        // Projects
        Route::prefix('projects')->name('projects.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Client\ProjectController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Client\ProjectController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Client\ProjectController::class, 'store'])->name('store');
            Route::get('/{project}', [\App\Http\Controllers\Client\ProjectController::class, 'show'])->name('show');
            Route::delete('/{project}', [\App\Http\Controllers\Client\ProjectController::class, 'cancel'])->name('cancel');
            Route::post('/{project}/proposals/{proposal}/accept', [\App\Http\Controllers\Client\ProjectController::class, 'acceptProposal'])->name('proposals.accept');
            Route::post('/{project}/proposals/{proposal}/reject', [\App\Http\Controllers\Client\ProjectController::class, 'rejectProposal'])->name('proposals.reject');
            Route::post('/{project}/milestones/{milestone}/approve', [\App\Http\Controllers\Client\ProjectController::class, 'approveMilestone'])->name('milestones.approve');
            Route::post('/{project}/milestones/{milestone}/revision', [\App\Http\Controllers\Client\ProjectController::class, 'requestRevision'])->name('milestones.revision');
        });
    });

/*
|--------------------------------------------------------------------------
| Freelancer Routes
|--------------------------------------------------------------------------
*/

// Profile routes — accessible even while identity verification is pending
Route::middleware(['auth', 'role:freelancer,admin,super_admin'])
    ->prefix('freelancer')
    ->name('freelancer.')
    ->group(function () {
        Route::get('/profile', [FreelancerProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [FreelancerProfileController::class, 'update'])->name('profile.update');
        Route::post('/portfolio', [FreelancerProfileController::class, 'addPortfolioItem'])->name('portfolio.add');
        Route::delete('/portfolio/{portfolioItem}', [FreelancerProfileController::class, 'deletePortfolioItem'])->name('portfolio.delete');
    });

Route::middleware(['auth', 'role:freelancer,admin,super_admin', 'identity.verified'])
    ->prefix('freelancer')
    ->name('freelancer.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Freelancer\DashboardController::class, 'index'])->name('dashboard');

        // Active contracts
        Route::get('/contracts', [\App\Http\Controllers\Freelancer\ProposalController::class, 'contracts'])->name('contracts.index');

        // Services / Gigs
        Route::resource('services', ServiceController::class);
        Route::post('/services/{service}/toggle', [ServiceController::class, 'toggleStatus'])->name('services.toggle');

        // Projects (browse + proposals)
        Route::get('/projects', [\App\Http\Controllers\Freelancer\ProposalController::class, 'browseProjects'])->name('projects.browse');
        Route::get('/projects/{project}', [\App\Http\Controllers\Freelancer\ProposalController::class, 'showProject'])->name('projects.show');
        Route::post('/projects/{project}/propose', [\App\Http\Controllers\Freelancer\ProposalController::class, 'submitProposal'])->name('proposals.submit');
        Route::post('/proposals/{proposal}/withdraw', [\App\Http\Controllers\Freelancer\ProposalController::class, 'withdrawProposal'])->name('proposals.withdraw');
        Route::get('/my-proposals', [\App\Http\Controllers\Freelancer\ProposalController::class, 'myProposals'])->name('proposals.index');
        Route::post('/milestones/{milestone}/deliver', [\App\Http\Controllers\Freelancer\ProposalController::class, 'deliverMilestone'])->name('milestones.deliver');

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [FreelancerOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [FreelancerOrderController::class, 'show'])->name('show');
            Route::post('/{order}/start', [FreelancerOrderController::class, 'start'])->name('start');
            Route::post('/{order}/deliver', [FreelancerOrderController::class, 'deliver'])->name('deliver');
        });
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin,super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Identity Verifications
        Route::prefix('verifications')->name('verifications.')->group(function () {
            Route::get('/', [AdminDashboardController::class, 'verifications'])->name('index');
            Route::get('/{verification}', [AdminDashboardController::class, 'showVerification'])->name('show');
            Route::get('/{verification}/document/{field}', [AdminDashboardController::class, 'serveDocument'])->name('document');
            Route::post('/{verification}/approve', [AdminDashboardController::class, 'approveVerification'])->name('approve');
            Route::post('/{verification}/reject', [AdminDashboardController::class, 'rejectVerification'])->name('reject');
        });

        // Services moderation
        Route::get('/services', [AdminDashboardController::class, 'services'])->name('services.index');
        Route::post('/services/{service}/approve', [AdminDashboardController::class, 'approveService'])->name('services.approve');
        Route::post('/services/{service}/reject', [AdminDashboardController::class, 'rejectService'])->name('services.reject');

        // Escrow management
        Route::get('/escrow', [AdminDashboardController::class, 'escrow'])->name('escrow.index');

        // Users (all roles)
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
        Route::post('/users/{user}/suspend', [\App\Http\Controllers\Admin\UserController::class, 'suspend'])->name('users.suspend');

        // Clients
        Route::get('/clients', [\App\Http\Controllers\Admin\UserController::class, 'clients'])->name('clients.index');

        // Freelancers
        Route::get('/freelancers', [\App\Http\Controllers\Admin\UserController::class, 'freelancers'])->name('freelancers.index');
        Route::get('/freelancers/create', [\App\Http\Controllers\Admin\UserController::class, 'createFreelancer'])->name('freelancers.create');
        Route::post('/freelancers', [\App\Http\Controllers\Admin\UserController::class, 'storeFreelancer'])->name('freelancers.store');

        // Orders
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');

        // Disputes
        Route::get('/disputes', [\App\Http\Controllers\Admin\DisputeController::class, 'index'])->name('disputes.index');
        Route::get('/disputes/{dispute}', [\App\Http\Controllers\Admin\DisputeController::class, 'show'])->name('disputes.show');
        Route::post('/disputes/{dispute}/resolve', [\App\Http\Controllers\Admin\DisputeController::class, 'resolve'])->name('disputes.resolve');

        // Categories
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

        // Withdrawals
        Route::get('/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals/{withdrawal}/process', [\App\Http\Controllers\Admin\WithdrawalController::class, 'process'])->name('withdrawals.process');

        // Deposits (CliQ proof review)
        Route::get('/deposits', [\App\Http\Controllers\Admin\DepositController::class, 'index'])->name('deposits.index');
        Route::post('/deposits/{transaction}/approve', [\App\Http\Controllers\Admin\DepositController::class, 'approve'])->name('deposits.approve');
        Route::post('/deposits/{transaction}/reject', [\App\Http\Controllers\Admin\DepositController::class, 'reject'])->name('deposits.reject');

        // Payment Proof PDF
        Route::get('/orders/{order}/payment-proof',         [PaymentProofController::class, 'download'])->name('payment-proof.download');
        Route::get('/orders/{order}/payment-proof/preview', [PaymentProofController::class, 'preview'])->name('payment-proof.preview');
        Route::get('/reports/bulk-pdf',                     [PaymentProofController::class, 'bulkReport'])->name('payment-proof.bulk');

        // User management (extra actions)
        Route::post('/users/{user}/ban',     [\App\Http\Controllers\Admin\UserController::class, 'ban'])->name('users.ban');
        Route::delete('/users/{user}',       [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

        // Settings (commission, platform config)
        Route::get('/settings',  [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings',  [SettingsController::class, 'update'])->name('settings.update');

        // Content management (FAQ / Terms / Privacy / About)
        Route::get('/content',             [ContentController::class, 'index'])->name('content.index');
        Route::get('/content/{page}/edit', [ContentController::class, 'edit'])->name('content.edit');
        Route::put('/content/{page}',      [ContentController::class, 'update'])->name('content.update');

        // Reports
        Route::get('/reports',         [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/csv',     [ReportsController::class, 'exportCsv'])->name('reports.csv');

        // Announcements
        Route::get('/announcements',  [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [AnnouncementController::class, 'send'])->name('announcements.send');
    });
