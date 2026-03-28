@extends('layouts.app')
@section('title', app()->getLocale() === 'ar' ? 'التحقق من الهوية' : 'Identity Verification')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    {{-- Status Banner --}}
    @if($existing)
        @if($existing->status === 'pending')
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 flex items-center gap-3 mb-6">
                <span class="text-2xl">⏳</span>
                <div>
                    <p class="font-semibold text-yellow-800">{{ app()->getLocale() === 'ar' ? 'وثائقك قيد المراجعة' : 'Documents Under Review' }}</p>
                    <p class="text-sm text-yellow-700">{{ app()->getLocale() === 'ar' ? 'سيتم مراجعة وثائقك خلال 24-48 ساعة' : 'Your documents will be reviewed within 24-48 hours' }}</p>
                </div>
            </div>
        @elseif($existing->status === 'approved')
            <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3 mb-6">
                <span class="text-2xl">✅</span>
                <div>
                    <p class="font-semibold text-green-800">{{ app()->getLocale() === 'ar' ? 'تم التحقق من هويتك' : 'Identity Verified' }}</p>
                    <p class="text-sm text-green-700">{{ app()->getLocale() === 'ar' ? 'حسابك تم التحقق منه بنجاح' : 'Your account has been successfully verified' }}</p>
                </div>
            </div>
        @elseif($existing->status === 'rejected')
            <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">❌</span>
                    <div>
                        <p class="font-semibold text-red-800">{{ app()->getLocale() === 'ar' ? 'تم رفض طلبك' : 'Verification Rejected' }}</p>
                        <p class="text-sm text-red-700">
                            {{ app()->getLocale() === 'ar' ? $existing->rejection_reason_ar : $existing->rejection_reason }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <div class="text-center mb-8">
            <div class="text-5xl mb-4">🪪</div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                {{ app()->getLocale() === 'ar' ? 'التحقق من الهوية' : 'Identity Verification' }}
            </h1>
            <p class="text-gray-500 text-sm max-w-md mx-auto">
                {{ app()->getLocale() === 'ar'
                    ? 'للحفاظ على أمان المنصة وامتثالاً للتشريعات الأردنية، يجب التحقق من هويتك قبل بدء العمل.'
                    : 'To maintain platform security and comply with Jordanian regulations, identity verification is required before you can start working.' }}
            </p>
        </div>

        <form method="POST" action="{{ route('verification.submit') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Document Type --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    {{ app()->getLocale() === 'ar' ? 'نوع الوثيقة' : 'Document Type' }}
                </label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach([
                        ['value' => 'national_id', 'icon' => '🪪', 'en' => 'National ID', 'ar' => 'الهوية الوطنية'],
                        ['value' => 'freelancer_permit', 'icon' => '📋', 'en' => 'Freelancer Permit', 'ar' => 'رخصة مستقل'],
                        ['value' => 'passport', 'icon' => '📘', 'en' => 'Passport', 'ar' => 'جواز سفر'],
                        ['value' => 'residency_permit', 'icon' => '📄', 'en' => 'Residency Permit', 'ar' => 'تصريح إقامة'],
                    ] as $docType)
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-primary-300 hover:bg-primary-50 transition-all has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50">
                            <input type="radio" name="document_type" value="{{ $docType['value'] }}"
                                {{ old('document_type', $existing?->document_type) === $docType['value'] ? 'checked' : '' }}
                                class="text-primary-600">
                            <span class="text-lg">{{ $docType['icon'] }}</span>
                            <span class="text-sm font-medium text-gray-800">
                                {{ app()->getLocale() === 'ar' ? $docType['ar'] : $docType['en'] }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Document Number --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    {{ app()->getLocale() === 'ar' ? 'رقم الوثيقة' : 'Document Number' }}
                </label>
                <input type="text" name="document_number" value="{{ old('document_number') }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100"
                    placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل رقم الوثيقة' : 'Enter document number' }}">
            </div>

            {{-- Upload Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'الوجه الأمامي *' : 'Front Side *' }}
                    </label>
                    <div id="frontBox" class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-primary-300 transition-colors cursor-pointer" onclick="document.getElementById('frontInput').click()">
                        <input type="file" name="front_image" accept=".jpg,.jpeg,.png,.pdf" required class="hidden" id="frontInput">
                        <div id="frontPreview" class="hidden mb-3">
                            <img id="frontImg" src="" alt="" class="max-h-32 mx-auto rounded-lg object-contain">
                        </div>
                        <span id="frontIcon" class="text-3xl block mb-2">📷</span>
                        <span id="frontName" class="text-sm font-medium text-primary-600 hidden block mb-1 truncate px-2"></span>
                        <span id="frontHint" class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'اضغط لرفع الصورة' : 'Click to upload' }}</span>
                        <span class="text-xs text-gray-400 block mt-1">JPG, PNG, PDF — {{ app()->getLocale() === 'ar' ? 'حد أقصى 10MB' : 'Max 10MB' }}</span>
                    </div>
                    @error('front_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'الوجه الخلفي' : 'Back Side' }}
                    </label>
                    <div id="backBox" class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-primary-300 transition-colors cursor-pointer" onclick="document.getElementById('backInput').click()">
                        <input type="file" name="back_image" accept=".jpg,.jpeg,.png,.pdf" class="hidden" id="backInput">
                        <div id="backPreview" class="hidden mb-3">
                            <img id="backImg" src="" alt="" class="max-h-32 mx-auto rounded-lg object-contain">
                        </div>
                        <span id="backIcon" class="text-3xl block mb-2">📷</span>
                        <span id="backName" class="text-sm font-medium text-primary-600 hidden block mb-1 truncate px-2"></span>
                        <span id="backHint" class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'اضغط لرفع الصورة' : 'Click to upload' }}</span>
                        <span class="text-xs text-gray-400 block mt-1">{{ app()->getLocale() === 'ar' ? 'اختياري' : 'Optional' }}</span>
                    </div>
                    @error('back_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    {{ app()->getLocale() === 'ar' ? 'صورة سيلفي (اختياري)' : 'Selfie Photo (Optional)' }}
                </label>
                <div id="selfieBox" class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-primary-300 transition-colors cursor-pointer" onclick="document.getElementById('selfieInput').click()">
                    <input type="file" name="selfie_image" accept=".jpg,.jpeg,.png" class="hidden" id="selfieInput">
                    <div id="selfiePreview" class="hidden mb-2">
                        <img id="selfieImg" src="" alt="" class="max-h-24 mx-auto rounded-full object-cover w-24 h-24">
                    </div>
                    <span id="selfieIcon" class="text-3xl block mb-1">🤳</span>
                    <span id="selfieName" class="text-sm font-medium text-primary-600 hidden block mb-1 truncate px-2"></span>
                    <span id="selfieHint" class="text-sm text-gray-500">{{ app()->getLocale() === 'ar' ? 'اضغط لرفع صورة سيلفي' : 'Click to upload selfie' }}</span>
                </div>
                @error('selfie_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    {{ app()->getLocale() === 'ar' ? 'تاريخ انتهاء الوثيقة' : 'Document Expiry Date' }}
                </label>
                <input type="date" name="document_expiry"
                    value="{{ old('document_expiry') }}"
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
            </div>

            {{-- Security Notice --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
                <span class="text-xl">🔒</span>
                <p class="text-sm text-blue-700">
                    {{ app()->getLocale() === 'ar'
                        ? 'وثائقك محمية ومشفرة. لن تُشارك مع أي طرف ثالث وتستخدم فقط للتحقق من هويتك.'
                        : 'Your documents are encrypted and protected. They will never be shared with third parties and are used solely for identity verification.' }}
                </p>
            </div>

            <button type="submit"
                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3.5 rounded-xl transition-colors">
                {{ app()->getLocale() === 'ar' ? '✅ إرسال للمراجعة' : '✅ Submit for Review' }}
            </button>
        </form>
    </div>
</div>

<script>
function handleFileInput(inputId, iconId, nameId, hintId, previewContainerId, imgId, isCircle) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    const name  = document.getElementById(nameId);
    const hint  = document.getElementById(hintId);
    const previewContainer = document.getElementById(previewContainerId);
    const img   = imgId ? document.getElementById(imgId) : null;

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        // Show filename
        name.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
        name.classList.remove('hidden');
        hint.textContent = '{{ app()->getLocale() === "ar" ? "تم الاختيار — اضغط للتغيير" : "Selected — click to change" }}';
        icon.textContent = '✅';

        // Show image preview
        if (img && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target.result;
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            icon.textContent = '📄';
            if (previewContainer) previewContainer.classList.add('hidden');
        }
    });
}

handleFileInput('frontInput',  'frontIcon',  'frontName',  'frontHint',  'frontPreview',  'frontImg',  false);
handleFileInput('backInput',   'backIcon',   'backName',   'backHint',   'backPreview',   'backImg',   false);
handleFileInput('selfieInput', 'selfieIcon', 'selfieName', 'selfieHint', 'selfiePreview', 'selfieImg', true);
</script>
@endsection
