<?php

namespace App\Http\Livewire\Invoices;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\SalesOrder;

#[Layout('layouts.app', ['title' => 'New Invoice'])]
class Create extends Component
{
    public string $invoice_number = '';
    public ?int $customer_id = null;
    public ?int $sales_order_id = null;
    public float $discount = 0;
    public string $status = 'draft';
    public string $due_date = '';
    public ?string $paid_date = null;
    public ?string $notes = null;
    public array $items = [
        ['product_id' => null, 'description' => '', 'quantity' => 1, 'unit_price' => 0, 'tax_rate' => 0],
    ];

    public function mount(): void
    {
        $maxId = Invoice::withTrashed()->max('id') ?? 0;
        $this->invoice_number = 'INV-' . str_pad((string) ($maxId + 1), 4, '0', STR_PAD_LEFT);
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'sales_order_id' => 'nullable|exists:sales_orders,id',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,partial,overdue,cancelled',
            'due_date' => 'required|date',
            'paid_date' => 'nullable|date',
            'notes' => 'nullable|max:5000',
            'items.*.description' => 'required|string|max:1000',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function addItem(): void
    {
        $this->items[] = [
            'product_id' => null,
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'tax_rate' => 0,
        ];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function onProductSelect(int $index): void
    {
        $product = Product::find($this->items[$index]['product_id'] ?? null);

        if (! $product) {
            return;
        }

        $this->items[$index]['description'] = $product->name;
        $this->items[$index]['unit_price'] = (float) $product->price;
    }

    public function getSubtotalProperty(): float
    {
        return (float) collect($this->items)->sum(fn ($i) => ($i['quantity'] ?? 0) * ($i['unit_price'] ?? 0));
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

        $subtotal = $this->subtotal;
        $taxAmount = $this->taxAmount;
        $total = $this->total;

        $invoice = Invoice::create([
            'invoice_number' => $this->invoice_number,
            'customer_id' => $this->customer_id,
            'sales_order_id' => $this->sales_order_id,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount' => $this->discount,
            'total' => $total,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'paid_date' => $this->paid_date,
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ]);

        foreach ($this->items as $item) {
            $q = (float) $item['quantity'];
            $p = (float) $item['unit_price'];
            $t = (float) $item['tax_rate'];

            $invoice->items()->create([
                'product_id' => $item['product_id'],
                'description' => $item['description'],
                'quantity' => $q,
                'unit_price' => $p,
                'tax_rate' => $t,
                'total' => ($q * $p) + (($q * $p) * ($t / 100)),
            ]);
        }

        session()->flash('success', 'Invoice created successfully.');
        return redirect()->route('invoices.index');
    }

    public function cancel()
    {
        return redirect()->route('invoices.index');
    }

    public function getProductsProperty()
    {
        return Product::orderBy('name')->get();
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function getSalesOrdersProperty()
    {
        return SalesOrder::orderBy('order_number')->get();
    }

    public function render()
    {
        return view('livewire.invoices.create');
    }
}
