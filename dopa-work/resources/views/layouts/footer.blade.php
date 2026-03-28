<footer class="bg-gray-900 text-gray-300 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">D</span>
                    </div>
                    <span class="font-bold text-xl text-white">{{ app()->getLocale() === 'ar' ? 'دوبا وورك' : 'Dopa Work' }}</span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">
                    {{ app()->getLocale() === 'ar'
                        ? 'منصة العمل الحر الأردنية الرائدة. ابحث عن أفضل المستقلين أو اعرض خدماتك بكل سهولة وأمان.'
                        : 'Jordan\'s leading freelancing marketplace. Find top talent or showcase your services safely.' }}
                </p>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-3">{{ app()->getLocale() === 'ar' ? 'روابط سريعة' : 'Quick Links' }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('services.index') }}" class="hover:text-white transition-colors">{{ app()->getLocale() === 'ar' ? 'تصفح الخدمات' : 'Browse Services' }}</a></li>
                    <li><a href="{{ route('freelancers.index') }}" class="hover:text-white transition-colors">{{ app()->getLocale() === 'ar' ? 'المستقلون' : 'Freelancers' }}</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">{{ app()->getLocale() === 'ar' ? 'سجل الآن' : 'Sign Up' }}</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-3">{{ app()->getLocale() === 'ar' ? 'الدعم' : 'Support' }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">{{ app()->getLocale() === 'ar' ? 'مركز المساعدة' : 'Help Center' }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ app()->getLocale() === 'ar' ? 'سياسة الاسترداد' : 'Refund Policy' }}</a></li>
                    <li><a href="mailto:support@dopawork.jo" class="hover:text-white transition-colors">support@dopawork.jo</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-white mb-3">{{ app()->getLocale() === 'ar' ? 'قانوني' : 'Legal' }}</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors">{{ app()->getLocale() === 'ar' ? 'شروط الاستخدام' : 'Terms of Service' }}</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">{{ app()->getLocale() === 'ar' ? 'سياسة الخصوصية' : 'Privacy Policy' }}</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm text-gray-500">
                © {{ date('Y') }} {{ app()->getLocale() === 'ar' ? 'دوبا وورك. جميع الحقوق محفوظة.' : 'Dopa Work. All rights reserved.' }}
                <span class="mx-2">|</span>
                {{ app()->getLocale() === 'ar' ? 'الأردن 🇯🇴' : 'Jordan 🇯🇴' }}
                <span class="mx-2">|</span>
                {{ app()->getLocale() === 'ar' ? 'العملة: دينار أردني (JOD)' : 'Currency: Jordanian Dinar (JOD)' }}
            </p>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'تابعنا:' : 'Follow:' }}</span>
                <a href="#" class="hover:text-white transition-colors">LinkedIn</a>
                <a href="#" class="hover:text-white transition-colors">Twitter</a>
                <a href="#" class="hover:text-white transition-colors">Instagram</a>
            </div>
        </div>
    </div>
</footer>
