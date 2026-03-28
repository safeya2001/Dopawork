@extends('layouts.app')
@section('title', 'إضافة خدمة')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
  <a href="{{ route('freelancer.services.index') }}" class="text-sm text-gray-400 hover:text-primary-600 mb-5 block">← خدماتي</a>
  <h1 class="text-2xl font-bold text-gray-900 mb-6">إضافة خدمة جديدة</h1>

  @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 text-sm text-red-700">
      <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <form method="POST" action="{{ route('freelancer.services.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- Basic Info --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
      <h2 class="font-semibold text-gray-900">📝 المعلومات الأساسية</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الخدمة (عربي) *</label>
          <input name="title_ar" value="{{ old('title_ar') }}" required dir="rtl"
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400"
            placeholder="مثال: تصميم موقع Laravel احترافي">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Service Title (English) *</label>
          <input name="title" value="{{ old('title') }}" required
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400"
            placeholder="Professional Laravel Website">
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">الفئة *</label>
          <select name="category_id" id="catSelect" required onchange="loadSubs(this.value)"
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary-400">
            <option value="">اختر فئة...</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->name_ar }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">الفئة الفرعية</label>
          <select name="subcategory_id" id="subSelect"
            class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary-400">
            <option value="">— اختياري —</option>
          </select>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">وصف الخدمة (عربي) * — 100 حرف على الأقل</label>
        <textarea name="description_ar" rows="4" required minlength="100" dir="rtl"
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary-400 resize-none"
          placeholder="اشرح ما تقدمه...">{{ old('description_ar') }}</textarea>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Description (English) *</label>
        <textarea name="description" rows="4" required minlength="100"
          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary-400 resize-none"
          placeholder="Describe your service...">{{ old('description') }}</textarea>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">مدة التسليم (أيام) *</label>
          <input name="delivery_days" type="number" min="1" max="90" value="{{ old('delivery_days',3) }}" required
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">عدد التعديلات *</label>
          <input name="revisions" type="number" min="0" max="10" value="{{ old('revisions',2) }}" required
            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-primary-400">
        </div>
      </div>
    </div>

    {{-- Images --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
      <h2 class="font-semibold text-gray-900">🖼️ الصور</h2>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">صورة الغلاف * (حد أقصى 5MB)</label>
        <input type="file" name="cover_image" accept="image/*" required
          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">معرض الصور (اختياري — حد أقصى 8 صور)</label>
        <input type="file" name="gallery[]" accept="image/*" multiple
          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none">
      </div>
    </div>

    {{-- Packages --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <h2 class="font-semibold text-gray-900 mb-4">📦 الباقات (Basic / Standard / Premium)</h2>
      <div class="space-y-6">
        @foreach([['basic','أساسية'],['standard','متوسطة'],['premium','مميزة']] as [$type,$label])
        <div class="border border-gray-100 rounded-xl p-4">
          <h3 class="font-semibold text-gray-700 mb-3 text-sm">{{ $label }} ({{ ucfirst($type) }})</h3>
          <input type="hidden" name="packages[{{ $loop->index }}][type]" value="{{ $type }}">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="text-xs text-gray-500 mb-1 block">اسم الباقة (عربي) *</label>
              <input name="packages[{{ $loop->index }}][name_ar]" value="{{ old("packages.{$loop->index}.name_ar") }}" required dir="rtl"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400">
            </div>
            <div>
              <label class="text-xs text-gray-500 mb-1 block">Package Name (English) *</label>
              <input name="packages[{{ $loop->index }}][name]" value="{{ old("packages.{$loop->index}.name") }}" required
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400">
            </div>
            <div>
              <label class="text-xs text-gray-500 mb-1 block">وصف الباقة (عربي) *</label>
              <textarea name="packages[{{ $loop->index }}][description_ar]" rows="2" required dir="rtl"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400 resize-none">{{ old("packages.{$loop->index}.description_ar") }}</textarea>
            </div>
            <div>
              <label class="text-xs text-gray-500 mb-1 block">Description (English) *</label>
              <textarea name="packages[{{ $loop->index }}][description]" rows="2" required
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400 resize-none">{{ old("packages.{$loop->index}.description") }}</textarea>
            </div>
            <div>
              <label class="text-xs text-gray-500 mb-1 block">السعر (JOD) *</label>
              <input name="packages[{{ $loop->index }}][price]" type="number" step="0.001" min="1" required
                value="{{ old("packages.{$loop->index}.price") }}"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400">
            </div>
            <div>
              <label class="text-xs text-gray-500 mb-1 block">مدة التسليم (أيام) *</label>
              <input name="packages[{{ $loop->index }}][delivery_days]" type="number" min="1" required
                value="{{ old("packages.{$loop->index}.delivery_days", 3) }}"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400">
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
      ℹ️ ستخضع خدمتك للمراجعة من قبل فريق دوبا وورك قبل نشرها. عادةً خلال 24-48 ساعة.
    </div>

    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3.5 rounded-xl transition-colors text-sm">
      إرسال الخدمة للمراجعة
    </button>
  </form>
</div>

<script>
const catData = @json($categories->keyBy('id'));
function loadSubs(catId) {
  const sub = document.getElementById('subSelect');
  sub.innerHTML = '<option value="">— اختياري —</option>';
  if (!catId || !catData[catId]) return;
  const children = catData[catId].children || [];
  children.forEach(c => {
    const opt = document.createElement('option');
    opt.value = c.id;
    opt.textContent = c.name_ar || c.name;
    sub.appendChild(opt);
  });
}
loadSubs(document.getElementById('catSelect').value);
</script>
@endsection
