@php
    $sessionToast = collect(['success', 'error', 'warning', 'info'])
        ->mapWithKeys(fn ($key) => [$key => session($key)])
        ->filter()
        ->map(fn ($message, $key) => [
            'variant' => $key === 'error' ? 'danger' : $key,
            'message' => $message,
        ])
        ->first();

    $paymentToast = match (request()->query('payment_result')) {
        'success' => ['variant' => 'success', 'message' => 'Pembayaran diterima Midtrans dan sedang diverifikasi oleh sistem.'],
        'pending' => ['variant' => 'warning', 'message' => 'Pembayaran masih menunggu penyelesaian.'],
        'error' => ['variant' => 'danger', 'message' => 'Proses pembayaran gagal. Silakan coba kembali dari detail pesanan.'],
        'closed' => ['variant' => 'info', 'message' => 'Jendela pembayaran ditutup. Anda dapat melanjutkan pembayaran dari detail pesanan.'],
        default => null,
    };

    $initialToast = $sessionToast ?? $paymentToast;
@endphp

<div
    class="ui-toast-region"
    x-data="{
        visible: false,
        message: '',
        variant: 'info',
        timer: null,
        show(detail) {
            const payload = Array.isArray(detail) ? detail[0] : detail;
            this.message = payload?.message ?? 'Proses selesai.';
            this.variant = ['success', 'danger', 'warning', 'info'].includes(payload?.variant)
                ? payload.variant
                : (payload?.type === 'error' ? 'danger' : (payload?.type ?? 'info'));
            this.visible = true;
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.visible = false, payload?.duration ?? 4500);
        },
    }"
    x-init="@js($initialToast) && show(@js($initialToast))"
    x-on:toast.window="show($event.detail)"
    x-on:alert.window="show($event.detail)"
    aria-live="polite"
>
    <div
        x-cloak
        x-show="visible"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="ui-toast"
        :class="{
            'ui-toast-success': variant === 'success',
            'ui-toast-danger': variant === 'danger',
            'ui-toast-warning': variant === 'warning',
            'ui-toast-info': variant === 'info',
        }"
        role="status"
    >
        <span class="mt-0.5 h-2.5 w-2.5 shrink-0 rounded-full bg-current" aria-hidden="true"></span>
        <p class="flex-1 font-medium text-content" x-text="message"></p>
        <button type="button" class="rounded p-1 text-muted hover:bg-subtle hover:text-content" x-on:click="visible = false" aria-label="Tutup notifikasi">×</button>
    </div>
</div>
