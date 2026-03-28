@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'مشاريعي' : 'My Projects')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ app()->getLocale()==='ar' ? 'مشاريعي' : 'My Projects' }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale()==='ar' ? 'تتبع مشاريعك المنشورة' : 'Track your posted projects' }}</p>
        </div>
        <a href="{{ route('client.projects.create') }}"
            class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
            + {{ app()->getLocale()==='ar' ? 'نشر مشروع جديد' : 'Post New Project' }}
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800 font-medium">{{ session('success') }}</div>
    @endif

    @forelse($projects as $project)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        @php
                            $statusColors = ['open'=>'bg-green-100 text-green-700','in_progress'=>'bg-blue-100 text-blue-700','completed'=>'bg-gray-100 text-gray-600','cancelled'=>'bg-red-100 text-red-600'];
                            $statusLabels = ['open'=>'مفتوح','in_progress'=>'قيد التنفيذ','completed'=>'مكتمل','cancelled'=>'ملغى'];
                        @endphp
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium {{ $statusColors[$project->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ app()->getLocale()==='ar' ? ($statusLabels[$project->status] ?? $project->status) : $project->status }}
                        </span>
                        @if($project->category)
                            <span class="text-xs text-gray-400">{{ $project->category->display_name }}</span>
                        @endif
                        <span class="text-xs text-gray-400">{{ $project->created_at->diffForHumans() }}</span>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900 truncate">{{ $project->title }}</h2>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ Str::limit($project->description, 120) }}</p>
                    <div class="flex items-center gap-4 mt-3 text-xs text-gray-500 flex-wrap">
                        <span>💰 {{ $project->budget_range }}</span>
                        <span>📋 {{ $project->proposals_count }} {{ app()->getLocale()==='ar' ? 'عرض' : 'proposals' }}</span>
                        @if($project->deadline)
                            <span>📅 {{ $project->deadline->format('d/m/Y') }}</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('client.projects.show', $project) }}"
                    class="shrink-0 text-sm text-primary-600 font-medium hover:underline">
                    {{ app()->getLocale()==='ar' ? 'عرض التفاصيل' : 'View' }} →
                </a>
            </div>
        </div>
    @empty
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <div class="text-5xl mb-4">📋</div>
            <p class="text-gray-500 mb-4">{{ app()->getLocale()==='ar' ? 'لم تنشر أي مشروع بعد' : 'No projects posted yet' }}</p>
            <a href="{{ route('client.projects.create') }}" class="bg-primary-600 text-white text-sm font-semibold px-6 py-2.5 rounded-xl hover:bg-primary-700 transition-colors">
                {{ app()->getLocale()==='ar' ? 'انشر مشروعك الأول' : 'Post Your First Project' }}
            </a>
        </div>
    @endforelse

    <div class="mt-4">{{ $projects->links() }}</div>
</div>
@endsection
