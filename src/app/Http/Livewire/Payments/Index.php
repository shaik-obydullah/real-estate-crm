<?php

namespace App\Http\Livewire\Payments;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Payment;

#[Layout('layouts.app', ['title' => 'Payments'])]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $methodFilter = '';
    public string $statusFilter = '';
    public string $sortBy = 'payment_date';
    public string $sortDirection = 'desc';
    public ?int $deleteId = null;

    protected string $paginationTheme = 'tailwind';

    protected $listeners = ['' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingMethodFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function deletePayment(): void
    {
        if ($this->deleteId) {
            Payment::findOrFail($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('paymentDeleted');
            session()->flash('success', 'Payment deleted successfully.');
        }
    }

    public function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'completed' => 'bg-green-100 text-green-700',
            'failed' => 'bg-red-100 text-red-700',
            'refunded' => 'bg-gray-100 text-gray-500',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function getMethodIcon(string $method): string
    {
        return match($method) {
            'credit_card' => 'fas fa-credit-card',
            'bank_transfer' => 'fas fa-university',
            'cash' => 'fas fa-money-bill-wave',
            'check' => 'fas fa-check-double',
            'paypal' => 'fab fa-paypal',
            'stripe' => 'fab fa-stripe-s',
            default => 'fas fa-wallet',
        };
    }

    public function getMethodColor(string $method): string
    {
        return match($method) {
            'credit_card' => 'text-blue-600',
            'bank_transfer' => 'text-indigo-600',
            'cash' => 'text-green-600',
            'check' => 'text-purple-600',
            'paypal' => 'text-blue-500',
            'stripe' => 'text-purple-500',
            default => 'text-gray-500',
        };
    }

    public function render()
    {
        $query = Payment::query()->with(['customer', 'invoice'])
            ->when($this->search, fn($q) => $q->where(function ($sub) {
                $sub->where('payment_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', fn($c) => $c->where('name', 'like', '%' . $this->search . '%'));
            }))
            ->when($this->methodFilter, fn($q) => $q->where('method', $this->methodFilter))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy($this->sortBy, $this->sortDirection);

        return view('livewire.payments.index', [
            'payments' => $query->paginate(15),
            'totalPayments' => Payment::count(),
        ]);
    }
}
