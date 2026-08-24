<?php

use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Manage users')] class extends Component {
    public function toggleAdmin(int $userId): void
    {
        /** @var \App\Models\User|null $current */
        $current = auth()->user();

        if (! $current || ! $current->isSuperAdmin()) {
            abort(403);
        }

        $user = User::findOrFail($userId);

        if ($user->isSuperAdmin()) {
            return;
        }

        $user->assignRole($user->isAdmin() ? User::ROLE_CUSTOMER : User::ROLE_ADMIN);

        Flux::toast(variant: 'success', text: __('User role updated.'));
    }

    public function toggleActive(int $userId): void
    {
        /** @var \App\Models\User|null $current */
        $current = auth()->user();

        if (! $current || (! $current->isAdmin() && ! $current->isSuperAdmin())) {
            abort(403);
        }

        $user = User::findOrFail($userId);

        $user->is_active = ! $user->is_active;
        $user->save();

        Flux::toast(variant: 'success', text: __('User activity updated.'));
    }

    public function render()
    {
        return view('pages.superadmin.users', [
            'users' => User::orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => __('Manage users')]);
    }
}; ?>

<section class="space-y-6">
        <div class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm backdrop-blur">
            <flux:heading size="xl">{{ __('Manage users') }}</flux:heading>
            <flux:text>{{ __('Promote customers to admin, revoke admin access back to customer, and toggle user activity.') }}</flux:text>
        </div>

        <div class="overflow-hidden rounded-3xl border border-white/70 bg-white/85 shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-red-50/80 text-zinc-700">
                    <tr>
                        <th class="px-4 py-3">{{ __('Name') }}</th>
                        <th class="px-4 py-3">{{ __('Email') }}</th>
                        <th class="px-4 py-3">{{ __('Role') }}</th>
                        <th class="px-4 py-3">{{ __('Active') }}</th>
                        <th class="px-4 py-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-t border-zinc-100">
                            <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                            <td class="px-4 py-3">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ $user->roleLabel() }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $user->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @if (auth()->user()->isSuperAdmin() && ! $user->isSuperAdmin())
                                        <flux:button size="sm" variant="primary" wire:click="toggleAdmin({{ $user->id }})">
                                            {{ $user->isAdmin() ? __('Revoke admin') : __('Make admin') }}
                                        </flux:button>
                                    @endif

                                    <flux:button size="sm" variant="filled" wire:click="toggleActive({{ $user->id }})">
                                        {{ $user->is_active ? __('Deactivate') : __('Activate') }}
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>