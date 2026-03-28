# Dopa Work — Setup Guide
## منصة دوبا وورك — دليل التثبيت

---

## Prerequisites

- XAMPP (PHP 8.1+) — at `C:\xampp\`
- MySQL (included with XAMPP)
- Composer — at `C:\xampp\php\composer`

---

## Quick Start (5 Steps)

### 1. Install Composer Dependencies
```bash
cd C:\Users\JYIF2\Downloads\FREELANCE3\dopa-work
C:\xampp\php\php.exe C:\xampp\php\composer install
```

### 2. Generate App Key
```bash
C:\xampp\php\php.exe artisan key:generate
```

### 3. Create Database
Open phpMyAdmin at http://localhost/phpmyadmin and create a database named `dopa_work`.

### 4. Run Migrations & Seeders
```bash
C:\xampp\php\php.exe artisan migrate --seed
```

### 5. Start Development Server
```bash
C:\xampp\php\php.exe artisan serve
```

Visit: **http://localhost:8000**

---

## Demo Accounts

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@dopawork.jo | DopaWork@2024! |
| Freelancer | freelancer@dopawork.jo | Demo@1234 |
| Client | client@dopawork.jo | Demo@1234 |

---

## Key URLs

| URL | Description |
|-----|-------------|
| `/` | Home page (Arabic by default) |
| `/lang/en` | Switch to English |
| `/lang/ar` | Switch to Arabic |
| `/register` | User registration |
| `/login` | Login |
| `/verify-identity` | KYC document upload |
| `/services` | Browse services marketplace |
| `/client/dashboard` | Client dashboard |
| `/freelancer/dashboard` | Freelancer dashboard |
| `/admin/dashboard` | Admin panel |

---

## Architecture Overview

```
app/
├── Console/Commands/
│   └── ReleaseEscrowFunds.php    ← Cron: auto-release escrow after 7 days
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                 ← Login, Register, Identity Verification
│   │   ├── Client/               ← Order placement, checkout, receipts
│   │   ├── Freelancer/           ← Service management, order delivery
│   │   ├── Admin/                ← Moderation, escrow, disputes, users
│   │   ├── HomeController        ← Landing page
│   │   └── MarketplaceController ← Service listings, freelancer profiles
│   └── Middleware/
│       ├── SetLocale             ← Auto Arabic/English switching
│       ├── EnsureRole            ← Role-based access (client/freelancer/admin)
│       └── EnsureIdentityVerified← KYC gate
├── Models/                       ← 15 models with relationships
└── Services/
    ├── EscrowService             ← Hold/release/refund escrow funds
    ├── WalletService             ← Credit/debit JOD wallet
    ├── OrderService              ← Full order lifecycle
    ├── NotificationService       ← Bilingual in-app notifications
    └── PdfService                ← Bilingual A4 payment proof PDFs
```

---

## Payment Flow (Escrow)

```
Client pays → EscrowTransaction (status: held)
                ↓
           Work delivered
                ↓
     Client approves delivery
                ↓
        EscrowTransaction (status: released)
        Freelancer wallet credited
        Platform fee retained
                ↓
     OR: Auto-release after 7 days if no action
```

---

## Bilingual Support

- Default language: **Arabic (RTL)**
- Switch: `GET /lang/en` or `GET /lang/ar`
- All models have `display_*` accessors that return Arabic or English based on locale
- PDF receipts generated in selected language with amounts in words:
  - EN: "Fifty Jordanian Dinar Only"
  - AR: "خمسون ديناراً أردنياً فقط لا غير"

---

## Scheduled Commands

Add to Windows Task Scheduler or crontab:
```
* * * * * C:\xampp\php\php.exe C:\Users\JYIF2\Downloads\FREELANCE3\dopa-work\artisan schedule:run
```

---

## Payment Gateways

### CliQ (Jordan)
Set in `.env`:
```
CLIQ_ALIAS=your-cliq-alias
CLIQ_BANK_ACCOUNT=JOxxBANKxxxxxxxxxxxxxxxxxxxxxxxx
```

### Stripe (International Cards)
```
STRIPE_KEY=pk_live_xxx
STRIPE_SECRET=sk_live_xxx
```

---

## Security Features

- ✅ CSRF protection on all forms
- ✅ Identity verification (KYC) required before transacting
- ✅ Role-based access control (client/freelancer/admin/super_admin)
- ✅ Secure file storage (identity documents in `private` disk)
- ✅ Escrow funds protection
- ✅ Input validation on all forms
- ✅ Rate limiting (Laravel default)
- ✅ SQL injection protection (Eloquent ORM)
- ✅ XSS protection (Blade auto-escaping)
