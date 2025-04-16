@if(config('midtrans.is_production'))
    <script type="text/javascript"
            src="{{ config('midtrans.production.snap_url', 'https://app.midtrans.com/snap/snap.js') }}"
            data-client-key="{{ config('midtrans.production.client_key') }}">
    </script>
@else
    <script type="text/javascript"
            src="{{ config('midtrans.sb.snap_frontend_url', 'https://app.sandbox.midtrans.com/snap/snap.js') }}"
            data-client-key="{{ config('midtrans.sb.client_key') }}">
    </script>
@endif

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {
        window.addEventListener('processPayment', (event) => {
            const snap_token = event.detail[0]['snap_token'];

            window.snap.pay(snap_token, {
                onSuccess: function (result) {
                    Livewire.dispatch('detailTransaction', {result});
                },
                onPending: function (result) {
                    Livewire.dispatch('detailTransaction', {result});
                },
                onError: function (result) {
                    Livewire.dispatch('detailTransaction', {result});
                },
                onClose: function () {
                    new FilamentNotification()
                        .title('Anda menutup tanpa menyelesaikan pembayaran')
                        .warning()
                        .send();
                }
            })
        });
    })
</script>
