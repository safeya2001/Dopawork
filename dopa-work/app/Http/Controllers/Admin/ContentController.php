<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    private array $pages = ['faq', 'terms', 'privacy', 'about'];

    public function index()
    {
        $pages = $this->pages;
        return view('admin.content.index', compact('pages'));
    }

    public function edit(string $page)
    {
        abort_unless(in_array($page, $this->pages), 404);
        $content_en = PlatformSetting::where('key', "content_{$page}_en")->value('value') ?? '';
        $content_ar = PlatformSetting::where('key', "content_{$page}_ar")->value('value') ?? '';
        return view('admin.content.edit', compact('page', 'content_en', 'content_ar'));
    }

    public function update(Request $request, string $page)
    {
        abort_unless(in_array($page, $this->pages), 404);

        $request->validate([
            'content_en' => 'required|string',
            'content_ar' => 'required|string',
        ]);

        PlatformSetting::updateOrCreate(['key' => "content_{$page}_en"], ['value' => $request->content_en, 'group' => 'content']);
        PlatformSetting::updateOrCreate(['key' => "content_{$page}_ar"], ['value' => $request->content_ar, 'group' => 'content']);

        return back()->with('success', app()->getLocale() === 'ar'
            ? 'تم حفظ المحتوى بنجاح ✓'
            : 'Content saved successfully ✓');
    }
}
