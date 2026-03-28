@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'الملف المهني' : 'Professional Profile')

@section('content')
@php $ar = app()->getLocale()==='ar'; @endphp

<div class="max-w-3xl mx-auto px-4 py-8 space-y-6">

  {{-- Header --}}
  <div>
    <h1 class="text-2xl font-bold text-gray-900">{{ $ar ? 'الملف المهني' : 'Professional Profile' }}</h1>
    <p class="text-sm text-gray-500 mt-1">{{ $ar ? 'معلوماتك المهنية التي تظهر للعملاء' : 'Your professional info visible to clients' }}</p>
  </div>

  @if(session('success'))
    <div class="rounded-xl bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
      <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
  @endif

  <form method="POST" action="{{ route('freelancer.profile.update') }}" class="space-y-5">
    @csrf @method('PUT')

    {{-- 1. Availability --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-center justify-between">
        <div>
          <p class="font-semibold text-sm text-gray-900">{{ $ar ? 'متاح للعمل' : 'Available for Work' }}</p>
          <p class="text-xs text-gray-500 mt-0.5">{{ $ar ? 'تظهر شارة "متاح" أمام اسمك للعملاء' : 'Shows an "Available" badge on your public profile' }}</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="hidden" name="is_available" value="0">
          <input type="checkbox" name="is_available" value="1" class="sr-only peer" {{ $profile->is_available ? 'checked' : '' }}>
          <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-primary-600
                      after:content-[''] after:absolute after:top-[2px] after:start-[2px]
                      after:bg-white after:border after:rounded-full after:h-5 after:w-5
                      after:transition-all peer-checked:after:translate-x-full"></div>
        </label>
      </div>
    </div>

    {{-- 2. Professional Title --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
      <h3 class="font-semibold text-gray-900 text-sm">{{ $ar ? 'المسمى المهني' : 'Professional Title' }}</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'بالإنجليزية' : 'English' }}</label>
          <input type="text" name="professional_title" value="{{ old('professional_title', $profile->professional_title) }}"
                 placeholder="e.g. Full-Stack Web Developer"
                 class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('professional_title') border-red-400 @enderror">
          @error('professional_title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'بالعربية' : 'Arabic' }}</label>
          <input type="text" name="professional_title_ar" dir="rtl" value="{{ old('professional_title_ar', $profile->professional_title_ar) }}"
                 placeholder="مثال: مطور مواقع متكامل"
                 class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
      </div>
    </div>

    {{-- 3. Bio --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
      <h3 class="font-semibold text-gray-900 text-sm">{{ $ar ? 'النبذة التعريفية' : 'Bio / Overview' }}</h3>
      <div>
        <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'بالإنجليزية' : 'English' }}</label>
        <textarea name="overview" rows="4" maxlength="3000"
                  placeholder="Tell clients about your skills, experience, and what makes you unique..."
                  class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none @error('overview') border-red-400 @enderror">{{ old('overview', $profile->overview) }}</textarea>
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'بالعربية' : 'Arabic' }}</label>
        <textarea name="overview_ar" rows="4" dir="rtl" maxlength="3000"
                  placeholder="اكتب نبذة تعريفية عن نفسك ومهاراتك وخبراتك..."
                  class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none">{{ old('overview_ar', $profile->overview_ar) }}</textarea>
      </div>
    </div>

    {{-- 4. Skills --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <h3 class="font-semibold text-gray-900 text-sm mb-1">{{ $ar ? 'المهارات' : 'Skills' }}</h3>
      <p class="text-xs text-gray-400 mb-3">{{ $ar ? 'اكتب مهارة واضغط Enter لإضافتها' : 'Type a skill and press Enter to add' }}</p>
      <div id="skills-tags" class="flex flex-wrap gap-2 mb-2 min-h-7"></div>
      <input type="text" id="skills-input" placeholder="{{ $ar ? 'PHP، Laravel، React...' : 'PHP, Laravel, React...' }}"
             class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      <input type="hidden" name="skills" id="skills-hidden"
             value="{{ old('skills', is_array($profile->skills) ? implode(', ', $profile->skills) : '') }}">
    </div>

    {{-- 5. Languages --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <h3 class="font-semibold text-gray-900 text-sm mb-1">{{ $ar ? 'اللغات' : 'Languages' }}</h3>
      <p class="text-xs text-gray-400 mb-3">{{ $ar ? 'اكتب لغة واضغط Enter لإضافتها' : 'Type a language and press Enter to add' }}</p>
      <div id="lang-tags" class="flex flex-wrap gap-2 mb-2 min-h-7"></div>
      <input type="text" id="lang-input" placeholder="{{ $ar ? 'العربية، الإنجليزية...' : 'Arabic, English...' }}"
             class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      <input type="hidden" name="languages" id="lang-hidden"
             value="{{ old('languages', is_array($profile->languages) ? implode(', ', $profile->languages) : '') }}">
    </div>

    {{-- 6. Categories --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <h3 class="font-semibold text-gray-900 text-sm mb-1">{{ $ar ? 'تخصصاتك (حد أقصى 3)' : 'Your Specializations (max 3)' }}</h3>
      <p class="text-xs text-gray-400 mb-4">{{ $ar ? 'اختر التصنيفات التي تعمل بها' : 'Select the categories you work in' }}</p>
      @php $selectedCats = old('category_ids', $profile->category_ids ?? []); @endphp
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2" id="cat-grid">
        @foreach($categories as $cat)
          <label class="flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-colors cat-row
                        {{ in_array($cat->id, $selectedCats) ? 'border-primary-300 bg-primary-50' : 'border-gray-100 hover:border-primary-200 hover:bg-primary-50/40' }}">
            <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}"
                   class="mt-0.5 accent-primary-600 cat-check"
                   {{ in_array($cat->id, $selectedCats) ? 'checked' : '' }}>
            <div>
              <p class="text-sm font-medium text-gray-800 leading-tight">{{ $cat->icon }} {{ $ar ? $cat->name_ar : $cat->name }}</p>
              @if($cat->children->count())
                <p class="text-xs text-gray-400 mt-0.5">{{ $cat->children->map(fn($c) => $ar ? $c->name_ar : $c->name)->take(3)->implode('، ') }}</p>
              @endif
            </div>
          </label>
        @endforeach
      </div>
      @error('category_ids')<p class="text-xs text-red-500 mt-2">{{ $message }}</p>@enderror
    </div>

    {{-- 7. Experience + Hourly Rate --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <h3 class="font-semibold text-gray-900 text-sm mb-4">{{ $ar ? 'الخبرة والسعر' : 'Experience & Rate' }}</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'مستوى الخبرة' : 'Experience Level' }}</label>
          <select name="experience_level" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 bg-white">
            <option value="entry"        {{ old('experience_level', $profile->experience_level) === 'entry'        ? 'selected' : '' }}>{{ $ar ? 'مبتدئ' : 'Entry Level' }}</option>
            <option value="intermediate" {{ old('experience_level', $profile->experience_level) === 'intermediate' ? 'selected' : '' }}>{{ $ar ? 'متوسط' : 'Intermediate' }}</option>
            <option value="expert"       {{ old('experience_level', $profile->experience_level) === 'expert'       ? 'selected' : '' }}>{{ $ar ? 'خبير' : 'Expert' }}</option>
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'السعر بالساعة (دينار أردني)' : 'Hourly Rate (JOD)' }}</label>
          <div class="relative">
            <span class="absolute end-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-medium">JOD</span>
            <input type="number" name="hourly_rate" step="0.001" min="0"
                   value="{{ old('hourly_rate', $profile->hourly_rate) }}"
                   placeholder="0.000"
                   class="w-full rounded-xl border border-gray-200 px-3 py-2.5 pe-14 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('hourly_rate') border-red-400 @enderror">
          </div>
          @error('hourly_rate')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- 8. Education --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <h3 class="font-semibold text-gray-900 text-sm mb-3">{{ $ar ? 'التعليم' : 'Education' }}</h3>
      <textarea name="education" rows="3" maxlength="1000"
                placeholder="{{ $ar ? 'مثال: بكالوريوس علوم حاسوب — جامعة الأردن، 2020' : 'e.g. BSc Computer Science — University of Jordan, 2020' }}"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none">{{ old('education', $profile->education) }}</textarea>
    </div>

    {{-- 9. Certifications --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-gray-900 text-sm">{{ $ar ? 'الشهادات والاعتمادات' : 'Certifications' }}</h3>
        <button type="button" onclick="addCertRow()"
                class="text-xs text-primary-600 font-medium border border-primary-200 rounded-lg px-3 py-1.5 hover:bg-primary-50 transition-colors">
          + {{ $ar ? 'أضف شهادة' : 'Add' }}
        </button>
      </div>
      <div class="text-xs text-gray-400 mb-2">{{ $ar ? 'الاسم — الجهة المانحة — السنة' : 'Name — Issuing Body — Year' }}</div>
      <div id="cert-rows" class="space-y-2">
        @php $certs = $profile->certifications ?? []; @endphp
        @if(count($certs))
          @foreach($certs as $cert)
          <div class="cert-row grid grid-cols-12 gap-2">
            <input type="text" name="cert_name[]" value="{{ $cert['name'] ?? '' }}"
                   placeholder="{{ $ar ? 'اسم الشهادة' : 'Certificate name' }}"
                   class="col-span-5 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <input type="text" name="cert_issuer[]" value="{{ $cert['issuer'] ?? '' }}"
                   placeholder="{{ $ar ? 'الجهة' : 'Issuer' }}"
                   class="col-span-4 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <input type="number" name="cert_year[]" value="{{ $cert['year'] ?? '' }}" min="1990" max="2030"
                   placeholder="{{ date('Y') }}"
                   class="col-span-2 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <button type="button" onclick="this.closest('.cert-row').remove()"
                    class="col-span-1 text-gray-300 hover:text-red-500 text-xl leading-tight pt-1">x</button>
          </div>
          @endforeach
        @else
          <div class="cert-row grid grid-cols-12 gap-2">
            <input type="text" name="cert_name[]" placeholder="{{ $ar ? 'اسم الشهادة' : 'Certificate name' }}"
                   class="col-span-5 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <input type="text" name="cert_issuer[]" placeholder="{{ $ar ? 'الجهة' : 'Issuer' }}"
                   class="col-span-4 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <input type="number" name="cert_year[]" min="1990" max="2030" placeholder="{{ date('Y') }}"
                   class="col-span-2 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <button type="button" onclick="this.closest('.cert-row').remove()"
                    class="col-span-1 text-gray-300 hover:text-red-500 text-xl leading-tight pt-1">x</button>
          </div>
        @endif
      </div>
    </div>

    {{-- 10. Portfolio URL --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
      <h3 class="font-semibold text-gray-900 text-sm mb-3">{{ $ar ? 'رابط الموقع / المحفظة' : 'Website / Portfolio URL' }}</h3>
      <input type="url" name="portfolio_url" value="{{ old('portfolio_url', $profile->portfolio_url) }}"
             placeholder="https://myportfolio.com"
             class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('portfolio_url') border-red-400 @enderror">
      @error('portfolio_url')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Save --}}
    <div class="flex justify-end gap-3">
      <a href="{{ route('freelancer.dashboard') }}"
         class="px-6 py-2.5 text-sm rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors">
        {{ $ar ? 'إلغاء' : 'Cancel' }}
      </a>
      <button type="submit"
              class="px-6 py-2.5 text-sm font-medium rounded-xl bg-primary-600 text-white hover:bg-primary-700 transition-colors">
        {{ $ar ? 'حفظ الملف المهني' : 'Save Profile' }}
      </button>
    </div>
  </form>

  {{-- ══════════════════════════════════ --}}
  {{-- PORTFOLIO GALLERY (separate form) --}}
  {{-- ══════════════════════════════════ --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="font-semibold text-gray-900 text-sm">{{ $ar ? 'معرض الأعمال' : 'Portfolio Gallery' }}</h3>
        <p class="text-xs text-gray-400 mt-0.5">{{ $ar ? 'صور، PDF، أو روابط لأعمالك (حد أقصى 8)' : 'Images, PDFs, or links to your work (max 8)' }}</p>
      </div>
      <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">{{ $portfolioItems->count() }} / 8</span>
    </div>

    @if($portfolioItems->count())
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-5">
        @foreach($portfolioItems as $item)
          <div class="relative group rounded-xl overflow-hidden border border-gray-100 bg-gray-50">
            @if($item->type === 'image')
              <img src="{{ Storage::disk('public')->url($item->file_path) }}" alt="{{ $item->title }}" class="w-full h-28 object-cover">
            @elseif($item->type === 'pdf')
              <div class="w-full h-28 flex flex-col items-center justify-center gap-1">
                <span class="text-3xl">PDF</span>
                <p class="text-xs text-gray-500 px-2 text-center truncate w-full">{{ basename($item->file_path) }}</p>
              </div>
            @else
              <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer"
                 class="w-full h-28 flex flex-col items-center justify-center gap-1 hover:bg-gray-100 transition-colors">
                <span class="text-3xl">LINK</span>
                <p class="text-xs text-gray-500 px-2 text-center truncate w-full">{{ $item->title ?: $item->url }}</p>
              </a>
            @endif
            @if($item->title && $item->type !== 'link')
              <p class="text-xs text-gray-600 font-medium px-2 py-1.5 bg-white border-t border-gray-100 truncate">{{ $item->title }}</p>
            @endif
            <form method="POST" action="{{ route('freelancer.portfolio.delete', $item->id) }}"
                  class="absolute top-1.5 end-1.5 opacity-0 group-hover:opacity-100 transition-opacity"
                  onsubmit="return confirm('{{ $ar ? 'حذف هذا العنصر؟' : 'Delete this item?' }}')">
              @csrf @method('DELETE')
              <button type="submit" class="w-6 h-6 rounded-full bg-red-500 text-white text-xs flex items-center justify-center hover:bg-red-600 shadow">x</button>
            </form>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-8 text-gray-400 mb-4">
        <p class="text-3xl mb-2">IMG</p>
        <p class="text-sm">{{ $ar ? 'لا توجد أعمال بعد' : 'No portfolio items yet' }}</p>
      </div>
    @endif

    @if($portfolioItems->count() < 8)
    <form method="POST" action="{{ route('freelancer.portfolio.add') }}" enctype="multipart/form-data"
          class="border border-dashed border-gray-200 rounded-xl p-4 space-y-3">
      @csrf
      <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $ar ? 'إضافة عنصر جديد' : 'Add New Item' }}</p>
      <div class="flex gap-2">
        @foreach([['image', $ar?'صورة':'Image'], ['pdf','PDF'], ['link', $ar?'رابط':'Link']] as [$val, $label])
        <label class="flex-1 cursor-pointer">
          <input type="radio" name="portfolio_type" value="{{ $val }}" class="sr-only peer portfolio-type-radio" {{ $val==='image'?'checked':'' }}>
          <div class="peer-checked:bg-primary-50 peer-checked:border-primary-400 peer-checked:text-primary-700
                      border border-gray-200 rounded-xl py-2 text-center text-sm font-medium text-gray-500 hover:border-gray-300 transition-colors">
            {{ $label }}
          </div>
        </label>
        @endforeach
      </div>
      <div>
        <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'العنوان (اختياري)' : 'Title (optional)' }}</label>
        <input type="text" name="portfolio_title" maxlength="100"
               placeholder="{{ $ar ? 'مثال: موقع متجر إلكتروني' : 'e.g. E-commerce website' }}"
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      <div id="field-file">
        <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'رفع ملف (صورة أو PDF — حد أقصى 5MB)' : 'Upload file (image or PDF — max 5MB)' }}</label>
        <input type="file" name="portfolio_file" accept="image/*,.pdf"
               class="w-full text-sm text-gray-500 file:me-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
        @error('portfolio_file')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
      </div>
      <div id="field-url" class="hidden">
        <label class="block text-xs text-gray-500 mb-1">{{ $ar ? 'الرابط' : 'URL' }}</label>
        <input type="url" name="portfolio_url_item" placeholder="https://..."
               class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
      </div>
      @error('portfolio')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
      <button type="submit"
              class="w-full py-2.5 text-sm font-medium rounded-xl bg-gray-800 text-white hover:bg-gray-900 transition-colors">
        + {{ $ar ? 'إضافة للمعرض' : 'Add to Gallery' }}
      </button>
    </form>
    @endif
  </div>

</div>

@push('scripts')
<script>
function initTagInput(containerId, inputId, hiddenId) {
  const container = document.getElementById(containerId);
  const input = document.getElementById(inputId);
  const hidden = document.getElementById(hiddenId);
  let tags = hidden.value ? hidden.value.split(',').map(t => t.trim()).filter(Boolean) : [];

  function render() {
    container.innerHTML = '';
    tags.forEach((tag, i) => {
      const span = document.createElement('span');
      span.className = 'inline-flex items-center gap-1 bg-primary-100 text-primary-700 text-xs font-medium px-2.5 py-1 rounded-full';
      span.innerHTML = tag + ' <button type="button" class="hover:text-red-500">&times;</button>';
      span.querySelector('button').addEventListener('click', () => { tags.splice(i, 1); render(); });
      container.appendChild(span);
    });
    hidden.value = tags.join(', ');
  }

  function addTag() {
    const val = input.value.trim().replace(/,$/, '');
    if (val && !tags.includes(val)) { tags.push(val); render(); }
    input.value = '';
  }

  input.addEventListener('keydown', e => { if ([',', 'Enter'].includes(e.key)) { e.preventDefault(); addTag(); } });
  input.addEventListener('blur', addTag);
  render();
}

const isAr = document.documentElement.dir === 'rtl';

function addCertRow() {
  const row = document.createElement('div');
  row.className = 'cert-row grid grid-cols-12 gap-2';
  row.innerHTML =
    '<input type="text" name="cert_name[]" placeholder="' + (isAr ? 'اسم الشهادة' : 'Certificate name') + '" class="col-span-5 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">' +
    '<input type="text" name="cert_issuer[]" placeholder="' + (isAr ? 'الجهة' : 'Issuer') + '" class="col-span-4 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">' +
    '<input type="number" name="cert_year[]" placeholder="' + new Date().getFullYear() + '" min="1990" max="2030" class="col-span-2 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">' +
    '<button type="button" onclick="this.closest(\'.cert-row\').remove()" class="col-span-1 text-gray-300 hover:text-red-500 text-xl leading-tight pt-1">&times;</button>';
  document.getElementById('cert-rows').appendChild(row);
}

document.getElementById('cat-grid').addEventListener('change', function(e) {
  if (!e.target.classList.contains('cat-check')) return;
  const checked = document.querySelectorAll('.cat-check:checked');
  if (checked.length > 3) { e.target.checked = false; return; }
  document.querySelectorAll('.cat-row').forEach(row => {
    const cb = row.querySelector('.cat-check');
    row.classList.toggle('border-primary-300', cb.checked);
    row.classList.toggle('bg-primary-50', cb.checked);
    row.classList.toggle('border-gray-100', !cb.checked);
  });
});

document.querySelectorAll('.portfolio-type-radio').forEach(r => {
  r.addEventListener('change', function() {
    const isLink = this.value === 'link';
    document.getElementById('field-file').classList.toggle('hidden', isLink);
    document.getElementById('field-url').classList.toggle('hidden', !isLink);
  });
});

document.addEventListener('DOMContentLoaded', () => {
  initTagInput('skills-tags', 'skills-input', 'skills-hidden');
  initTagInput('lang-tags', 'lang-input', 'lang-hidden');
});
</script>
@endpush
@endsection