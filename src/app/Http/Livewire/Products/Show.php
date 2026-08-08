<?php

namespace App\Http\Livewire\Products;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;

#[Layout('layouts.app', ['title' => 'View Product'])]
class Show extends Component
{
    public Product $product;

    public function mount(Product $product): void
    {
        $this->product = $product;
    }

    public function render()
    {
        return view('livewire.products.show');
    }
}
