<?php

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Orders')] class extends Component {
    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user || ! $user->isCustomer()) {
            abort(403);
        }
    }

    public function render()
    {
        return view('pages.customer.orders', [
            'orders' => Order::with(['orderState', 'orderedProducts.product', 'orderAddress'])
                ->where('user_id', Auth::id())
                ->latest()
                ->get(),
        ])->layout('layouts.app', ['title' => __('Orders')]);
    }
}; ?>

<section class="space-y-6">
        <div class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm backdrop-blur">
            <flux:heading size="xl">{{ __('My orders') }}</flux:heading>
            <flux:text>{{ __('See all the orders you have placed so far.') }}</flux:text>
        </div>

        <div class="space-y-4">
            @forelse ($orders as $order)
                <div class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <flux:heading size="lg">{{ __('Order #') }}{{ $order->id }}</flux:heading>
                            <flux:text>{{ $order->created_at->format('d M, Y h:i A') }}</flux:text>
                        </div>

                        <span class="rounded-full bg-red-50 px-3 py-1 text-sm font-semibold text-red-700">
                            {{ $order->orderState?->name ?? __('pending') }}
                        </span>
                    </div>

                    <div class="mt-4 grid gap-4 lg:grid-cols-2">
                        <div>
                            <p class="text-sm text-zinc-500">{{ __('Shipping address') }}</p>
                            <p class="font-medium">{{ $order->orderAddress?->name }}</p>
                            <p class="text-sm text-zinc-600">{{ $order->orderAddress?->address }}</p>
                            <p class="text-sm text-zinc-600">{{ $order->orderAddress?->phone }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-zinc-500">{{ __('Totals') }}</p>
                            <p class="font-medium">{{ __('Product total: ৳') }}{{ number_format((float) $order->total_price, 2) }}</p>
                            <p class="font-medium">{{ __('Shipping: ৳') }}{{ number_format((float) $order->total_shipping_charge, 2) }}</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2">
                        @foreach ($order->orderedProducts as $item)
                            <div class="flex items-center justify-between rounded-2xl bg-red-50/60 px-4 py-3">
                                <span>{{ $item->product?->name }} x {{ $item->quantity }}</span>
                                <span class="font-semibold">৳{{ number_format((float) $item->price, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-sm">
                    {{ __('No orders yet.') }}
                </div>
            @endforelse
        </div>
    </section>