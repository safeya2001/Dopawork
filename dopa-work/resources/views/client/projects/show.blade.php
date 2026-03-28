@extends('layouts.app')
@section('title', $project->title)
@section('content')
@php
    $statusColors = ['open'=>'bg-green-100 text-green-700','in_progress'=>'bg-blue-100 text-blue-700','completed'=>'bg-gray-100 text-gray-600','cancelled'=>'bg-red-100 text-red-600'];
    $statusLabels = ['open'=>'مفتوح','in_progress'=>'قيد التنفيذ','completed'=>'مكتمل','cancelled'=>'ملغى'];
    $milestoneColors = ['pending'=>'bg-gray-100 text-gray-500','in_progress'=>'bg-blue-100 text-blue-700','submitted'=>'bg-yellow-100 text-yellow-700','approved'=>'bg-green-100 text-green-700','revision_requested'=>'bg-orange-100 text-orange-700'];
    $milestoneLabels = ['pending'=>'في الانتظار','in_progress'=>'قيد التنفيذ','submitted'=>'تم التسليم','approved'=>'موافق عليه','revision_requested'=>'طلب تعديل'];
    $acceptedProposal = $project->acceptedProposal;
@endphp
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-5">
        <a href="{{ route('client.projects.index') }}" class="text-sm text-gray-400 hover:text-primary-600">← {{ app()->getLocale()==='ar' ? 'مشاريعي' : 'My Projects' }}</a>
    </div>

    @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800 font-medium">{{ session('success') }}</div>
    @endif

    {{-- Project Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-3 flex-wrap">
                    <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $statusColors[$project->status] }}">
                        {{ $statusLabels[$project->status] }}
                    </span>
                    @if($project->category)
                        <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">{{ $project->category->display_name }}</span>
                    @endif
                    <span class="text-xs text-gray-400">{{ $project->created_at->diffForHumans() }}</span>
                </div>
                <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $project->title }}</h1>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $project->description }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 pt-5 border-t border-gray-100">
            <div>
                <p class="text-xs text-gray-400">{{ app()->getLocale()==='ar' ? 'الميزانية' : 'Budget' }}</p>
                <p class="text-sm font-semibold text-gray-800">{{ $project->budget_range }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">{{ app()->getLocale()==='ar' ? 'نوع الأجر' : 'Type' }}</p>
                <p class="text-sm font-semibold text-gray-800">{{ $project->budget_type === 'fixed' ? 'سعر ثابت' : 'بالساعة' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">{{ app()->getLocale()==='ar' ? 'الموعد النهائي' : 'Deadline' }}</p>
                <p class="text-sm font-semibold text-gray-800">{{ $project->deadline ? $project->deadline->format('d/m/Y') : '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">{{ app()->getLocale()==='ar' ? 'العروض' : 'Proposals' }}</p>
                <p class="text-sm font-semibold text-gray-800">{{ $project->proposals->count() }}</p>
            </div>
        </div>
        @if($project->required_skills && count($project->required_skills) > 0)
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($project->required_skills as $skill)
                    <span class="text-xs bg-primary-50 text-primary-700 border border-primary-100 px-2.5 py-1 rounded-full">{{ $skill }}</span>
                @endforeach
            </div>
        @endif

        @if($project->status === 'open')
            <div class="mt-4 pt-4 border-t border-gray-100">
                <form method="POST" action="{{ route('client.projects.cancel', $project) }}" onsubmit="return confirm('إلغاء المشروع؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700">🗑 {{ app()->getLocale()==='ar' ? 'إلغاء المشروع' : 'Cancel Project' }}</button>
                </form>
            </div>
        @endif
    </div>

    {{-- Milestones (if project in_progress) --}}
    @if($project->status === 'in_progress' && $project->milestones->count() > 0)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">🎯 {{ app()->getLocale()==='ar' ? 'مراحل المشروع' : 'Project Milestones' }}</h2>
            @foreach($project->milestones as $milestone)
                <div class="border border-gray-100 rounded-xl p-4 mb-3">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $milestoneColors[$milestone->status] }}">{{ $milestoneLabels[$milestone->status] }}</span>
                                <span class="text-sm font-semibold text-gray-800">{{ $milestone->title }}</span>
                            </div>
                            @if($milestone->description)
                                <p class="text-xs text-gray-500">{{ $milestone->description }}</p>
                            @endif
                            @if($milestone->delivery_note)
                                <div class="mt-2 bg-blue-50 rounded-lg p-2 text-xs text-blue-800">
                                    💬 {{ $milestone->delivery_note }}
                                </div>
                            @endif
                        </div>
                        <div class="text-start shrink-0">
                            <p class="text-sm font-bold text-primary-600">{{ number_format($milestone->amount, 3) }} JOD</p>
                            @if($milestone->due_date)
                                <p class="text-xs text-gray-400">{{ $milestone->due_date->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Actions for submitted milestones --}}
                    @if($milestone->status === 'submitted')
                        <div class="mt-3 pt-3 border-t border-gray-100 flex gap-3 flex-wrap">
                            <form method="POST" action="{{ route('client.projects.milestones.approve', [$project, $milestone]) }}">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition-colors">
                                    ✓ {{ app()->getLocale()==='ar' ? 'قبول التسليم' : 'Approve' }}
                                </button>
                            </form>
                            <button onclick="document.getElementById('revisionForm{{ $milestone->id }}').classList.toggle('hidden')"
                                class="border border-orange-300 text-orange-600 text-xs font-semibold px-4 py-2 rounded-lg hover:bg-orange-50 transition-colors">
                                ↩ {{ app()->getLocale()==='ar' ? 'طلب تعديل' : 'Request Revision' }}
                            </button>
                            <form id="revisionForm{{ $milestone->id }}" method="POST"
                                action="{{ route('client.projects.milestones.revision', [$project, $milestone]) }}"
                                class="hidden w-full mt-2">
                                @csrf
                                <textarea name="revision_note" rows="2" required placeholder="{{ app()->getLocale()==='ar' ? 'اشرح التعديلات المطلوبة...' : 'Describe the needed revisions...' }}"
                                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400 mb-2"></textarea>
                                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-semibold px-4 py-2 rounded-lg">
                                    {{ app()->getLocale()==='ar' ? 'إرسال طلب التعديل' : 'Send Revision Request' }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Proposals --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">
            💼 {{ app()->getLocale()==='ar' ? 'عروض الفريلانسرين' : 'Freelancer Proposals' }}
            <span class="text-sm font-normal text-gray-400">({{ $project->proposals->count() }})</span>
        </h2>

        @forelse($project->proposals as $proposal)
            @php $fl = $proposal->freelancer; $profile = $fl->freelancerProfile; @endphp
            <div class="border border-gray-100 rounded-xl p-4 mb-3 {{ $proposal->status === 'accepted' ? 'border-green-300 bg-green-50' : '' }}">
                <div class="flex items-start gap-4">
                    <img src="{{ $fl->avatar ? Storage::url($fl->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($fl->name).'&color=3b82f6&background=dbeafe&size=48' }}"
                        class="w-12 h-12 rounded-full object-cover border border-gray-200 shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route('freelancers.show', $fl) }}" class="text-sm font-semibold text-gray-900 hover:text-primary-600">{{ $fl->name }}</a>
                            @if($profile && $profile->rating > 0)
                                <span class="text-xs text-yellow-500">★ {{ number_format($profile->rating, 1) }}</span>
                            @endif
                            @if($proposal->status !== 'pending')
                                @php $pColors = ['accepted'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-600','withdrawn'=>'bg-gray-100 text-gray-500']; $pLabels = ['accepted'=>'مقبول','rejected'=>'مرفوض','withdrawn'=>'مسحوب']; @endphp
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $pColors[$proposal->status] ?? '' }}">{{ $pLabels[$proposal->status] ?? $proposal->status }}</span>
                            @endif
                        </div>
                        @if($profile && $profile->professional_title)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $profile->professional_title }}</p>
                        @endif
                        <p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ Str::limit($proposal->cover_letter, 200) }}</p>
                        <div class="flex gap-4 mt-2 text-xs text-gray-500">
                            <span>💰 <strong class="text-gray-800">{{ number_format($proposal->budget, 3) }} JOD</strong></span>
                            <span>⏱ {{ $proposal->delivery_days }} {{ app()->getLocale()==='ar' ? 'يوم' : 'days' }}</span>
                        </div>
                    </div>
                </div>

                @if($project->status === 'open' && $proposal->status === 'pending')
                    <div class="flex gap-3 mt-3 pt-3 border-t border-gray-100">
                        <form method="POST" action="{{ route('client.projects.proposals.accept', [$project, $proposal]) }}">
                            @csrf
                            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold px-5 py-2 rounded-lg transition-colors">
                                ✓ {{ app()->getLocale()==='ar' ? 'قبول العرض' : 'Accept' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('client.projects.proposals.reject', [$project, $proposal]) }}">
                            @csrf
                            <button type="submit" class="border border-gray-200 text-gray-500 text-xs font-semibold px-5 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                ✕ {{ app()->getLocale()==='ar' ? 'رفض' : 'Reject' }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-10 text-gray-400 text-sm">
                {{ app()->getLocale()==='ar' ? 'لا توجد عروض بعد — سيصلك إشعار عند وصول عروض جديدة' : 'No proposals yet — you\'ll be notified when freelancers apply' }}
            </div>
        @endforelse
    </div>
</div>
@endsection
