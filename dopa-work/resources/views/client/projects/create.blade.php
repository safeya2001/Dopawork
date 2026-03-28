@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'نشر مشروع جديد' : 'Post a Project')
@section('content')
@php
    $cities = ['عمان','إربد','الزرقاء','العقبة','السلط','مادبا','جرش','عجلون','الكرك','معان','الطفيلة','المفرق'];
@endphp
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('client.projects.index') }}" class="text-sm text-gray-400 hover:text-primary-600">← {{ app()->getLocale()==='ar' ? 'العودة' : 'Back' }}</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ app()->getLocale()==='ar' ? 'نشر مشروع جديد' : 'Post a New Project' }}</h1>
    </div>

    <form method="POST" action="{{ route('client.projects.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Basic Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">{{ app()->getLocale()==='ar' ? 'معلومات المشروع' : 'Project Info' }}</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'عنوان المشروع' : 'Project Title' }} <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required maxlength="150"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 @error('title') border-red-400 @enderror"
                        placeholder="{{ app()->getLocale()==='ar' ? 'مثال: تصميم موقع إلكتروني لمتجر أردني' : 'e.g., Design a website for a Jordanian store' }}">
                    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'وصف المشروع' : 'Project Description' }} <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="5" required minlength="50"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 resize-none @error('description') border-red-400 @enderror"
                        placeholder="{{ app()->getLocale()==='ar' ? 'اشرح تفاصيل المشروع، المتطلبات، والنتائج المتوقعة...' : 'Describe the project in detail...' }}">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'التصنيف' : 'Category' }}</label>
                    <select name="category_id" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 bg-white">
                        <option value="">{{ app()->getLocale()==='ar' ? '-- اختر تصنيفاً --' : '-- Select Category --' }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->display_name }}
                            </option>
                            @foreach($cat->children as $child)
                                <option value="{{ $child->id }}" {{ old('category_id') == $child->id ? 'selected' : '' }}>
                                    &nbsp;&nbsp;— {{ $child->display_name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Budget --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">{{ app()->getLocale()==='ar' ? 'الميزانية' : 'Budget' }}</h2>

            <div class="flex gap-3 mb-4">
                <label class="flex-1 flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                    <input type="radio" name="budget_type" value="fixed" {{ old('budget_type','fixed')==='fixed'?'checked':'' }} onchange="toggleBudgetType('fixed')" class="accent-primary-600">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ app()->getLocale()==='ar' ? 'سعر ثابت' : 'Fixed Price' }}</p>
                        <p class="text-xs text-gray-400">JOD</p>
                    </div>
                </label>
                <label class="flex-1 flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                    <input type="radio" name="budget_type" value="hourly" {{ old('budget_type')==='hourly'?'checked':'' }} onchange="toggleBudgetType('hourly')" class="accent-primary-600">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ app()->getLocale()==='ar' ? 'بالساعة' : 'Hourly Rate' }}</p>
                        <p class="text-xs text-gray-400">JOD/{{ app()->getLocale()==='ar'?'ساعة':'hr' }}</p>
                    </div>
                </label>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" id="budgetMinLabel">{{ app()->getLocale()==='ar' ? 'الحد الأدنى (JOD)' : 'Min Budget (JOD)' }}</label>
                    <input type="number" name="budget_min" id="budget_min" value="{{ old('budget_min') }}" min="1" step="0.001"
                        oninput="validateBudget()"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400" placeholder="0.000">
                    @error('budget_min')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'الحد الأقصى (JOD)' : 'Max Budget (JOD)' }}</label>
                    <input type="number" name="budget_max" id="budget_max" value="{{ old('budget_max') }}" min="1" step="0.001"
                        oninput="validateBudget()"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400" placeholder="0.000">
                    @error('budget_max')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    <p id="budgetError" class="text-red-500 text-xs mt-1 hidden">{{ app()->getLocale()==='ar' ? 'الحد الأقصى يجب أن يكون أكبر من الحد الأدنى' : 'Max must be greater than min' }}</p>
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">{{ app()->getLocale()==='ar' ? 'تفاصيل إضافية' : 'Additional Details' }}</h2>
            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'المهارات المطلوبة' : 'Required Skills' }}</label>
                    <input type="text" name="required_skills" value="{{ old('required_skills') }}"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400"
                        placeholder="{{ app()->getLocale()==='ar' ? 'مثال: PHP, Laravel, Vue.js (افصل بفاصلة)' : 'e.g., PHP, Laravel, Vue.js (comma-separated)' }}">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'الموعد النهائي' : 'Deadline' }}</label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400">
                        @error('deadline')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'الموقع المفضل' : 'Preferred Location' }}</label>
                        <select name="preferred_location" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400 bg-white">
                            <option value="">{{ app()->getLocale()==='ar' ? 'أي مكان' : 'Anywhere in Jordan' }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ old('preferred_location')===$city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ app()->getLocale()==='ar' ? 'مرفقات (اختياري)' : 'Attachments (optional)' }}</label>
                    <input type="file" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx,.zip"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary-400">
                    <p class="text-xs text-gray-400 mt-1">{{ app()->getLocale()==='ar' ? 'يمكنك رفع عدة ملفات — حد أقصى 10MB لكل ملف' : 'Multiple files allowed — max 10MB each' }}</p>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3.5 rounded-xl transition-colors text-sm">
            🚀 {{ app()->getLocale()==='ar' ? 'نشر المشروع' : 'Post Project' }}
        </button>
    </form>
</div>
<script>
function toggleBudgetType(type) {
    const label = document.getElementById('budgetMinLabel');
    if (label) label.textContent = type === 'hourly'
        ? '{{ app()->getLocale()==="ar" ? "الحد الأدنى (JOD/ساعة)" : "Min Rate (JOD/hr)" }}'
        : '{{ app()->getLocale()==="ar" ? "الحد الأدنى (JOD)" : "Min Budget (JOD)" }}';
}
function validateBudget() {
    const min = parseFloat(document.getElementById('budget_min').value);
    const maxInput = document.getElementById('budget_max');
    const err = document.getElementById('budgetError');
    const max = parseFloat(maxInput.value);
    if (min && max && max < min) {
        err.classList.remove('hidden');
        maxInput.setCustomValidity('{{ app()->getLocale()==="ar" ? "الحد الأقصى يجب أن يكون أكبر من الحد الأدنى" : "Max must be greater than min" }}');
    } else {
        err.classList.add('hidden');
        maxInput.setCustomValidity('');
    }
}
</script>
@endsection
