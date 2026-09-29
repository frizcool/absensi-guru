<x-filament::section :aside="false">
    <x-slot name="heading">
        <div style="display: flex; align-items: center; gap: 8px; font-size: 16px; font-weight: 700;">
            <x-filament::icon icon="heroicon-o-computer-desktop" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" style="width: 20px; height: 20px;" />
            <span>{{ __('filament-breezy::default.profile.browser_sessions.heading') }}</span>
        </div>
    </x-slot>

    <x-slot name="description">
        <span style="font-size: 12.5px;">{{ __('filament-breezy::default.profile.browser_sessions.subheading') }}</span>
    </x-slot>

    <div style="margin-top: 12px;">
        {{ $this->form }}
    </div>

    <x-filament-actions::modals />
</x-filament::section>
