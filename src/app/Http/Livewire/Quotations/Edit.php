<?php

namespace App\Http\Livewire\Quotations;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Opportunity;
use App\Models\Product;
use App\Models\Quotation;

#[Layout('layouts.app', ['title' => 'Edit Quotation'])]
class Edit extends Component
{
    public Quotation $quotation;
    public string $quote_number = '';
    public ?int $customer_id = null;
    public ?int $opportunity_id = null;
    public string $status = 'draft';
    public ?string $valid_until = null;
    public ?string $payment_terms = null;
    public ?string $notes = null;
    public string $tax_rate = '0';
    public string $discount = '0';
    public array $items = [];

    public function mount(Quotation $quotation): void
    {
        $this->quotation = $quotation;
        $this->fill($quotation->only([
            'quote_number', 'customer_id', 'opportunity_id', 'status', 'payment_terms', 'notes',
        ]));
        $this->valid_until = $quotation->valid_until?->format('Y-m-d');
        $this->tax_rate = (string) $quotation->tax_rate;
        $this->discount = (string) $quotation->discount;
        $this->items = $quotation->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'description' => $item->description,
            'quantity' => (float) $item->quantity,
            'unit_price' => (float) $item->unit_price,
            'tax_rate' => (float) $item->tax_rate,
            'discount' => (float) $item->discount,
        ])->values()->all();

        if (empty($this->items)) {
            $this->items = [
                ['product_id' => null, 'description' => '', 'quantity' => 1, 'unit_price' => 0, 'tax_rate' => 0, 'discount' => 0],
            ];
        }
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'opportunity_id' => 'nullable|exists:opportunities,id',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,sent,accepted,rejected,expired',
            'valid_until' => 'required|date',
            'payment_terms' => 'nullable|max:2000',
            'notes' => 'nullable|max:5000',
            'items.*.description' => 'required|string|max:1000',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'description' => '', 'quantity' => 1, 'unit_price' => 0, 'tax_rate' => 0, 'discount' => 0];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function onProductSelect(int $index, mixed $value): void
    {
        $this->items[$index]['product_id'] = $value ? (int) $value : null;

        if (! $value) {
            return;
        }

        $product = Product::find($value);

        if ($product) {
            $this->items[$index]['description'] = $product->name;
            $this->items[$index]['unit_price'] = (float) $product->price;
        }
    }

    public function getSubtotalProperty(): float
    {
        return (float) collect($this->items)->sum(fn ($i) => ($i['quantity'] ?? 0) * ($i['unit_price'] ?? 0) - ($i['discount'] ?? 0));
    }

    public function getTaxAmountProperty(): float
    {
        return (float) collect($this->items)->sum(fn ($i) => (($i['quantity'] ?? 0) * ($i['unit_price'] ?? 0)) * (($i['tax_rate'] ?? 0) / 100));
    }

    public function getTotalProperty(): float
    {
        return $this->subtotal + $this->taxAmount - (float) $this->discount;
    }

    public function save()
    {
        $this->validate();

        $hasItems = collect($this->items)->contains(fn ($item) => trim((string) ($item['description'] ?? '')) !== '');

        $subtotal = $this->subtotal;
        $taxAmount = $this->taxAmount;
        $total = $this->total;

        $this->quotation->update([
            'customer_id' => $this->customer_id,
            'opportunity_id' => $this->opportunity_id,
            'subtotal' => $subtotal,
            'tax_rate' => (float) $this->tax_rate,
            'tax_amount' => $taxAmount,
            'discount' => (float) $this->discount,
            'total' => $total,
            'status' => $this->status,
            'valid_until' => $this->valid_until,
            'payment_terms' => $this->payment_terms,
            'notes' => $this->notes,
        ]);

        if ($hasItems) {
            $this->quotation->items()->delete();

            foreach ($this->items as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $taxRate = (float) ($item['tax_rate'] ?? 0);
                $discount = (float) ($item['discount'] ?? 0);

                $this->quotation->items()->create([
                    'product_id' => $item['product_id'] ?: null,
                    'description' => $item['description'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'tax_rate' => $taxRate,
                    'discount' => $discount,
                    'total' => ($quantity * $unitPrice) + (($quantity * $unitPrice) * $taxRate / 100) - $discount,
                ]);
            }
        }

        session()->flash('success', 'Quotation updated successfully.');
        return redirect()->route('quotations.index');
    }

    public function cancel()
    {
        return redirect()->route('quotations.index');
    }

    public function getProductsProperty()
    {
        return Product::orderBy('name')->get();
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function getOpportunitiesProperty()
    {
        return Opportunity::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.quotations.edit');
    }
}
