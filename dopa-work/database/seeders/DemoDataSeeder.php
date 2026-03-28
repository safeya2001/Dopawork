<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\IdentityVerification;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $freelancer = User::where('email', 'freelancer@dopawork.jo')->first();
        $client     = User::where('email', 'client@dopawork.jo')->first();

        if (!$freelancer || !$client) {
            $this->command->warn('Demo users not found. Run AdminUserSeeder first.');
            return;
        }

        // ── 1. Verify the client (approved identity verification) ──────────
        IdentityVerification::firstOrCreate(
            ['user_id' => $client->id],
            [
                'document_type'  => 'national_id',
                'front_image'    => 'demo/id_front.jpg',
                'back_image'     => 'demo/id_back.jpg',
                'status'         => 'approved',
                'reviewed_at'    => now(),
            ]
        );
        // Make sure client is active with ZERO balance (must deposit themselves)
        $client->update(['status' => 'active', 'wallet_balance' => 0.000]);

        // ── 2. Make sure freelancer is active ──────────────────────────────
        $freelancer->update(['status' => 'active']);

        // ── 3. Get a category ──────────────────────────────────────────────
        $category = Category::where('slug', 'web-development')
            ->orWhere('slug', 'programming')
            ->orWhere('slug', 'tech')
            ->first() ?? Category::first();

        if (!$category) {
            $this->command->warn('No categories found. Run CategorySeeder first.');
            return;
        }

        // ── 4. Create demo services ────────────────────────────────────────
        $services = [
            [
                'title'       => 'Professional Laravel Web Application',
                'title_ar'    => 'تطبيق ويب لارافيل احترافي',
                'slug'        => 'professional-laravel-web-application',
                'description' => 'I will build you a professional Laravel web application with authentication, dashboard, REST APIs, and database design. Fully responsive and well-documented code.',
                'description_ar' => 'سأبني لك تطبيق ويب لارافيل احترافي مع نظام تسجيل الدخول، لوحة تحكم، APIs، وتصميم قاعدة بيانات. كود متجاوب ومنظم بالكامل.',
                'is_featured' => true,
                'rating'      => 4.90,
                'packages' => [
                    ['type'=>'basic',    'name'=>'Basic',    'name_ar'=>'الأساسية',  'price'=>35.000, 'delivery_days'=>5,  'revisions'=>2, 'description'=>'Simple Laravel app with auth and basic CRUD.', 'description_ar'=>'تطبيق لارافيل بسيط مع تسجيل دخول وعمليات CRUD أساسية.'],
                    ['type'=>'standard', 'name'=>'Standard', 'name_ar'=>'الاحترافية','price'=>75.000, 'delivery_days'=>10, 'revisions'=>3, 'description'=>'Full app with REST API, roles, and admin panel.', 'description_ar'=>'تطبيق كامل مع REST API وصلاحيات ولوحة إدارة.'],
                    ['type'=>'premium',  'name'=>'Premium',  'name_ar'=>'المتميزة',  'price'=>150.000,'delivery_days'=>21, 'revisions'=>5, 'description'=>'Enterprise-grade app with advanced features, deployment, and support.', 'description_ar'=>'تطبيق على مستوى المؤسسات مع ميزات متقدمة ونشر ودعم.'],
                ],
            ],
            [
                'title'       => 'React.js & Next.js Frontend Development',
                'title_ar'    => 'تطوير واجهة React.js و Next.js',
                'slug'        => 'reactjs-nextjs-frontend-development',
                'description' => 'I will create a modern, fast, and responsive frontend using React.js or Next.js with Tailwind CSS and best practices.',
                'description_ar' => 'سأنشئ واجهة أمامية حديثة وسريعة ومتجاوبة باستخدام React.js أو Next.js مع Tailwind CSS.',
                'is_featured' => true,
                'rating'      => 4.75,
                'packages' => [
                    ['type'=>'basic',    'name'=>'Basic',    'name_ar'=>'الأساسية',  'price'=>25.000, 'delivery_days'=>3,  'revisions'=>2, 'description'=>'Simple React component or landing page.', 'description_ar'=>'مكون React بسيط أو صفحة هبوط.'],
                    ['type'=>'standard', 'name'=>'Standard', 'name_ar'=>'الاحترافية','price'=>55.000, 'delivery_days'=>7,  'revisions'=>3, 'description'=>'Multi-page React app with routing and API integration.', 'description_ar'=>'تطبيق React متعدد الصفحات مع توجيه وتكامل API.'],
                    ['type'=>'premium',  'name'=>'Premium',  'name_ar'=>'المتميزة',  'price'=>110.000,'delivery_days'=>14, 'revisions'=>5, 'description'=>'Full Next.js app with SSR, auth, and deployment.', 'description_ar'=>'تطبيق Next.js كامل مع SSR وتسجيل دخول ونشر.'],
                ],
            ],
            [
                'title'       => 'MySQL Database Design & Optimization',
                'title_ar'    => 'تصميم وتحسين قاعدة بيانات MySQL',
                'slug'        => 'mysql-database-design-optimization',
                'description' => 'Professional database design with ER diagrams, indexes, and query optimization for high-performance applications.',
                'description_ar' => 'تصميم قاعدة بيانات احترافي مع مخططات ER، فهارس، وتحسين الاستعلامات لتطبيقات عالية الأداء.',
                'is_featured' => false,
                'rating'      => 4.80,
                'packages' => [
                    ['type'=>'basic',    'name'=>'Basic',    'name_ar'=>'الأساسية',  'price'=>15.000, 'delivery_days'=>2,  'revisions'=>1, 'description'=>'Design up to 10 tables with relationships.', 'description_ar'=>'تصميم حتى 10 جداول مع علاقاتها.'],
                    ['type'=>'standard', 'name'=>'Standard', 'name_ar'=>'الاحترافية','price'=>35.000, 'delivery_days'=>5,  'revisions'=>2, 'description'=>'Full schema + migrations + seed data.', 'description_ar'=>'مخطط كامل + migrations + بيانات اختبارية.'],
                    ['type'=>'premium',  'name'=>'Premium',  'name_ar'=>'المتميزة',  'price'=>70.000, 'delivery_days'=>10, 'revisions'=>3, 'description'=>'Complete DB solution with optimization and documentation.', 'description_ar'=>'حل قاعدة بيانات كامل مع تحسين وتوثيق.'],
                ],
            ],
        ];

        foreach ($services as $svcData) {
            $packages = $svcData['packages'];
            unset($svcData['packages']);

            $service = Service::firstOrCreate(
                ['slug' => $svcData['slug']],
                array_merge($svcData, [
                    'user_id'       => $freelancer->id,
                    'category_id'   => $category->id,
                    'status'        => 'active',
                    'delivery_days' => $packages[0]['delivery_days'],
                    'revisions'     => $packages[0]['revisions'],
                    'orders_count'  => rand(5, 47),
                    'reviews_count' => rand(3, 30),
                ])
            );

            foreach ($packages as $pkg) {
                ServicePackage::firstOrCreate(
                    ['service_id' => $service->id, 'type' => $pkg['type']],
                    [
                        'name'            => $pkg['name'],
                        'name_ar'         => $pkg['name_ar'],
                        'description'     => $pkg['description'],
                        'description_ar'  => $pkg['description_ar'],
                        'price'           => $pkg['price'],
                        'delivery_days'   => $pkg['delivery_days'],
                        'revisions'       => $pkg['revisions'],
                        'is_active'       => true,
                    ]
                );
            }

            $this->command->info("✓ Service: {$service->title}");
        }

        $this->command->info('✓ Client verified and wallet_balance = 500 JOD');
        $this->command->info('Demo data seeded successfully!');
    }
}
