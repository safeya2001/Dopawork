<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    /**
     * Generate bilingual payment proof PDF for an order.
     */
    public function generatePaymentProof(Order $order, string $locale = 'en'): \Barryvdh\DomPDF\PDF
    {
        $data = [
            'order' => $order->load(['client', 'freelancer', 'service', 'package', 'payment']),
            'locale' => $locale,
            'isAr' => $locale === 'ar',
            'amountInWords' => $this->amountToWords($order->total_amount, $locale),
            'generatedAt' => now(),
            'platform_name' => $locale === 'ar' ? config('platform.name_ar') : config('app.name'),
        ];

        $pdf = Pdf::loadView('pdf.payment-proof', $data);
        $pdf->setPaper('a4', $locale === 'ar' ? 'landscape' : 'portrait');

        if ($locale === 'ar') {
            $pdf->setOptions(['isRemoteEnabled' => true, 'defaultFont' => 'dejavusans']);
        }

        return $pdf;
    }

    /**
     * Convert a JOD amount to words in Arabic and English.
     */
    public function amountToWords(float $amount, string $locale = 'en'): string
    {
        $jod = (int) floor($amount);
        $fils = (int) round(($amount - $jod) * 1000); // JOD has 3 decimals (fils)

        if ($locale === 'ar') {
            return $this->toArabicWords($jod, $fils);
        }

        return $this->toEnglishWords($jod, $fils);
    }

    private function toEnglishWords(int $jod, int $fils): string
    {
        $words = $this->numberToEnglish($jod) . ' Jordanian Dinar';
        if ($fils > 0) {
            $words .= ' and ' . $this->numberToEnglish($fils) . ' Fils';
        }
        return $words . ' Only';
    }

    private function toArabicWords(int $jod, int $fils): string
    {
        $words = $this->numberToArabic($jod) . ' ديناراً أردنياً';
        if ($fils > 0) {
            $words .= ' و' . $this->numberToArabic($fils) . ' فلساً';
        }
        return $words . ' فقط لا غير';
    }

    private function numberToEnglish(int $n): string
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
                 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
                 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        if ($n === 0) return 'Zero';
        if ($n < 20) return $ones[$n];
        if ($n < 100) return $tens[intval($n / 10)] . ($n % 10 ? '-' . $ones[$n % 10] : '');
        if ($n < 1000) return $ones[intval($n / 100)] . ' Hundred' . ($n % 100 ? ' ' . $this->numberToEnglish($n % 100) : '');
        if ($n < 1000000) return $this->numberToEnglish(intval($n / 1000)) . ' Thousand' . ($n % 1000 ? ' ' . $this->numberToEnglish($n % 1000) : '');
        return (string) $n;
    }

    private function numberToArabic(int $n): string
    {
        $ones = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة',
                 'عشرة', 'أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر',
                 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];
        $tens = ['', '', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
        $hundreds = ['', 'مئة', 'مئتان', 'ثلاثمئة', 'أربعمئة', 'خمسمئة', 'ستمئة', 'سبعمئة', 'ثمانمئة', 'تسعمئة'];

        if ($n === 0) return 'صفر';
        if ($n < 20) return $ones[$n];
        if ($n < 100) return $tens[intval($n / 10)] . ($n % 10 ? ' و' . $ones[$n % 10] : '');
        if ($n < 1000) return $hundreds[intval($n / 100)] . ($n % 100 ? ' و' . $this->numberToArabic($n % 100) : '');
        if ($n < 1000000) return $this->numberToArabic(intval($n / 1000)) . ' ألف' . ($n % 1000 ? ' و' . $this->numberToArabic($n % 1000) : '');
        return (string) $n;
    }
}
