<?php

namespace App\Http\Livewire\Reports;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Opportunity;
use App\Models\User;
use App\Models\Ticket;
use Carbon\Carbon;

#[Layout('layouts.app', ['title' => 'Reports'])]
class Index extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        $from = $this->dateFrom ? Carbon::parse($this->dateFrom) : Carbon::now()->subMonth();
        $to = $this->dateTo ? Carbon::parse($this->dateTo) : Carbon::now();

        $data = [
            'total_customers' => Customer::count(),
            'new_customers_period' => Customer::whereBetween('created_at', [$from, $to])->count(),
            'total_leads' => Lead::count(),
            'leads_in_period' => Lead::whereBetween('created_at', [$from, $to])->count(),
            'converted_leads' => Lead::where('status', 'converted')->whereBetween('created_at', [$from, $to])->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'revenue_in_period' => Payment::where('status', 'completed')->whereBetween('payment_date', [$from, $to])->sum('amount'),
            'total_invoices' => Invoice::count(),
            'paid_invoices' => Invoice::where('status', 'paid')->count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'total_opportunities' => Opportunity::count(),
            'won_opportunities' => Opportunity::where('stage', 'won')->count(),
            'open_opportunities' => Opportunity::whereNotIn('stage', ['won', 'lost'])->count(),
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::where('status', 'open')->count(),
            'resolved_tickets' => Ticket::where('status', 'resolved')->count(),
            'total_users' => User::count(),
            'conversion_rate' => 0,
            'pipeline_value' => Opportunity::whereNotIn('stage', ['won', 'lost'])->sum('value'),
        ];

        if ($data['total_leads'] > 0) {
            $data['conversion_rate'] = round(($data['converted_leads'] / max($data['leads_in_period'], 1)) * 100, 1);
        }

        return view('livewire.reports.index', ['data' => $data]);
    }
}
