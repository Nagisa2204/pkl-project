@props(['wireModel' => 'turnstileToken'])

@if (config('turnstile.enabled'))
    <div class="flex justify-center">
        <div wire:ignore x-data="{
            widgetId: null,
            boot() {
                const render = () => {
                    if (!window.turnstile) {
                        window.setTimeout(render, 100);
                        return;
                    }
        
                    this.widgetId = window.turnstile.render(this.$refs.widget, {
                        sitekey: @js(config('turnstile.site_key')),
                        theme: 'auto',
                        callback: (token) => this.$wire.set(@js($wireModel), token),
                        'expired-callback': () => this.$wire.set(@js($wireModel), ''),
                        'error-callback': () => {
                            this.$wire.set(@js($wireModel), '');
                            return true;
                        },
                    });
                };
        
                render();
            },
            reset() {
                this.$wire.set(@js($wireModel), '');
        
                if (window.turnstile && this.widgetId !== null) {
                    window.turnstile.reset(this.widgetId);
                }
            },
        }" x-init="boot()" x-on:turnstile-reset.window="reset()">
            <div x-ref="widget"></div>
        </div>

        <x-input-error :messages="$errors->get($wireModel)" class="mt-2" />
    </div>

    @once
        @push('scripts')
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" async defer></script>
        @endpush
    @endonce
@endif
