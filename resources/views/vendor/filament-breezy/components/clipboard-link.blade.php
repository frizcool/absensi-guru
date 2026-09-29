@props([
    'data'
])
<a x-data="{}"
    x-on:click.prevent="window.navigator.clipboard.writeText(@js($data)); $tooltip('{{ __('filament-breezy::default.clipboard.tooltip') }}');"
    href="#"
    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 rounded-lg hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800/60 transition-colors">
    <x-filament::icon icon="heroicon-s-clipboard-document" class="w-3.5 h-3.5 shrink-0" style="width: 14px; height: 14px;" />
    <span>{{ __('filament-breezy::default.clipboard.link') }}</span>
</a>
