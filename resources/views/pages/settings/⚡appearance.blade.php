<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Appearance settings')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Appearance settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <div class="rounded-xl border border-white/70 bg-white/80 p-4 text-sm text-zinc-700 shadow-sm">
            {{ __('This application uses a single light theme with a red-to-green gradient background on every page.') }}
        </div>
    </x-pages::settings.layout>
</section>
