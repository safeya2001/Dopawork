@extends('layouts.app')
@section('title', $project->title)
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-5">
        <a href="{{ route('freelancer.projects.browse') }}" class="text-sm text-gray-400 hover:text-primary-600">← {{ app()->getLocale()==='ar' ? 'تصفح المشاريع' : 'Browse Projects' }}</a>
    </div>

    @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800 font-medium">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Project Details --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex flex-wrap gap-2 mb-3">
                    @if($project->category)
                        <span class="text-xs bg-primary-50 text-primary-700 px-2.5 py-1 rounded-full">{{ $project->category->display_name }}</span>
                    @endif
                    <span class="text-xs {{ $project->budget_type === 'fixed' ? 'bg-green-50 text-green-700' : 'bg-purple-50 text-purple-700' }} px-2.5 py-1 rounded-full">
                        {{ $project->budget_type === 'fixed' ? 'سعر ثابت' : 'بالساعة' }}
                    </span>
                </div>
                <h1 class="text-xl font-bold text-gray-900 mb-3">{{ $project->title }}</h1>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $project->description }}</p>

                @if($project->required_skills && count($project->required_skills) > 0)
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 mb-2">{{ app()->getLocale()==='ar' ? 'المهارات المطلوبة' : 'Required Skills' }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($project->required_skills as $skill)
                                <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Client Info --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-500 mb-3">{{ app()->getLocale()==='ar' ? 'العميل' : 'Client' }}</p>
                <div class="flex items-center gap-3">
                    <img src="{{ $project->client->avatar ? Storage::url($project->client->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($project->client->name).'&color=3b82f6&background=dbeafe&size=40' }}"
                        class="w-10 h-10 rounded-full object-cover border border-gray-200">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $project->client->name }}</p>
                        @if($project->client->city)
                            <p class="text-xs text-gray-400">📍 {{ $project->client->city }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar: Budget + Proposal Form --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="text-center mb-4">
                    <p class="text-xs text-gray-400 mb-1">{{ app()->getLocale()==='ar' ? 'الميزانية' : 'Budget' }}</p>
                    <p class="text-2xl font-bold text-primary-600">{{ $project->budget_range }}</p>
                </div>
                <div class="space-y-2 text-xs text-gray-500">
                    @if($project->deadline)
                        <div class="flex justify-between">
                            <span>📅 {{ app()->getLocale()==='ar' ? 'الموعد النهائي' : 'Deadline' }}</span>
                            <span class="font-medium text-gray-700">{{ $project->deadline->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span>📋 {{ app()->getLocale()==='ar' ? 'العروض' : 'Proposals' }}</span>
                        <span class="font-medium text-gray-700">{{ $project->proposals_count }}</span>
                    </div>
                    @if($project->preferred_location)
                        <div class="flex justify-between">
                            <span>📍 {{ app()->getLocale()==='ar' ? 'الموقع المفضل' : 'Preferred Location' }}</span>
                            <span class="font-medium text-gray-700">{{ $project->preferred_location }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Proposal Form or Status --}}
            @if($myProposal)
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 text-center">
                    <div class="text-2xl mb-2">{{ $myProposal->status === 'accepted' ? '🎉' : ($myProposal->status === 'rejected' ? '😔' : '⏳') }}</div>
                    <p class="text-sm font-semibold text-blue-900 mb-1">
                        @if($myProposal->status === 'pending') {{ app()->getLocale()==='ar' ? 'عرضك قيد المراجعة' : 'Proposal Under Review' }}
                        @elseif($myProposal->status === 'accepted') {{ app()->getLocale()==='ar' ? 'تم قبول عرضك!' : 'Proposal Accepted!' }}
                        @elseif($myProposal->status === 'rejected') {{ app()->getLocale()==='ar' ? 'تم رفض عرضك' : 'Proposal Rejected' }}
                        @else {{ app()->getLocale()==='ar' ? 'تم سحب عرضك' : 'Proposal Withdrawn' }}
                        @endif
                    </p>
                    <p class="text-xs text-blue-700">{{ number_format($myProposal->budget, 3) }} JOD · {{ $myProposal->delivery_days }} {{ app()->getLocale()==='ar' ? 'يوم' : 'days' }}</p>
                    @if($myProposal->status === 'pending')
                        <form method="POST" action="{{ route('freelancer.proposals.withdraw', $myProposal) }}" class="mt-3">
                            @csrf
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">{{ app()->getLocale()==='ar' ? 'سحب العرض' : 'Withdraw Proposal' }}</button>
                        </form>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4">{{ app()->getLocale()==='ar' ? 'تقديم عرض' : 'Submit Proposal' }}</h3>
                    <form method="POST" action="{{ route('freelancer.proposals.submit', $project) }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">{{ app()->getLocale()==='ar' ? 'عرض السعر (JOD)' : 'Your Bid (JOD)' }} <span class="text-red-500">*</span></label>
                                <input type="number" name="budget" step="0.001" min="1" required value="{{ old('budget') }}"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400 @error('budget') border-red-400 @enderror"
                                    placeholder="0.000">
                                @error('budget')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">{{ app()->getLocale()==='ar' ? 'مدة التسليم (أيام)' : 'Delivery Days' }} <span class="text-red-500">*</span></label>
                                <input type="number" name="delivery_days" min="1" max="365" required value="{{ old('delivery_days') }}"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400 @error('delivery_days') border-red-400 @enderror"
                                    placeholder="7">
                                @error('delivery_days')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">{{ app()->getLocale()==='ar' ? 'رسالة التقديم' : 'Cover Letter' }} <span class="text-red-500">*</span></label>
                                <textarea name="cover_letter" rows="5" required minlength="50" maxlength="2000"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400 resize-none @error('cover_letter') border-red-400 @enderror"
                                    placeholder="{{ app()->getLocale()==='ar' ? 'اشرح لماذا أنت مناسب لهذا المشروع...' : 'Explain why you are the right fit...' }}">{{ old('cover_letter') }}</textarea>
                                @error('cover_letter')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 rounded-xl transition-colors text-sm">
                                🚀 {{ app()->getLocale()==='ar' ? 'إرسال العرض' : 'Submit Proposal' }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
