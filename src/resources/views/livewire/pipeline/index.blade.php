

<div class="space-y-6" x-data="pipelineBoard()">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sales Pipeline</h1>
            <p class="text-sm text-gray-500">{{ $totalCount }} opportunities &middot; Total value: <span class="font-semibold text-blue-600">${{ number_format($totalValue, 2) }}</span></p>
        </div>
        <a href="{{ route('opportunities.create') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm">
            <i class="fas fa-plus"></i> Add Opportunity
        </a>
    </div>

    @if (session('success'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700 flex items-center gap-2">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Kanban Board --}}
    <div class="flex gap-4 overflow-x-auto pb-4 -mx-6 px-6">
        @foreach($stages as $stage)
        @php
        $stageOpportunities = $opportunitiesByStage[$stage] ?? collect();
        $stageValue = $stageOpportunities->sum('value');
        $stageCount = $stageOpportunities->count();
        $config = $pipelineSummary[$stage]['config'] ?? ['label' => ucfirst($stage), 'color' => 'gray'];
        $colorMap = [
            'blue' => 'bg-blue-500', 'indigo' => 'bg-indigo-500', 'yellow' => 'bg-yellow-500',
            'orange' => 'bg-orange-500', 'red' => 'bg-red-500', 'green' => 'bg-green-500', 'gray' => 'bg-gray-400',
        ];
        $bgColorMap = [
            'blue' => 'bg-blue-50', 'indigo' => 'bg-indigo-50', 'yellow' => 'bg-yellow-50',
            'orange' => 'bg-orange-50', 'red' => 'bg-red-50', 'green' => 'bg-green-50', 'gray' => 'bg-gray-50',
        ];
        $textColorMap = [
            'blue' => 'text-blue-700', 'indigo' => 'text-indigo-700', 'yellow' => 'text-yellow-700',
            'orange' => 'text-orange-700', 'red' => 'text-red-700', 'green' => 'text-green-700', 'gray' => 'text-gray-600',
        ];
        @endphp
        <div class="flex-shrink-0 w-80">
            {{-- Stage Header --}}
            <div class="{{ $bgColorMap[$config['color']] ?? 'bg-gray-50' }} rounded-lg p-3 mb-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full {{ $colorMap[$config['color']] ?? 'bg-gray-400' }}"></div>
                        <h3 class="text-sm font-semibold {{ $textColorMap[$config['color']] ?? 'text-gray-700' }}">{{ $config['label'] }}</h3>
                        <span class="inline-flex items-center justify-center w-5 h-5 text-[11px] font-bold {{ $textColorMap[$config['color']] ?? 'text-gray-600' }} bg-white rounded-full shadow-sm">{{ $stageCount }}</span>
                    </div>
                    <p class="text-xs font-semibold {{ $textColorMap[$config['color']] ?? 'text-gray-600' }}">${{ number_format($stageValue, 0) }}</p>
                </div>
            </div>

            {{-- Cards Container --}}
            <div class="space-y-3 min-h-[200px] rounded-lg border-2 border-dashed border-gray-200 p-2 transition-colors"
                 x-on:dragover.prevent="dragOver($event)"
                 x-on:dragleave="dragLeave($event)"
                 x-on:drop.prevent="dropCard($event, '{{ $stage }}')"
                 data-stage="{{ $stage }}">
                @forelse($stageOpportunities as $opp)
                <div class="bg-white rounded-lg border border-gray-200 p-3 shadow-sm hover:shadow-md transition cursor-grab active:cursor-grabbing"
                     draggable="true"
                     x-on:dragstart="dragStart($event, {{ $opp->id }})"
                     x-on:dragend="dragEnd($event)">
                    {{-- Value --}}
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Opportunity</span>
                        <span class="text-sm font-bold text-blue-600">${{ number_format($opp->value, 0) }}</span>
                    </div>
                    {{-- Title --}}
                    <h4 class="text-sm font-medium text-gray-900 mb-1">{{ $opp->name }}</h4>
                    {{-- Company & Contact --}}
                    <div class="space-y-1 mb-2">
                        @if($opp->company_name)
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-building text-gray-400"></i> {{ $opp->company_name }}
                        </p>
                        @endif
                        @if($opp->contact)
                        <p class="text-xs text-gray-500 flex items-center gap-1">
                            <i class="fas fa-user text-gray-400"></i> {{ $opp->contact->first_name }} {{ $opp->contact->last_name }}
                        </p>
                        @endif
                    </div>
                    {{-- Footer --}}
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                        <div class="flex items-center gap-1 text-xs text-gray-400">
                            @if($opp->expected_closing_date)
                            <i class="fas fa-calendar text-[10px]"></i>
                            {{ $opp->expected_closing_date->format('M d') }}
                            @endif
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="text-[10px] font-medium text-gray-500">{{ $opp->probability ?? 0 }}%</span>
                            <div class="w-12 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full" style="width: {{ $opp->probability ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <i class="fas fa-inbox text-2xl text-gray-300 mb-2"></i>
                    <p class="text-xs text-gray-400">No opportunities</p>
                </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pipeline Summary Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Pipeline Summary</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            @foreach($stages as $stage)
            @php
            $config = $pipelineSummary[$stage]['config'];
            $count = $pipelineSummary[$stage]['count'];
            $value = $pipelineSummary[$stage]['value'];
            $bgMap = [
                'blue' => 'bg-blue-50 border-blue-200', 'indigo' => 'bg-indigo-50 border-indigo-200',
                'yellow' => 'bg-yellow-50 border-yellow-200', 'orange' => 'bg-orange-50 border-orange-200',
                'red' => 'bg-red-50 border-red-200', 'green' => 'bg-green-50 border-green-200', 'gray' => 'bg-gray-50 border-gray-200',
            ];
            $textMap = [
                'blue' => 'text-blue-700', 'indigo' => 'text-indigo-700', 'yellow' => 'text-yellow-700',
                'orange' => 'text-orange-700', 'red' => 'text-red-700', 'green' => 'text-green-700', 'gray' => 'text-gray-600',
            ];
            @endphp
            <div class="{{ $bgMap[$config['color']] ?? 'bg-gray-50 border-gray-200' }} border rounded-lg p-3 text-center">
                <p class="text-xs font-medium {{ $textMap[$config['color']] ?? 'text-gray-600' }}">{{ $config['label'] }}</p>
                <p class="text-lg font-bold {{ $textMap[$config['color']] ?? 'text-gray-900' }} mt-1">${{ number_format($value, 0) }}</p>
                <p class="text-[11px] {{ $textMap[$config['color']] ?? 'text-gray-500' }}">{{ $count }} deal{{ $count !== 1 ? 's' : '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
function pipelineBoard() {
    return {
        draggedId: null,
        dragStart(event, id) {
            this.draggedId = id;
            event.dataTransfer.effectAllowed = 'move';
            event.target.style.opacity = '0.5';
        },
        dragEnd(event) {
            this.draggedId = null;
            event.target.style.opacity = '1';
        },
        dragOver(event) {
            event.preventDefault();
            event.currentTarget.classList.add('border-blue-400', 'bg-blue-50/50');
        },
        dragLeave(event) {
            event.currentTarget.classList.remove('border-blue-400', 'bg-blue-50/50');
        },
        dropCard(event, stage) {
            event.currentTarget.classList.remove('border-blue-400', 'bg-blue-50/50');
            if (this.draggedId) {
                this.$wire.moveOpportunity(this.draggedId, stage);
            }
        }
    };
}
</script>
@endpush
