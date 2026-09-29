<x-filament::section :aside="false">
    <x-slot name="heading">
        <div style="display: flex; align-items: center; gap: 8px; font-size: 16px; font-weight: 700;">
            <x-filament::icon icon="heroicon-o-shield-check" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" style="width: 20px; height: 20px;" />
            <span>{{ __('filament-breezy::default.profile.2fa.title') }}</span>
        </div>
    </x-slot>

    <x-slot name="description">
        <span style="font-size: 12.5px;">{{ __('filament-breezy::default.profile.2fa.description') }}</span>
    </x-slot>

    <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 12px;">
        @if($this->showRequiresTwoFactorAlert())
            <div style="padding: 14px 16px; border-radius: 12px; background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.3); display: flex; align-items: center; gap: 12px;">
                <x-filament::icon icon="heroicon-s-shield-exclamation" class="w-5 h-5 text-rose-500 shrink-0" style="width: 20px; height: 20px;" />
                <p style="font-size: 13px; font-weight: 600; color: #e11d48; margin: 0;">
                    {{ __('filament-breezy::default.profile.2fa.must_enable') }}
                </p>
            </div>
        @endif

        @unless ($user->hasEnabledTwoFactor())
            <div style="padding: 16px 20px; border-radius: 14px; background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25); display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="padding: 6px; border-radius: 8px; background: rgba(245, 158, 11, 0.15); color: #d97706; display: flex; align-items: center; justify-content: center;">
                        <x-filament::icon icon="heroicon-o-shield-exclamation" class="w-5 h-5" style="width: 20px; height: 20px;" />
                    </div>
                    <h3 style="font-size: 15px; font-weight: 700; margin: 0; color: inherit;">
                        {{ __('filament-breezy::default.profile.2fa.not_enabled.title') }}
                    </h3>
                </div>
                <p style="font-size: 12.5px; color: var(--prof-text-muted, #64748b); margin: 0; line-height: 1.5;">
                    {{ __('filament-breezy::default.profile.2fa.not_enabled.description') }}
                </p>
                <div style="padding-top: 6px;">
                    {{ $this->enableAction }}
                </div>
            </div>
        @else
            @if ($user->hasConfirmedTwoFactor())
                <div style="padding: 16px 20px; border-radius: 14px; background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="padding: 6px; border-radius: 8px; background: rgba(16, 185, 129, 0.15); color: #059669; display: flex; align-items: center; justify-content: center;">
                            <x-filament::icon icon="heroicon-o-shield-check" class="w-5 h-5" style="width: 20px; height: 20px;" />
                        </div>
                        <h3 style="font-size: 15px; font-weight: 700; margin: 0; color: #059669;">
                            {{ __('filament-breezy::default.profile.2fa.enabled.title') }}
                        </h3>
                    </div>
                    <p style="font-size: 12.5px; color: var(--prof-text-muted, #64748b); margin: 0; line-height: 1.5;">
                        {{ __('filament-breezy::default.profile.2fa.enabled.description') }}
                    </p>
                    @if ($showRecoveryCodes)
                        <div style="padding-top: 6px; display: flex; flex-direction: column; gap: 8px;">
                            <p style="font-size: 12px; font-weight: 600; margin: 0;">{{ __('filament-breezy::default.profile.2fa.enabled.store_codes') }}</p>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 8px;">
                                @foreach ($this->recoveryCodes->toArray() as $code)
                                    <span style="font-family: monospace; font-size: 11.5px; padding: 6px 10px; border-radius: 8px; text-align: center; font-weight: 600; background: var(--prof-badge-bg, #f1f5f9); border: 1px solid var(--prof-border, #e2e8f0); user-select: all;">{{ $code }}</span>
                                @endforeach
                            </div>
                            <div style="margin-top: 4px;">
                                <x-filament-breezy::clipboard-link :data="$this->recoveryCodes->join(',')" />
                            </div>
                        </div>
                    @endif
                    <div style="display: flex; align-items: center; gap: 12px; padding-top: 8px;">
                        {{ $this->regenerateCodesAction }}
                        {{ $this->disableAction }}
                    </div>
                </div>
            @else
                <div style="padding: 16px 20px; border-radius: 14px; background: rgba(14, 165, 233, 0.08); border: 1px solid rgba(14, 165, 233, 0.25); display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="padding: 6px; border-radius: 8px; background: rgba(14, 165, 233, 0.15); color: #0284c7; display: flex; align-items: center; justify-content: center;">
                            <x-filament::icon icon="heroicon-o-question-mark-circle" class="w-5 h-5" style="width: 20px; height: 20px;" />
                        </div>
                        <h3 style="font-size: 15px; font-weight: 700; margin: 0; color: #0284c7;">
                            {{ __('filament-breezy::default.profile.2fa.finish_enabling.title') }}
                        </h3>
                    </div>
                    <p style="font-size: 12.5px; color: var(--prof-text-muted, #64748b); margin: 0; line-height: 1.5;">
                        {{ __('filament-breezy::default.profile.2fa.finish_enabling.description') }}
                    </p>
                    <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-start; padding-top: 8px;">
                        <div style="padding: 14px; background: #ffffff; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; text-align: center;">
                            <div style="width: 160px; height: 160px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                {!! $this->getTwoFactorQrCode() !!}
                            </div>
                            <p style="padding-top: 8px; font-size: 11px; font-family: monospace; color: #64748b; margin: 0;">
                                {{ $this->two_factor_secret }}
                            </p>
                        </div>
                        <div style="flex: 1; min-width: 240px; display: flex; flex-direction: column; gap: 8px;">
                            <p style="font-size: 12px; font-weight: 600; margin: 0;">{{ __('filament-breezy::default.profile.2fa.enabled.store_codes') }}</p>
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 6px;">
                                @foreach ($this->recoveryCodes->toArray() as $code)
                                    <span style="font-family: monospace; font-size: 11px; padding: 4px 8px; border-radius: 6px; background: var(--prof-badge-bg, #f1f5f9); border: 1px solid var(--prof-border, #e2e8f0); text-align: center; user-select: all;">{{ $code }}</span>
                                @endforeach
                            </div>
                            <div style="margin-top: 4px;">
                                <x-filament-breezy::clipboard-link :data="$this->recoveryCodes->join(',')" />
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 12px; padding-top: 10px;">
                        {{ $this->confirmAction }}
                        {{ $this->disableAction }}
                    </div>
                </div>
            @endif
        @endunless
    </div>

    <x-filament-actions::modals />
</x-filament::section>
