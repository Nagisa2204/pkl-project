@props([
    'token' => null,
    'redirectUrl' => null,
])

<div
    class="hidden"
    x-data
    x-on:midtrans-snap-open.window="window.openMidtransSnap($event.detail.token, $event.detail.redirectUrl)"
    @if($token && $redirectUrl)
        x-init="setTimeout(() => window.openMidtransSnap(@js($token), @js($redirectUrl)), 250)"
    @endif
></div>

@once
    @push('scripts')
        @if(config('midtrans.client_key'))
            <script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
        @endif
    @endpush
@endonce
