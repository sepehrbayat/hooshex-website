<?php

declare(strict_types=1);

namespace App\Livewire\Commerce;

use App\Domains\Commerce\Services\Cart;
use App\Domains\Courses\Models\Course;
use Livewire\Component;
use Livewire\Attributes\On;

class AddToCartButton extends Component
{
    public $product; // Course model

    public bool $isLoading = false;

    public function mount($product): void
    {
        $this->product = $product instanceof Course ? $product : Course::findOrFail($product);
    }

    public function addToCart(): void
    {
        $this->isLoading = true;

        try {
            $cart = app(Cart::class);
            $cart->addCourse($this->product);

            $this->dispatch('cart-updated');
            $this->dispatch('cart-item-added', productId: $this->product->id);

            session()->flash('cart_message', 'دوره به سبد خرید اضافه شد.');
        } catch (\Exception $e) {
            session()->flash('cart_error', 'خطا در افزودن به سبد خرید.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        $finalPrice = $this->product->sale_price ?? $this->product->price;
        $hasSale = $this->product->sale_price !== null && $this->product->sale_price < $this->product->price;

        return view('livewire.commerce.add-to-cart-button', [
            'finalPrice' => $finalPrice,
            'hasSale' => $hasSale,
        ]);
    }
}
