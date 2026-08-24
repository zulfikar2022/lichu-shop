<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased text-zinc-900">
        <!-- Desktop header (top navigation) -->
        <flux:header sticky class="hidden lg:flex border-b border-white/70 bg-white/80 backdrop-blur supports-backdrop-filter:bg-white/70">
            <div class="w-full flex items-center justify-between gap-4 px-4 lg:px-8">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-lg font-semibold">Lichu Shop</a>
                </div>

                <nav class="flex items-center gap-2">
                    @if (auth()->user()->isSuperAdmin())
                        <flux:navlist.item icon="users" :href="route('superadmin.users')" :current="request()->routeIs('superadmin.users')" wire:navigate>
                            {{ __('Manage users') }}
                        </flux:navlist.item>

                    @elseif (auth()->user()->isAdmin())
                        <flux:navlist.item icon="home-modern" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:navlist.item>

                        <flux:navlist.item icon="shopping-bag" :href="route('admin.products')" :current="request()->routeIs('admin.products')" wire:navigate>
                            {{ __('Manage products') }}
                        </flux:navlist.item>

                        <flux:navlist.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                            {{ __('Manage users') }}
                        </flux:navlist.item>

                    @else
                        <flux:navlist.item icon="home-modern" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:navlist.item>

                        <flux:navlist.item icon="shopping-bag" :href="route('customer.products')" :current="request()->routeIs('customer.products')" wire:navigate>
                            {{ __('Shop') }}
                        </flux:navlist.item>

                        <flux:navlist.item icon="shopping-cart" :href="route('customer.cart')" :current="request()->routeIs('customer.cart')" wire:navigate>
                            {{ __('Cart') }}
                        </flux:navlist.item>

                        <flux:navlist.item icon="clipboard-document-list" :href="route('customer.orders')" :current="request()->routeIs('customer.orders')" wire:navigate>
                            {{ __('Orders') }}
                        </flux:navlist.item>
                    @endif
                </nav>

                <div class="flex items-center gap-4">
                    <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
                </div>
            </div>
        </flux:header>

        <!-- Mobile sidebar (toggle opens this) -->
        <flux:sidebar sticky collapsible="mobile" class="lg:hidden border-r border-white/70 bg-white/80 backdrop-blur supports-backdrop-filter:bg-white/70">
            <flux:sidebar.header>
                <div class="flex items-center gap-3">
                    <flux:sidebar.toggle icon="bars-2" />
                    <a href="{{ route('dashboard') }}" class="text-lg font-semibold">Lichu Shop</a>
                </div>
                <flux:sidebar.collapse />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    @if (auth()->user()->isSuperAdmin())
                        <flux:sidebar.item icon="users" :href="route('superadmin.users')" :current="request()->routeIs('superadmin.users')" wire:navigate>
                            {{ __('Manage users') }}
                        </flux:sidebar.item>

                    @elseif (auth()->user()->isAdmin())
                        <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="shopping-bag" :href="route('admin.products')" :current="request()->routeIs('admin.products')" wire:navigate>
                            {{ __('Manage products') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>
                            {{ __('Manage users') }}
                        </flux:sidebar.item>

                    @else
                        <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="shopping-bag" :href="route('customer.products')" :current="request()->routeIs('customer.products')" wire:navigate>
                            {{ __('Shop') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="shopping-cart" :href="route('customer.cart')" :current="request()->routeIs('customer.cart')" wire:navigate>
                            {{ __('Cart') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="clipboard-document-list" :href="route('customer.orders')" :current="request()->routeIs('customer.orders')" wire:navigate>
                            {{ __('Orders') }}
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="block lg:hidden" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden border-b border-white/70 bg-white/80 backdrop-blur supports-backdrop-filter:bg-white/70">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
