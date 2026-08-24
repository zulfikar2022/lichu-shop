<x-layouts::app :title="__('Dashboard')">
    <section class="space-y-6">
        <div class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm backdrop-blur">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <flux:heading size="xl">{{ __('Lichi Shop Dashboard') }}</flux:heading>
                    <flux:text>{{ __('A simple role-based dashboard for super-admins, admins, and customers.') }}</flux:text>
                </div>

                <div class="rounded-full bg-red-50 px-4 py-2 text-sm font-medium text-red-700">
                    {{ auth()->user()->roleLabel() }}
                </div>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <a href="{{ route('customer.products') }}" wire:navigate class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <flux:heading size="lg">{{ __('Shop products') }}</flux:heading>
                <flux:text>{{ __('Browse lichi products and add them to your cart.') }}</flux:text>
            </a>

            <a href="{{ route('customer.cart') }}" wire:navigate class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <flux:heading size="lg">{{ __('My cart') }}</flux:heading>
                <flux:text>{{ __('Review items and place an order.') }}</flux:text>
            </a>

            <a href="{{ route('customer.orders') }}" wire:navigate class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <flux:heading size="lg">{{ __('My orders') }}</flux:heading>
                <flux:text>{{ __('See your order history and delivery status.') }}</flux:text>
            </a>
        </div>

        @if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
            <div class="grid gap-4 lg:grid-cols-2">
                <a href="{{ route('admin.products') }}" wire:navigate class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <flux:heading size="lg">{{ __('Manage products') }}</flux:heading>
                    <flux:text>{{ __('Add, edit, and toggle product availability.') }}</flux:text>
                </a>

                @if (auth()->user()->isSuperAdmin())
                    <a href="{{ route('superadmin.users') }}" wire:navigate class="rounded-3xl border border-white/70 bg-white/85 p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                        <flux:heading size="lg">{{ __('Manage users') }}</flux:heading>
                        <flux:text>{{ __('Promote or demote admin accounts and toggle activity.') }}</flux:text>
                    </a>
                @endif
            </div>
        @endif
    </section>
</x-layouts::app>
