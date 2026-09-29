<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div>
        <div>
            <div class="text-xs text-gray-600 dark:text-gray-400">
                {{ __('filament-breezy::default.profile.browser_sessions.content') }}
            </div>
            @if (count($data) > 0)
                <div class="mt-4 space-y-3">
                    @foreach ($data as $session)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50/80 dark:bg-gray-800/40 border border-gray-200/60 dark:border-gray-700/60">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-gray-200/60 dark:bg-gray-700/60 text-gray-600 dark:text-gray-300 shrink-0">
                                    @if ($session->device['desktop'])
                                        <x-filament::icon
                                            icon="heroicon-o-computer-desktop"
                                            class="w-5 h-5"
                                            style="width: 20px; height: 20px;"
                                        />
                                    @else
                                        <x-filament::icon
                                            icon="heroicon-o-device-phone-mobile"
                                            class="w-5 h-5"
                                            style="width: 20px; height: 20px;"
                                        />
                                    @endif
                                </div>

                                <div>
                                    <div class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $session->device['platform'] ? $session->device['platform'] : __('Unknown') }} - {{ $session->device['browser'] ? $session->device['browser'] : __('Unknown') }}
                                    </div>

                                    <div class="text-[11px] text-gray-500">
                                        {{ $session->ip_address }} &bull;
                                        @if ($session->is_current_device)
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ __('filament-breezy::default.profile.browser_sessions.device') }}</span>
                                        @else
                                            {{ __('filament-breezy::default.profile.browser_sessions.last_active') }} {{ $session->last_active }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
