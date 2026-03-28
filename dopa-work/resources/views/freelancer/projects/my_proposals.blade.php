@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'عروضي' : 'My Proposals')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ app()->getLocale()==='ar' ? 'عروضي' : 'My Proposals' }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale()==='ar' ? 'تتبع حالة عروضك المقدمة' : 'Track your submitted proposals' }}</p>
        </div>
        <a href="{{ route('freelancer.projects.browse') }}" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
            🔍 {{ app()->getLocale()==='ar' ? 'تصفح المشاريع' : 'Browse Projects' }}
        </a>
    </div>

    @forelse($proposals as $proposal)
        @php
            $project = $proposal->project;
            $pColors = ['pending'=>'bg-yellow-100 text-yellow-700','accepted'=>'bg-green-100 text-green-700','rejected'=>'bg-red-100 text-red-600','withdrawn'=>'bg-gray-100 text-gray-500'];
            $pLabels = ['pending'=>'قيد المراجعة','accepted'=>'مقبول','rejected'=>'مرفوض','withdrawn'=>'مسحوب'];
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4">
            <div class="flex items-start gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $pColors[$proposal->status] }}">{{ $pLabels[$proposal->status] }}</span>
                        @if($project->category)
                            <span class="text-xs text-gray-400">{{ $project->category->display_name }}</span>
                        @endif
                        <span class="text-xs text-gray-400">{{ $proposal->created_at->diffForHumans() }}</span>
                    </div>
                    <a href="{{ route('freelancer.projects.show', $project) }}" class="text-base font-semibold text-gray-900 hover:text-primary-600">
                        {{ $project->title }}
                    </a>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit($proposal->cover_letter, 150) }}</p>
                    <div class="flex gap-4 mt-2 text-xs text-gray-500">
                        <span>💰 {{ number_format($proposal->budget, 3) }} JOD</span>
                        <span>⏱ {{ $proposal->delivery_days }} {{ app()->getLocale()==='ar' ? 'يوم' : 'days' }}</span>
                        <span>👤 {{ $project->client->name }}</span>
                    </div>
                </div>
                @if($proposal->status === 'pending')
                    <form method="POST" action="{{ route('freelancer.proposals.withdraw', $proposal) }}">
                        @csrf
                        <button type="submit" class="text-xs text-red-400 hover:text-red-600 border border-red-200 px-3 py-1.5 rounded-lg">
                            {{ app()->getLocale()==='ar' ? 'سحب' : 'Withdraw' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <div class="text-5xl mb-4">📋</div>
            <p class="text-gray-500 mb-4">{{ app()->getLocale()==='ar' ? 'لم تقدم أي عروض بعد' : 'No proposals submitted yet' }}</p>
            <a href="{{ route('freelancer.projects.browse') }}" class="bg-primary-600 text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-primary-700">
                {{ app()->getLocale()==='ar' ? 'تصفح المشاريع' : 'Browse Projects' }}
            </a>
        </div>
    @endforelse

    <div class="mt-4">{{ $proposals->links() }}</div>
</div>
@endsection
