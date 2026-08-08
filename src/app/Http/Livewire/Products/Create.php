<?php

namespace App\Http\Livewire\Products;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;

#[Layout('layouts.app', ['title' => 'New Product'])]
class Create extends Component
{
    public string $name = '';
    public string $sku = '';
    public ?string $description = null;
    public ?float $price = null;
    public ?float $cost = null;
    public ?string $category = null;
    public int $stock = 0;
    public string $status = 'active';

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku',
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

        Product::create($this->only([
            'name', 'sku', 'description', 'price', 'cost', 'category', 'stock', 'status',
        ]));

        session()->flash('success', 'Product created successfully.');
        return redirect()->route('products.index');
    }

    public function cancel()
    {
        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.products.create');
    }
}
