<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = PlatformSetting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'commission_percent' => 'required|numeric|min:0|max:50',
            'platform_name'      => 'required|string|max:100',
            'platform_name_ar'   => 'required|string|max:100',
            'support_email'      => 'required|email|max:150',
            'min_withdrawal'     => 'required|numeric|min:0',
            'max_withdrawal'     => 'required|numeric|min:1',
        ]);

        $fields = [
            'commission_percent' => $request->commission_percent,
            'platform_name'      => $request->platform_name,
            'platform_name_ar'   => $request->platform_name_ar,
            'support_email'      => $request->support_email,
            'min_withdrawal'     => $request->min_withdrawal,
            'max_withdrawal'     => $request->max_withdrawal,
        ];

        foreach ($fields as $key => $value) {
            PlatformSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم حفظ الإعدادات بنجاح ✓'
            : 'Settings saved successfully ✓');
    }
}
