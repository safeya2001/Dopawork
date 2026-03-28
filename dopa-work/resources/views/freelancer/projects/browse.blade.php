@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'تصفح المشاريع' : 'Browse Projects')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ app()->getLocale()==='ar' ? 'المشاريع المتاحة' : 'Available Projects' }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale()==='ar' ? 'اعثر على المشروع المناسب وقدّم عرضك' : 'Find the right project and submit your proposal' }}</p>
    </div>

    {{-- Filters --}}
    <form method="GET" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'بحث' : 'Search' }}</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ app()->getLocale()==='ar' ? 'كلمة مفتاحية...' : 'Keyword...' }}"
                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400">
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'التصنيف' : 'Category' }}</label>
            <select name="category" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400 bg-white">
                <option value="">{{ app()->getLocale()==='ar' ? 'الكل' : 'All' }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->display_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">{{ app()->getLocale()==='ar' ? 'نوع الأجر' : 'Budget Type' }}</label>
            <select name="budget_type" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-primary-400 bg-white">
                <option value="">{{ app()->getLocale()==='ar' ? 'الكل' : 'All' }}</option>
                <option value="fixed" {{ request('budget_type')==='fixed'?'selected':'' }}>{{ app()->getLocale()==='ar' ? 'سعر ثابت' : 'Fixed Price' }}</option>
                <option value="hourly" {{ request('budget_type')==='hourly'?'selected':'' }}>{{ app()->getLocale()==='ar' ? 'بالساعة' : 'Hourly' }}</option>
            </select>
        </div>
        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium px-5 py-2 rounded-xl transition-colors">
            🔍 {{ app()->getLocale()==='ar' ? 'بحث' : 'Search' }}
        </button>
        @if(request()->hasAny(['q','category','budget_type','location']))
            <a href="{{ route('freelancer.projects.browse') }}" class="text-sm text-gray-400 hover:text-gray-600 py-2">✕ {{ app()->getLocale()==='ar' ? 'مسح' : 'Clear' }}</a>
        @endif
    </form>

    {{-- Projects Grid --}}
    @forelse($projects as $project)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-4 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        @if($project->category)
                            <span class="text-xs bg-primary-50 text-primary-700 px-2.5 py-1 rounded-full">{{ $project->category->display_name }}</span>
                        @endif
                        <span class="text-xs {{ $project->budget_type === 'fixed' ? 'bg-green-50 text-green-700' : 'bg-purple-50 text-purple-700' }} px-2.5 py-1 rounded-full">
                            {{ $project->budget_type === 'fixed' ? (app()->getLocale()==='ar' ? 'سعر ثابت' : 'Fixed') : (app()->getLocale()==='ar' ? 'بالساعة' : 'Hourly') }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $project->created_at->diffForHumans() }}</span>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900">{{ $project->title }}</h2>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ Str::limit($project->description, 150) }}</p>

                    @if($project->required_skills && count($project->required_skills) > 0)
                        <div class="flex flex-wrap gap-1.5 mt-2">
                            @foreach(array_slice($project->required_skills, 0, 5) as $skill)
                                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md">{{ $skill }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-center gap-4 mt-3 text-xs text-gray-500 flex-wrap">
                        <span>💰 {{ $project->budget_range }}</span>
                        <span>📋 {{ $project->proposals_count }} {{ app()->getLocale()==='ar' ? 'عرض' : 'proposals' }}</span>
                        @if($project->deadline)
                            <span>📅 {{ $project->deadline->format('d/m/Y') }}</span>
                        @endif
                        @if($project->preferred_location)
                            <span>📍 {{ $project->preferred_location }}</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('freelancer.projects.show', $project) }}"
                    class="shrink-0 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold px-4 py-2 rounded-xl transition-colors">
                    {{ app()->getLocale()==='ar' ? 'تقديم عرض' : 'Apply' }}
                </a>
            </div>
        </div>
    @empty
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <div class="text-5xl mb-4">🔍</div>
            <p class="text-gray-500">{{ app()->getLocale()==='ar' ? 'لا توجد مشاريع مفتوحة حالياً' : 'No open projects at the moment' }}</p>
        </div>
    @endforelse

    <div class="mt-4">{{ $projects->links() }}</div>
</div>
@endsection
