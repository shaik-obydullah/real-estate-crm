<?php

namespace App\Http\Livewire\Payments;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;

#[Layout('layouts.app', ['title' => 'Edit Payment'])]
class Edit extends Component
{
    public Payment $payment;

    public ?int $invoice_id = null;
    public ?int $customer_id = null;
    public ?float $amount = null;
    public string $method = 'cash';
    public ?string $reference_number = null;
    public ?string $payment_date = null;
    public ?string $notes = null;
    public string $status = 'pending';

    public function mount(Payment $payment)
    {
        $this->payment = $payment;
        $this->invoice_id = $payment->invoice_id;
        $this->customer_id = $payment->customer_id;
        $this->amount = (float) $payment->amount;
        $this->method = $payment->method;
        $this->reference_number = $payment->reference_number;
        $this->payment_date = $payment->payment_date?->format('Y-m-d');
        $this->notes = $payment->notes;
        $this->status = $payment->status;
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

        $this->payment->update($this->only([
            'invoice_id', 'customer_id', 'amount', 'method',
            'reference_number', 'payment_date', 'notes', 'status',
        ]));

        session()->flash('success', 'Payment updated successfully.');
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
        return view('livewire.payments.edit');
    }
}
