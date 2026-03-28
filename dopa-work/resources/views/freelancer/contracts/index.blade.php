@extends('layouts.app')
@section('title', app()->getLocale()==='ar' ? 'عقودي النشطة' : 'Active Contracts')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">

  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-900">
        {{ app()->getLocale()==='ar' ? 'عقودي النشطة' : 'Active Contracts' }}
      </h1>
      <p class="text-sm text-gray-500 mt-1">
        {{ app()->getLocale()==='ar' ? 'المشاريع المقبولة ومراحلها' : 'Accepted projects and their milestones' }}
      </p>
    </div>
    <a href="{{ route('freelancer.dashboard') }}"
       class="text-sm text-primary-600 hover:underline">
      ← {{ app()->getLocale()==='ar' ? 'العودة للوحة' : 'Back to Dashboard' }}
    </a>
  </div>

  @if(session('success'))
    <div class="mb-5 rounded-xl bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm">
      {{ session('success') }}
    </div>
  @endif

  @if($contracts->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
      <p class="text-5xl mb-4">📋</p>
      <p class="text-gray-500 text-sm">
        {{ app()->getLocale()==='ar' ? 'لا توجد عقود نشطة حالياً' : 'No active contracts yet' }}
      </p>
      <a href="{{ route('freelancer.projects.browse') }}"
         class="mt-4 inline-block text-primary-600 text-sm hover:underline">
        {{ app()->getLocale()==='ar' ? 'تصفح المشاريع المتاحة' : 'Browse available projects' }}
      </a>
    </div>
  @else
    <div class="space-y-6">
      @foreach($contracts as $contract)
        @php
          $project = $contract->project;
          $milestones = $contract->milestones;
          $totalAmount = $milestones->sum('amount');
          $approvedAmount = $milestones->where('status', 'approved')->sum('amount');
          $progress = $milestones->count() > 0
            ? round($milestones->where('status', 'approved')->count() / $milestones->count() * 100)
            : 0;
        @endphp

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          {{-- Contract Header --}}
          <div class="p-5 border-b border-gray-100">
            <div class="flex items-start justify-between gap-4">
              <div class="flex-1 min-w-0">
                <a href="{{ route('client.projects.show', $project) }}"
                   class="font-bold text-gray-900 hover:text-primary-600 line-clamp-1">
                  {{ $project->title }}
                </a>
                <p class="text-xs text-gray-500 mt-1">
                  {{ app()->getLocale()==='ar' ? 'العميل:' : 'Client:' }}
                  <span class="font-medium text-gray-700">{{ $project->client?->name }}</span>
                  &nbsp;·&nbsp;
                  {{ app()->getLocale()==='ar' ? 'بدأ:' : 'Started:' }}
                  {{ $contract->updated_at->format('d M Y') }}
                </p>
              </div>
              <div class="text-right shrink-0">
                <p class="text-sm font-bold text-gray-900">
                  {{ number_format($contract->budget, 3) }} JOD
                </p>
                <p class="text-xs text-gray-500">
                  {{ $contract->delivery_days }} {{ app()->getLocale()==='ar' ? 'يوم' : 'days' }}
                </p>
              </div>
            </div>

            {{-- Progress Bar --}}
            @if($milestones->count() > 0)
              <div class="mt-4">
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                  <span>{{ app()->getLocale()==='ar' ? 'التقدم' : 'Progress' }}</span>
                  <span>{{ $progress }}%
                    ({{ $milestones->where('status', 'approved')->count() }}/{{ $milestones->count() }}
                    {{ app()->getLocale()==='ar' ? 'مراحل' : 'milestones' }})
                  </span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                  <div class="bg-primary-600 h-2 rounded-full transition-all"
                       style="width: {{ $progress }}%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-1">
                  <span>{{ app()->getLocale()==='ar' ? 'مكتسب:' : 'Earned:' }}
                    <span class="font-medium text-green-600">{{ number_format($approvedAmount, 3) }} JOD</span>
                  </span>
                  <span>{{ app()->getLocale()==='ar' ? 'متبقي:' : 'Remaining:' }}
                    <span class="font-medium">{{ number_format($totalAmount - $approvedAmount, 3) }} JOD</span>
                  </span>
                </div>
              </div>
            @endif
          </div>

          {{-- Milestones --}}
          @if($milestones->isEmpty())
            <div class="p-6 text-center text-sm text-gray-400">
              {{ app()->getLocale()==='ar' ? 'لا توجد مراحل محددة' : 'No milestones defined yet' }}
            </div>
          @else
            <div class="divide-y divide-gray-50">
              @foreach($milestones as $ms)
                @php
                  $statusColors = [
                    'pending'           => 'bg-gray-100 text-gray-600',
                    'in_progress'       => 'bg-blue-100 text-blue-700',
                    'submitted'         => 'bg-yellow-100 text-yellow-700',
                    'approved'          => 'bg-green-100 text-green-700',
                    'revision_requested'=> 'bg-orange-100 text-orange-700',
                  ];
                  $statusLabels = [
                    'pending'            => app()->getLocale()==='ar' ? 'معلق'     : 'Pending',
                    'in_progress'        => app()->getLocale()==='ar' ? 'جارٍ'     : 'In Progress',
                    'submitted'          => app()->getLocale()==='ar' ? 'مُسلَّم'  : 'Submitted',
                    'approved'           => app()->getLocale()==='ar' ? 'مُعتمد'   : 'Approved',
                    'revision_requested' => app()->getLocale()==='ar' ? 'مراجعة'   : 'Revision Needed',
                  ];
                  $canDeliver = in_array($ms->status, ['pending', 'in_progress', 'revision_requested']);
                @endphp

                <div class="px-5 py-4" id="milestone-{{ $ms->id }}">
                  <div class="flex items-start gap-4">
                    {{-- Sort number --}}
                    <div class="w-7 h-7 shrink-0 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                      {{ $ms->sort_order ?? loop()->index + 1 }}
                    </div>

                    <div class="flex-1 min-w-0">
                      <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-semibold text-sm text-gray-900">{{ $ms->title }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $statusColors[$ms->status] ?? 'bg-gray-100 text-gray-600' }}">
                          {{ $statusLabels[$ms->status] ?? $ms->status }}
                        </span>
                      </div>

                      @if($ms->description)
                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $ms->description }}</p>
                      @endif

                      <div class="flex items-center gap-4 mt-1.5 text-xs text-gray-500">
                        <span class="font-bold text-gray-800">{{ number_format($ms->amount, 3) }} JOD</span>
                        @if($ms->due_date)
                          <span>⏰ {{ \Carbon\Carbon::parse($ms->due_date)->format('d M Y') }}</span>
                        @endif
                        @if($ms->delivered_at)
                          <span>📤 {{ \Carbon\Carbon::parse($ms->delivered_at)->format('d M Y') }}</span>
                        @endif
                        @if($ms->approved_at)
                          <span class="text-green-600">✅ {{ \Carbon\Carbon::parse($ms->approved_at)->format('d M Y') }}</span>
                        @endif
                      </div>

                      {{-- Revision note --}}
                      @if($ms->status === 'revision_requested' && $ms->revision_note)
                        <div class="mt-2 rounded-lg bg-orange-50 border border-orange-200 px-3 py-2 text-xs text-orange-800">
                          <p class="font-semibold mb-0.5">{{ app()->getLocale()==='ar' ? 'ملاحظات المراجعة:' : 'Revision Note:' }}</p>
                          {{ $ms->revision_note }}
                        </div>
                      @endif

                      {{-- Deliver form --}}
                      @if($canDeliver)
                        <div class="mt-3">
                          @if(!isset($deliver_open[$ms->id]))
                            <button type="button"
                                    onclick="document.getElementById('deliver-form-{{ $ms->id }}').classList.toggle('hidden')"
                                    class="text-xs font-medium text-white bg-primary-600 hover:bg-primary-700 px-3 py-1.5 rounded-lg transition-colors">
                              📤 {{ app()->getLocale()==='ar' ? 'رفع التسليم' : 'Submit Delivery' }}
                            </button>
                          @endif

                          <div id="deliver-form-{{ $ms->id }}" class="hidden mt-3">
                            <form method="POST" action="{{ route('freelancer.milestones.deliver', $ms) }}">
                              @csrf
                              <textarea name="delivery_note" rows="3" required
                                        placeholder="{{ app()->getLocale()==='ar' ? 'اشرح ما أنجزته في هذه المرحلة...' : 'Describe what you delivered in this milestone...' }}"
                                        class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                              <div class="flex gap-2 mt-2">
                                <button type="submit"
                                        class="text-xs font-medium bg-green-600 text-white px-4 py-1.5 rounded-lg hover:bg-green-700 transition-colors">
                                  ✓ {{ app()->getLocale()==='ar' ? 'تأكيد التسليم' : 'Confirm Delivery' }}
                                </button>
                                <button type="button"
                                        onclick="document.getElementById('deliver-form-{{ $ms->id }}').classList.add('hidden')"
                                        class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1.5">
                                  {{ app()->getLocale()==='ar' ? 'إلغاء' : 'Cancel' }}
                                </button>
                              </div>
                            </form>
                          </div>
                        </div>
                      @endif

                      {{-- Delivery note (submitted / approved) --}}
                      @if(in_array($ms->status, ['submitted', 'approved']) && $ms->delivery_note)
                        <div class="mt-2 rounded-lg bg-blue-50 border border-blue-200 px-3 py-2 text-xs text-blue-800">
                          <p class="font-semibold mb-0.5">{{ app()->getLocale()==='ar' ? 'ملاحظة التسليم:' : 'Delivery Note:' }}</p>
                          {{ $ms->delivery_note }}
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      @endforeach
    </div>

    <div class="mt-6">
      {{ $contracts->links() }}
    </div>
  @endif
</div>
@endsection
