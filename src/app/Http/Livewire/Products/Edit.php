<?php

namespace App\Http\Livewire\Products;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;

#[Layout('layouts.app', ['title' => 'Edit Product'])]
class Edit extends Component
{
    public ?Product $product = null;
    public string $name = '';
    public string $sku = '';
    public ?string $description = null;
    public ?float $price = null;
    public ?float $cost = null;
    public ?string $category = null;
    public int $stock = 0;
    public string $status = 'active';

    public function mount(Product $product): void
    {
        $this->product = $product;
        $this->fill($product->only([
            'name', 'sku', 'description', 'price', 'cost', 'category',
        ]));
        $this->stock = $product->stock ?? 0;
        $this->status = $product->status ?? 'active';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $this->product->id,
            'description' => 'nullable|max:5000',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'category' => 'nullable|max:255',
            'stock' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->product->update($this->only([
            'name', 'sku', 'description', 'price', 'cost', 'category', 'stock', 'status',
        ]));

        session()->flash('success', 'Product updated successfully.');
        return redirect()->route('products.index');
    }

    public function cancel()
    {
        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.products.edit');
    }
}
