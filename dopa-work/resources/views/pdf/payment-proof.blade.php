<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $isAr ? 'إيصال دفع - دوبا وورك' : 'Payment Receipt - Dopa Work' }}</title>
    <style>
        @if($isAr)
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }
        body { font-family: 'DejaVu Sans', sans-serif; direction: rtl; text-align: right; }
        @else
        body { font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif; direction: ltr; text-align: left; }
        @endif

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #ffffff; color: #1a1a2e; font-size: 12px; line-height: 1.6; }
        .page { max-width: 794px; margin: 0 auto; padding: 40px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #2563eb; }
        .logo-area { }
        .logo-box { width: 50px; height: 50px; background: #2563eb; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; }
        .logo-box span { color: white; font-size: 24px; font-weight: bold; }
        .platform-name { font-size: 20px; font-weight: bold; color: #2563eb; }
        .platform-sub { font-size: 10px; color: #6b7280; }
        .doc-info { text-align: {{ $isAr ? 'left' : 'right' }}; }
        .doc-title { font-size: 22px; font-weight: bold; color: #1e3a8a; margin-bottom: 5px; }
        .doc-reference { font-size: 11px; color: #6b7280; }
        .doc-reference span { font-weight: bold; color: #374151; }
        .badge-official { background: #059669; color: white; font-size: 9px; font-weight: bold; padding: 2px 8px; border-radius: 4px; display: inline-block; margin-top: 4px; letter-spacing: 1px; }

        /* Bilingual Section */
        .bilingual-title { text-align: center; margin: 20px 0; padding: 12px; background: linear-gradient(135deg, #eff6ff, #dbeafe); border-radius: 10px; border: 1px solid #bfdbfe; }
        .bilingual-title h1 { font-size: 16px; color: #1d4ed8; font-weight: bold; }
        .bilingual-title h2 { font-size: 13px; color: #3b82f6; margin-top: 4px; }

        /* Amount Box */
        .amount-box { background: linear-gradient(135deg, #1d4ed8, #2563eb); color: white; border-radius: 16px; padding: 25px; text-align: center; margin: 20px 0; }
        .amount-label { font-size: 11px; opacity: 0.8; margin-bottom: 8px; }
        .amount-value { font-size: 36px; font-weight: bold; letter-spacing: -1px; }
        .amount-currency { font-size: 16px; opacity: 0.9; margin-bottom: 12px; }
        .amount-words { font-size: 11px; opacity: 0.85; padding: 8px 15px; background: rgba(255,255,255,0.1); border-radius: 8px; margin: 0 auto; display: inline-block; }

        /* Details Table */
        .section-header { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #6b7280; letter-spacing: 1px; margin: 20px 0 10px; padding-bottom: 5px; border-bottom: 1px solid #e5e7eb; }
        .details-table { width: 100%; border-collapse: collapse; }
        .details-table tr { border-bottom: 1px solid #f3f4f6; }
        .details-table tr:last-child { border-bottom: none; }
        .details-table td { padding: 8px 4px; font-size: 11px; }
        .details-table .label { color: #6b7280; width: 40%; }
        .details-table .value { color: #111827; font-weight: 500; }
        .details-table .highlight { color: #2563eb; font-weight: bold; }
        .status-badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; background: #d1fae5; color: #065f46; }

        /* Parties */
        .parties-grid { display: flex; gap: 15px; margin: 15px 0; }
        .party-box { flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px; }
        .party-type { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 6px; }
        .party-name { font-size: 13px; font-weight: bold; color: #111827; }
        .party-email { font-size: 10px; color: #6b7280; margin-top: 2px; }

        /* Fee Breakdown */
        .fee-table { width: 100%; border-collapse: collapse; }
        .fee-table td { padding: 6px 4px; font-size: 11px; }
        .fee-table .fee-label { color: #374151; }
        .fee-table .fee-value { text-align: {{ $isAr ? 'left' : 'right' }}; font-weight: 500; }
        .fee-total { font-weight: bold; font-size: 13px; color: #1d4ed8; border-top: 2px solid #dbeafe; }

        /* Escrow Notice */
        .escrow-notice { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px; margin: 15px 0; }
        .escrow-notice p { font-size: 10px; color: #166534; }

        /* Stamp Area */
        .stamp-area { margin-top: 25px; display: flex; justify-content: space-between; align-items: flex-end; }
        .stamp-box { width: 130px; height: 130px; border: 2px dashed #d1d5db; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-align: center; color: #9ca3af; }
        .stamp-box p { font-size: 9px; line-height: 1.4; }
        .signature-area { text-align: {{ $isAr ? 'left' : 'right' }}; }
        .signature-line { width: 180px; border-bottom: 1px solid #374151; margin-bottom: 5px; height: 40px; }
        .signature-label { font-size: 10px; color: #6b7280; }
        .platform-seal { font-size: 9px; color: #2563eb; font-weight: bold; margin-top: 2px; }

        /* Footer */
        .footer { margin-top: 25px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; }
        .footer p { font-size: 9px; color: #9ca3af; line-height: 1.8; }
        .footer .verify { font-size: 9px; color: #2563eb; margin-top: 5px; }

        /* QR-like placeholder */
        .qr-placeholder { width: 60px; height: 60px; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
        .qr-placeholder p { font-size: 7px; color: #9ca3af; text-align: center; }

        .text-green { color: #059669; }
        .text-blue { color: #2563eb; }
        .text-muted { color: #6b7280; }
        .fw-bold { font-weight: bold; }
        .mt-1 { margin-top: 4px; }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="logo-area">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:5px;">
                <div class="logo-box"><span>D</span></div>
                <div>
                    <div class="platform-name">{{ $platform_name }}</div>
                    <div class="platform-sub">Jordan's Freelancing Marketplace | منصة العمل الحر الأردنية</div>
                </div>
            </div>
            <div><span class="badge-official">✓ OFFICIAL DOCUMENT • وثيقة رسمية</span></div>
        </div>
        <div class="doc-info">
            <div class="doc-title">{{ $isAr ? 'إيصال دفع رسمي' : 'Official Payment Receipt' }}</div>
            <div class="doc-reference">
                {{ $isAr ? 'رقم الطلب:' : 'Order No:' }}
                <span>{{ $order->order_number }}</span>
            </div>
            <div class="doc-reference">
                {{ $isAr ? 'المرجع:' : 'Reference:' }}
                <span>{{ $order->payment?->reference ?? 'DW-REF-' . str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="doc-reference">
                {{ $isAr ? 'التاريخ:' : 'Date:' }}
                <span>{{ $generatedAt->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    {{-- Bilingual Title --}}
    <div class="bilingual-title">
        <h1>Payment Proof Document | وثيقة إثبات الدفع</h1>
        <h2>Dopa Work Freelancing Platform — Jordan (JOD) | منصة دوبا وورك — الأردن</h2>
    </div>

    {{-- Amount Box --}}
    <div class="amount-box">
        <div class="amount-label">
            {{ $isAr ? 'المبلغ الإجمالي المدفوع | Total Amount Paid' : 'Total Amount Paid | المبلغ الإجمالي المدفوع' }}
        </div>
        <div class="amount-value">{{ number_format($order->total_amount, 3) }}</div>
        <div class="amount-currency">JOD — {{ $isAr ? 'دينار أردني' : 'Jordanian Dinar' }}</div>
        <div class="amount-words">
            🔤 {{ $amountInWords }}
        </div>
    </div>

    {{-- Parties --}}
    <div class="section-header">
        {{ $isAr ? 'أطراف العقد • Contract Parties' : 'Contract Parties • أطراف العقد' }}
    </div>
    <div class="parties-grid">
        <div class="party-box">
            <div class="party-type">{{ $isAr ? '👤 العميل • CLIENT' : '👤 CLIENT • العميل' }}</div>
            <div class="party-name">{{ $order->client->name }}</div>
            <div class="party-email">{{ $order->client->email }}</div>
        </div>
        <div class="party-box">
            <div class="party-type">{{ $isAr ? '💼 المستقل • FREELANCER' : '💼 FREELANCER • المستقل' }}</div>
            <div class="party-name">{{ $order->freelancer->name }}</div>
            <div class="party-email">{{ $order->freelancer->email }}</div>
        </div>
    </div>

    {{-- Order Details --}}
    <div class="section-header">
        {{ $isAr ? 'تفاصيل الطلب • Order Details' : 'Order Details • تفاصيل الطلب' }}
    </div>
    <table class="details-table">
        <tr>
            <td class="label">{{ $isAr ? 'الخدمة / Service' : 'Service / الخدمة' }}</td>
            <td class="value">{{ $order->service->title }}</td>
        </tr>
        <tr>
            <td class="label">{{ $isAr ? 'الباقة / Package' : 'Package / الباقة' }}</td>
            <td class="value">{{ $order->package?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">{{ $isAr ? 'مدة التسليم / Delivery' : 'Delivery / مدة التسليم' }}</td>
            <td class="value">{{ $order->delivery_days }} {{ $isAr ? 'أيام' : 'Days' }}</td>
        </tr>
        <tr>
            <td class="label">{{ $isAr ? 'تاريخ الطلب / Order Date' : 'Order Date / تاريخ الطلب' }}</td>
            <td class="value">{{ $order->created_at->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">{{ $isAr ? 'تاريخ الإكمال / Completion' : 'Completion / تاريخ الإكمال' }}</td>
            <td class="value">{{ $order->completed_at?->format('d/m/Y') ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">{{ $isAr ? 'الحالة / Status' : 'Status / الحالة' }}</td>
            <td class="value"><span class="status-badge">{{ ucfirst($order->status) }}</span></td>
        </tr>
    </table>

    {{-- Fee Breakdown --}}
    <div class="section-header">
        {{ $isAr ? 'تفصيل المبالغ • Amount Breakdown' : 'Amount Breakdown • تفصيل المبالغ' }}
    </div>
    <table class="fee-table">
        <tr>
            <td class="fee-label">{{ $isAr ? 'قيمة الخدمة / Service Value' : 'Service Value / قيمة الخدمة' }}</td>
            <td class="fee-value">{{ number_format($order->subtotal, 3) }} JOD</td>
        </tr>
        <tr>
            <td class="fee-label">{{ $isAr ? 'رسوم المنصة (15%) / Platform Fee' : 'Platform Fee (15%) / رسوم المنصة' }}</td>
            <td class="fee-value">{{ number_format($order->platform_fee, 3) }} JOD</td>
        </tr>
        <tr class="fee-total">
            <td class="fee-label fw-bold">{{ $isAr ? 'الإجمالي المدفوع / Total Paid' : 'Total Paid / الإجمالي المدفوع' }}</td>
            <td class="fee-value">{{ number_format($order->total_amount, 3) }} JOD</td>
        </tr>
        <tr>
            <td class="fee-label text-muted" style="font-size:10px">{{ $isAr ? 'مستحقات المستقل / Freelancer Earnings' : 'Freelancer Earnings / مستحقات المستقل' }}</td>
            <td class="fee-value text-green" style="font-size:10px">{{ number_format($order->freelancer_earnings, 3) }} JOD</td>
        </tr>
    </table>

    {{-- Escrow Notice --}}
    <div class="escrow-notice">
        <p>
            🔒 <strong>{{ $isAr ? 'نظام الضمان:' : 'Escrow Protection:' }}</strong>
            {{ $isAr
                ? 'تم الاحتفاظ بالمبلغ في نظام الضمان الآمن الخاص بمنصة دوبا وورك حتى اكتمال العمل وموافقة العميل. هذه الوثيقة تؤكد إتمام العملية بنجاح.'
                : 'The payment was held in Dopa Work\'s secure escrow system until the work was completed and approved by the client. This document confirms the successful completion of the transaction.' }}
        </p>
    </div>

    {{-- Stamp & Signature Area --}}
    <div class="stamp-area">
        <div>
            <div class="stamp-box">
                <p>{{ $isAr ? 'ختم المنصة الرسمي' : 'Official Platform' }}<br>{{ $isAr ? 'دوبا وورك' : 'Seal' }}<br>🏛️</p>
            </div>
            <p style="font-size:9px;color:#9ca3af;margin-top:5px;text-align:center;">
                {{ $isAr ? 'مكان الختم' : 'Stamp Area' }}
            </p>
        </div>

        <div style="text-align:center;">
            <div class="qr-placeholder">
                <p>QR<br>{{ $isAr ? 'تحقق' : 'Verify' }}</p>
            </div>
            <p style="font-size:8px;color:#9ca3af;margin-top:3px;">
                {{ $isAr ? 'رمز التحقق' : 'Verification Code' }}<br>
                {{ strtoupper(substr(md5($order->order_number), 0, 12)) }}
            </p>
        </div>

        <div class="signature-area">
            <div class="signature-line"></div>
            <div class="signature-label">{{ $isAr ? 'التوقيع المعتمد / Authorized Signature' : 'Authorized Signature / التوقيع المعتمد' }}</div>
            <div class="platform-seal">DOPA WORK PLATFORM | منصة دوبا وورك</div>
            <div style="font-size:9px;color:#6b7280;margin-top:2px;">support@dopawork.jo | +962 7x-xxx-xxxx</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>
            {{ $isAr
                ? 'هذه وثيقة رسمية صادرة عن منصة دوبا وورك للعمل الحر - الأردن | تاريخ الإصدار: ' . $generatedAt->format('d/m/Y H:i:s')
                : 'This is an official document issued by Dopa Work Freelancing Platform - Jordan | Issued: ' . $generatedAt->format('d/m/Y H:i:s') }}
        </p>
        <p class="mt-1">
            {{ $isAr ? 'للتحقق من صحة هذه الوثيقة أو للاستفسارات:' : 'To verify this document or for inquiries:' }}
        </p>
        <div class="verify">support@dopawork.jo | www.dopawork.jo</div>
    </div>

</div>
</body>
</html>
