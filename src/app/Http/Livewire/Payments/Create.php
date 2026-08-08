<?php

namespace App\Http\Livewire\Payments;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;

#[Layout('layouts.app', ['title' => 'New Payment'])]
class Create extends Component
{
    public string $payment_number = '';
    public ?int $invoice_id = null;
    public ?int $customer_id = null;
    public ?float $amount = null;
    public string $method = 'cash';
    public ?string $reference_number = null;
    public ?string $payment_date = null;
    public ?string $notes = null;
    public string $status = 'pending';

    public function mount()
    {
        $maxId = Payment::max('id') ?? 0;
        $this->payment_number = 'PAY-' . str_pad((string) ($maxId + 1), 4, '0', STR_PAD_LEFT);
    }

    public function rules(): array
    {
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,bank_transfer,credit_card,check,other',
            'reference_number' => 'nullable|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|max:5000',
            'status' => 'required|in:pending,completed,failed,refunded',
        ];
    }

    public function onInvoiceChange(): void
    {
        $invoice = Invoice::with('customer')->find($this->invoice_id);
        if ($invoice) {
            $this->customer_id = $invoice->customer_id;
            $this->amount = (float) $invoice->total - (float) $invoice->paid_amount;
        }
    }

    public function save()
    {
        $this->validate();

        Payment::create([
            'payment_number' => $this->payment_number,
            'invoice_id' => $this->invoice_id,
            'customer_id' => $this->customer_id,
            'amount' => $this->amount,
            'method' => $this->method,
            'reference_number' => $this->reference_number,
            'payment_date' => $this->payment_date,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', 'Payment created successfully.');
        return redirect()->route('payments.index');
    }

    public function cancel()
    {
        return redirect()->route('payments.index');
    }

    public function getInvoicesProperty()
    {
        return Invoice::orderByDesc('id')->get();
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.payments.create');
    }
}
