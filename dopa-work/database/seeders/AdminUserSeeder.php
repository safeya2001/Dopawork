<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@dopawork.jo'],
            [
                'name' => 'Dopa Work Admin',
                'name_ar' => 'مدير دوبا وورك',
                'password' => Hash::make('DopaWork@2024!'),
                'role' => 'super_admin',
                'status' => 'active',
                'locale' => 'ar',
                'wallet_balance' => 0,
            ]
        );

        // Demo freelancer
        $freelancer = User::firstOrCreate(
            ['email' => 'freelancer@dopawork.jo'],
            [
                'name' => 'Ahmed Al-Rashid',
                'name_ar' => 'أحمد الراشد',
                'password' => Hash::make('Demo@1234'),
                'role' => 'freelancer',
                'status' => 'active',
                'locale' => 'ar',
                'wallet_balance' => 250.500,
            ]
        );

        $freelancer->freelancerProfile()->firstOrCreate(
            ['user_id' => $freelancer->id],
            [
                'professional_title' => 'Full Stack Laravel Developer',
                'professional_title_ar' => 'مطور لارافيل متكامل',
                'overview' => 'Expert in Laravel, Vue.js, and modern web development with 5+ years experience.',
                'overview_ar' => 'خبرة في لارافيل وفيو جيه إس وتطوير الويب الحديث لأكثر من 5 سنوات.',
                'skills' => ['Laravel', 'PHP', 'Vue.js', 'MySQL', 'REST APIs'],
                'hourly_rate' => 25.000,
                'experience_level' => 'expert',
                'rating' => 4.90,
                'total_reviews' => 47,
                'is_verified' => true,
                'is_available' => true,
            ]
        );

        // Demo client
        User::firstOrCreate(
            ['email' => 'client@dopawork.jo'],
            [
                'name' => 'Sara Business',
                'name_ar' => 'سارة بيزنس',
                'password' => Hash::make('Demo@1234'),
                'role' => 'client',
                'status' => 'active',
                'locale' => 'ar',
                'wallet_balance' => 500.000,
            ]
        );
    }
}
