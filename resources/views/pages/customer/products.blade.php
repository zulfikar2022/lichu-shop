<?php

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Products')] class extends Component {
    public function addToCart(int $productId): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->isCustomer()) {
            abort(403);
        }

        $product = Product::findOrFail($productId);

        if (! $product->is_available || $product->available_quantity < 1) {
            Flux::toast(variant: 'warning', text: __('This product is not available right now.'));

            return;
        }

        $cart = Cart::firstOrNew([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $cart->quantity = ($cart->exists ? $cart->quantity : 0) + 1;
        $cart->save();

        Flux::toast(variant: 'success', text: __('Added to cart.'));
    }

    public function render()
    {
        return view('pages.customer.products', [
            'products' => Product::with('productImages')->latest()->get(),
        ])->layout('layouts.app', ['title' => __('Products')]);
    }
}; ?>

<section class="space-y-6">
        <div class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm backdrop-blur">
            <flux:heading size="xl">{{ __('Available lichi products') }}</flux:heading>
            <flux:text>{{ __('Browse the products and add them to your cart.') }}</flux:text>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($products as $product)
                <div class="overflow-hidden rounded-3xl border border-white/70 bg-white/85 shadow-sm">
                    <div class="aspect-4/3 bg-linear-to-br from-red-100 to-green-100">
                        @if ($product->productImages->first())
                            <img src="{{ asset('storage/'.$product->productImages->first()->image_link) }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
                        @endif
                    </div>

                    <div class="space-y-3 p-5">
                        <div>
                            <flux:heading size="lg">{{ $product->name }}</flux:heading>
                            <flux:text>{{ $product->category_name }}</flux:text>
                        </div>

                        <p class="text-sm text-zinc-600">{{ $product->description }}</p>

                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-red-700">৳{{ number_format((float) $product->price_per_piece, 2) }}</span>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $product->is_available ? __('Available') : __('Unavailable') }}
                            </span>
                        </div>

                        @if ($product->is_available && $product->available_quantity > 0)
                            <flux:button variant="primary" class="w-full" wire:click="addToCart({{ $product->id }})">
                                {{ __('Add to cart') }}
                            </flux:button>
                        @else
                            <flux:button variant="primary" class="w-full" disabled>
                                {{ __('Unavailable') }}
                            </flux:button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>