<x-filament::section :aside="false">
    <x-slot name="heading">
        <div style="display: flex; align-items: center; gap: 8px; font-size: 16px; font-weight: 700;">
            <x-filament::icon icon="heroicon-o-key" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" style="width: 20px; height: 20px;" />
            <span>{{ __('filament-breezy::default.profile.password.heading') }}</span>
        </div>
    </x-slot>

    <x-slot name="description">
        <span style="font-size: 12.5px;">{{ __('filament-breezy::default.profile.password.subheading') }}</span>
    </x-slot>

    <form wire:submit.prevent="submit" style="display: flex; flex-direction: column; gap: 20px; margin-top: 12px;">
        {{ $this->form }}

        <div style="display: flex; justify-content: flex-end; padding-top: 12px; border-top: 1px solid var(--prof-border, #e2e8f0);">
            <x-filament::button
                type="submit"
                color="primary"
                icon="heroicon-o-arrow-path"
            >
                {{ __('filament-breezy::default.profile.password.submit.label') }}
            </x-filament::button>
        </div>
    </form>
</x-filament::section>
