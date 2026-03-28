<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Programming & Tech', 'name_ar' => 'البرمجة والتقنية', 'icon' => '💻', 'children' => [
                ['name' => 'Web Development', 'name_ar' => 'تطوير المواقع'],
                ['name' => 'Mobile Apps', 'name_ar' => 'تطبيقات الموبايل'],
                ['name' => 'WordPress', 'name_ar' => 'ووردبريس'],
                ['name' => 'E-Commerce', 'name_ar' => 'التجارة الإلكترونية'],
                ['name' => 'Cybersecurity', 'name_ar' => 'الأمن السيبراني'],
            ]],
            ['name' => 'Graphic Design', 'name_ar' => 'التصميم الجرافيكي', 'icon' => '🎨', 'children' => [
                ['name' => 'Logo Design', 'name_ar' => 'تصميم الشعارات'],
                ['name' => 'Brand Identity', 'name_ar' => 'الهوية البصرية'],
                ['name' => 'Social Media Design', 'name_ar' => 'تصميم السوشيال ميديا'],
                ['name' => 'UI/UX Design', 'name_ar' => 'تصميم الواجهات'],
            ]],
            ['name' => 'Content Writing', 'name_ar' => 'كتابة المحتوى', 'icon' => '✍️', 'children' => [
                ['name' => 'Arabic Content', 'name_ar' => 'المحتوى العربي'],
                ['name' => 'English Content', 'name_ar' => 'المحتوى الإنجليزي'],
                ['name' => 'Translation', 'name_ar' => 'الترجمة'],
                ['name' => 'SEO Writing', 'name_ar' => 'كتابة SEO'],
            ]],
            ['name' => 'Digital Marketing', 'name_ar' => 'التسويق الرقمي', 'icon' => '📢', 'children' => [
                ['name' => 'Social Media Marketing', 'name_ar' => 'التسويق عبر السوشيال ميديا'],
                ['name' => 'Google Ads', 'name_ar' => 'إعلانات جوجل'],
                ['name' => 'SEO', 'name_ar' => 'تحسين محركات البحث'],
                ['name' => 'Email Marketing', 'name_ar' => 'التسويق عبر البريد الإلكتروني'],
            ]],
            ['name' => 'Video & Animation', 'name_ar' => 'الفيديو والأنيميشن', 'icon' => '🎬', 'children' => [
                ['name' => 'Video Editing', 'name_ar' => 'مونتاج الفيديو'],
                ['name' => 'Motion Graphics', 'name_ar' => 'الموشن جرافيك'],
                ['name' => 'Explainer Videos', 'name_ar' => 'فيديوهات شرح'],
            ]],
            ['name' => 'Business & Finance', 'name_ar' => 'الأعمال والمال', 'icon' => '📊', 'children' => [
                ['name' => 'Business Plans', 'name_ar' => 'خطط الأعمال'],
                ['name' => 'Accounting', 'name_ar' => 'المحاسبة'],
                ['name' => 'Legal Consulting', 'name_ar' => 'الاستشارات القانونية'],
            ]],
            ['name' => 'Voice & Audio', 'name_ar' => 'الصوت والصوتيات', 'icon' => '🎙️', 'children' => [
                ['name' => 'Voice Over (Arabic)', 'name_ar' => 'تعليق صوتي عربي'],
                ['name' => 'Voice Over (English)', 'name_ar' => 'تعليق صوتي إنجليزي'],
                ['name' => 'Podcast Editing', 'name_ar' => 'مونتاج البودكاست'],
            ]],
            ['name' => 'Photography', 'name_ar' => 'التصوير الفوتوغرافي', 'icon' => '📸', 'children' => [
                ['name' => 'Product Photography', 'name_ar' => 'تصوير المنتجات'],
                ['name' => 'Portrait Photography', 'name_ar' => 'تصوير البورتريه'],
                ['name' => 'Photo Editing', 'name_ar' => 'تحرير الصور'],
            ]],
        ];

        foreach ($categories as $i => $cat) {
            $parent = Category::create([
                'name' => $cat['name'],
                'name_ar' => $cat['name_ar'],
                'slug' => Str::slug($cat['name']),
                'icon' => $cat['icon'],
                'is_active' => true,
                'sort_order' => $i + 1,
            ]);

            foreach ($cat['children'] ?? [] as $j => $child) {
                Category::create([
                    'name' => $child['name'],
                    'name_ar' => $child['name_ar'],
                    'slug' => Str::slug($child['name']) . '-' . $parent->id,
                    'parent_id' => $parent->id,
                    'is_active' => true,
                    'sort_order' => $j + 1,
                ]);
            }
        }
    }
}
