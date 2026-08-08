<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Task;
use App\Models\Activity;

#[Layout('layouts.app', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    public int $totalCustomers = 0;
    public int $activeLeads = 0;
    public float $pipelineValue = 0;
    public int $pendingTasks = 0;
    public int $totalOpportunities = 0;
    public int $totalInvoices = 0;

    public function mount(): void
    {
        $this->totalCustomers = Customer::count();
        $this->activeLeads = Lead::where('status', '!=', 'converted')->count();
        $this->pipelineValue = (float) Opportunity::whereNotIn('stage', ['won', 'lost'])->sum('value');
        $this->pendingTasks = Task::where('status', '!=', 'completed')->count();
        $this->totalOpportunities = Opportunity::count();
    }

    public function render()
    {
        return view('livewire.dashboard.index', [
            'recentActivities' => Activity::with(['customer', 'lead'])
                ->latest('date')
                ->limit(10)
                ->get(),
            'recentLeads' => Lead::latest()->limit(5)->get(),
            'recentCustomers' => Customer::latest()->limit(5)->get(),
        ]);
    }
}
