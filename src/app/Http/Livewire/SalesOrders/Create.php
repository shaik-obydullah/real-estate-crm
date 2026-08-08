<?php

namespace App\Http\Livewire\SalesOrders;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;

#[Layout('layouts.app', ['title' => 'New Sales Order'])]
class Create extends Component
{
    public string $order_number = '';
    public ?int $customer_id = null;
    public ?int $quotation_id = null;
    public float $discount = 0;
    public string $status = 'pending';
    public ?string $delivery_date = null;
    public ?string $shipping_address = null;
    public ?string $notes = null;
    public array $items = [['product_id' => null, 'description' => '', 'quantity' => 1, 'unit_price' => 0]];

    public function mount(): void
    {
        $maxId = SalesOrder::withTrashed()->max('id') ?? 0;
        $this->order_number = 'SO-' . str_pad((string) ($maxId + 1), 4, '0', STR_PAD_LEFT);
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'delivery_date' => 'nullable|date',
            'shipping_address' => 'nullable|max:2000',
            'notes' => 'nullable|max:5000',
            'items.*.description' => 'required|string|max:1000',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => null, 'description' => '', 'quantity' => 1, 'unit_price' => 0];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function onProductSelect(int $index): void
    {
        $product = Product::find($this->items[$index]['product_id'] ?? null);

        if ($product) {
            $this->items[$index]['description'] = $product->name;
            $this->items[$index]['unit_price'] = (float) $product->price;
        }
    }

    public function getSubtotalProperty(): float
    {
        return (float) collect($this->items)->sum(fn ($i) => ($i['quantity'] ?? 0) * ($i['unit_price'] ?? 0));
    }

    public function getTotalProperty(): float
    {
        return $this->subtotal - (float) $this->discount;
    }

    public function save()
    {
        $this->validate();

        $subtotal = $this->subtotal;
        $total = $this->total;

        $salesOrder = SalesOrder::create([
            'order_number' => $this->order_number,
            'customer_id' => $this->customer_id,
            'quotation_id' => $this->quotation_id,
            'subtotal' => $subtotal,
            'tax_amount' => 0,
            'discount' => (float) $this->discount,
            'total' => $total,
            'status' => $this->status,
            'delivery_date' => $this->delivery_date,
            'shipping_address' => $this->shipping_address,
            'notes' => $this->notes,
            'created_by' => auth()->id(),
        ]);

        foreach ($this->items as $item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['unit_price'];

            SalesOrderItem::create([
                'sales_order_id' => $salesOrder->id,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $qty,
                'unit_price' => $price,
                'total' => $qty * $price,
            ]);
        }

        session()->flash('success', 'Sales order created successfully.');
        return redirect()->route('sales-orders.index');
    }

    public function cancel()
    {
        return redirect()->route('sales-orders.index');
    }

    public function getProductsProperty()
    {
        return Product::orderBy('name')->get();
    }

    public function getCustomersProperty()
    {
        return Customer::orderBy('name')->get();
    }

    public function getQuotationsProperty()
    {
        return Quotation::orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.sales-orders.create');
    }
}
