<?php

namespace App\Http\Livewire\Pipeline;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Opportunity;

#[Layout('layouts.app', ['title' => 'Pipeline'])]
class Index extends Component
{
    public string $viewMode = 'board';

    protected $listeners = ['opportunityUpdated' => '$refresh'];

    public function moveOpportunity(int $opportunityId, string $newStage): void
    {
        $opp = Opportunity::findOrFail($opportunityId);
        $config = $this->getStageConfig($newStage);
        $opp->update([
            'stage' => $newStage,
            'probability' => $config['probability'] ?? 0,
        ]);
        $this->dispatch('opportunityUpdated');
    }

    public function getStageConfig(string $stage): array
    {
        $configs = [
            'new' => [
                'label' => 'New',
                'color' => 'blue',
                'probability' => 10,
                'icon' => 'fas fa-plus-circle',
            ],
            'qualified' => [
                'label' => 'Qualified',
                'color' => 'indigo',
                'probability' => 25,
                'icon' => 'fas fa-check-circle',
            ],
            'meeting' => [
                'label' => 'Meeting',
                'color' => 'yellow',
                'probability' => 40,
                'icon' => 'fas fa-calendar-check',
            ],
            'proposal' => [
                'label' => 'Proposal',
                'color' => 'orange',
                'probability' => 60,
                'icon' => 'fas fa-file-alt',
            ],
            'negotiation' => [
                'label' => 'Negotiation',
                'color' => 'red',
                'probability' => 75,
                'icon' => 'fas fa-handshake',
            ],
            'won' => [
                'label' => 'Closed Won',
                'color' => 'green',
                'probability' => 100,
                'icon' => 'fas fa-trophy',
            ],
            'lost' => [
                'label' => 'Closed Lost',
                'color' => 'gray',
                'probability' => 0,
                'icon' => 'fas fa-times-circle',
            ],
        ];

        return $configs[$stage] ?? ['label' => ucfirst($stage), 'color' => 'gray', 'probability' => 0, 'icon' => 'fas fa-circle'];
    }

    public function render()
    {
        $stages = ['new', 'qualified', 'meeting', 'proposal', 'negotiation', 'won', 'lost'];

        $opportunities = Opportunity::with(['contact', 'lead', 'assignedTo'])
            ->get()
            ->groupBy('stage');

        $pipelineSummary = [];
        $totalValue = 0;
        $totalCount = 0;

        foreach ($stages as $stage) {
            $stageOpps = $opportunities[$stage] ?? collect();
            $stageValue = $stageOpps->sum('value');
            $stageCount = $stageOpps->count();
            $totalValue += $stageValue;
            $totalCount += $stageCount;
            $pipelineSummary[$stage] = [
                'count' => $stageCount,
                'value' => $stageValue,
                'config' => $this->getStageConfig($stage),
            ];
        }

        return view('livewire.pipeline.index', [
            'stages' => $stages,
            'opportunitiesByStage' => $opportunities,
            'pipelineSummary' => $pipelineSummary,
            'totalValue' => $totalValue,
            'totalCount' => $totalCount,
        ]);
    }
}
