<?php

use App\Models\Cart;
use App\Models\District;
use App\Models\Division;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderState;
use App\Models\OrderedProduct;
use App\Models\Product;
use App\Models\Upazila;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Cart')] class extends Component {
    public int|string|null $division_id = null;
    public int|string|null $district_id = null;
    public int|string|null $upazila_id = null;
    public string $name = '';
    public string $phone = '';
    public string $alternative_phone = '';
    public string $address = '';

    public function mount(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->isCustomer()) {
            abort(403);
        }
    }

    public function updatedDivisionId(): void
    {
        $this->district_id = null;
        $this->upazila_id = null;
    }

    public function updatedDistrictId(): void
    {
        $this->upazila_id = null;
    }

    public function increment(int $cartId): void
    {
        $cart = Cart::where('user_id', Auth::id())->findOrFail($cartId);
        $cart->increment('quantity');
    }

    public function decrement(int $cartId): void
    {
        $cart = Cart::where('user_id', Auth::id())->findOrFail($cartId);

        if ($cart->quantity <= 1) {
            $cart->delete();

            return;
        }

        $cart->decrement('quantity');
    }

    public function remove(int $cartId): void
    {
        Cart::where('user_id', Auth::id())->findOrFail($cartId)->delete();
    }

    public function placeOrder(): void
    {
        $validated = $this->validate([
            'division_id' => ['required', 'exists:divisions,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'upazila_id' => ['required', 'exists:upazilas,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'alternative_phone' => ['nullable', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $carts = Cart::with('product')->where('user_id', Auth::id())->get();

        if ($carts->isEmpty()) {
            Flux::toast(variant: 'warning', text: __('Your cart is empty.'));

            return;
        }

        DB::transaction(function () use ($validated, $carts): void {
            $pendingState = OrderState::firstOrCreate(['name' => 'pending']);

            foreach ($carts as $cart) {
                $product = $cart->product;

                if (! $product || ! $product->is_available || $product->available_quantity < $cart->quantity) {
                    abort(422, __('One or more items are no longer available in the requested quantity.'));
                }
            }

            $totalPrice = $carts->sum(fn (Cart $cart) => $cart->quantity * (float) $cart->product->price_per_piece);
            $shippingCharge = 0;

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_state_id' => $pendingState->id,
                'total_price' => $totalPrice,
                'total_shipping_charge' => $shippingCharge,
            ]);

            OrderAddress::create([
                'order_id' => $order->id,
                'division_id' => $validated['division_id'],
                'district_id' => $validated['district_id'],
                'upazila_id' => $validated['upazila_id'],
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'alternative_phone' => $validated['alternative_phone'] ?? null,
                'address' => $validated['address'],
            ]);

            foreach ($carts as $cart) {
                $product = Product::findOrFail($cart->product_id);

                OrderedProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'unit_price' => $product->price_per_piece,
                    'quantity' => $cart->quantity,
                    'price' => $product->price_per_piece * $cart->quantity,
                    'shipping_charge' => 0,
                ]);

                $product->decrement('available_quantity', $cart->quantity);

                $cart->delete();
            }
        });

        $this->reset(['division_id', 'district_id', 'upazila_id', 'name', 'phone', 'alternative_phone', 'address']);

        Flux::toast(variant: 'success', text: __('Order placed.'));
    }

    public function render()
    {
        return view('pages.customer.cart', [
            'carts' => Cart::with('product.productImages')->where('user_id', Auth::id())->latest()->get(),
            'divisions' => Division::orderBy('name')->get(),
            'districts' => $this->division_id ? District::where('division_id', $this->division_id)->orderBy('name')->get() : collect(),
            'upazilas' => $this->district_id ? Upazila::where('district_id', $this->district_id)->orderBy('name')->get() : collect(),
        ])->layout('layouts.app', ['title' => __('Cart')]);
    }
}; ?>

<section class="space-y-6">
        <div class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm backdrop-blur">
            <flux:heading size="xl">{{ __('My cart') }}</flux:heading>
            <flux:text>{{ __('Review your selected products and place the order.') }}</flux:text>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-4">
                @forelse ($carts as $cart)
                    <div class="flex items-center gap-4 rounded-3xl border border-white/70 bg-white/85 p-4 shadow-sm">
                        <div class="h-20 w-20 overflow-hidden rounded-2xl bg-red-50">
                            @if ($cart->product->productImages->first())
                                <img src="{{ asset('storage/'.$cart->product->productImages->first()->image_link) }}" alt="{{ $cart->product->name }}" class="h-full w-full object-cover" />
                            @endif
                        </div>

                        <div class="flex-1">
                            <flux:heading size="md">{{ $cart->product->name }}</flux:heading>
                            <flux:text>{{ __('৳') }}{{ number_format((float) $cart->product->price_per_piece, 2) }}</flux:text>
                            <p class="text-sm text-zinc-500">{{ __('Subtotal: ৳') }}{{ number_format((float) $cart->product->price_per_piece * $cart->quantity, 2) }}</p>
                        </div>

                        <div class="flex items-center gap-2">
                            <flux:button size="sm" variant="filled" wire:click="decrement({{ $cart->id }})">-</flux:button>
                            <span class="min-w-8 text-center font-semibold">{{ $cart->quantity }}</span>
                            <flux:button size="sm" variant="filled" wire:click="increment({{ $cart->id }})">+</flux:button>
                            <flux:button size="sm" variant="danger" wire:click="remove({{ $cart->id }})">{{ __('Remove') }}</flux:button>
                        </div>
                    </div>
                @empty
                    <div class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-sm">
                        {{ __('Your cart is empty.') }}
                    </div>
                @endforelse
            </div>

            <div class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-sm">
                <flux:heading size="lg">{{ __('Checkout') }}</flux:heading>
                <div class="mt-4 space-y-4">
                    <flux:input wire:model="name" :label="__('Full name')" type="text" />
                    <flux:input wire:model="phone" :label="__('Phone')" type="text" />
                    <flux:input wire:model="alternative_phone" :label="__('Alternative phone')" type="text" />
                    <flux:textarea wire:model="address" :label="__('Address')" rows="3" />

                    <flux:select wire:model.live="division_id" :label="__('Division')">
                        <option value="">{{ __('Choose division') }}</option>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model.live="district_id" :label="__('District')">
                        <option value="">{{ __('Choose district') }}</option>
                        @foreach ($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model.live="upazila_id" :label="__('Upazila')">
                        <option value="">{{ __('Choose upazila') }}</option>
                        @foreach ($upazilas as $upazila)
                            <option value="{{ $upazila->id }}">{{ $upazila->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:button variant="primary" class="w-full" wire:click="placeOrder">
                        {{ __('Place order') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </section>