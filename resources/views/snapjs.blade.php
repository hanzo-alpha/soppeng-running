@if(config('midtrans.is_production'))
    <script type="text/javascript"
            src="{{ config('midtrans.snap_frontend_url', 'https://app.midtrans.com/snap/snap.js') }}"
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
                    new FilamentNotification()
                        .title('Pembayaran anda sedang diproses')
                        .icon('heroicon-o-check-circle')
                        .color('info')
                        .actions([
                            new FilamentNotificationAction('Konfirmasi')
                                .color('primary')
                                .button()
                                .icon('heroicon-o-arrow-down-tray')
                                .dispatch('detailTransaction', {result}),
                        ])
                        .send()
                },
                onPending: function (result) {
                    new FilamentNotification()
                        .title('Menunggu Pembayaran')
                        .icon('heroicon-o-clock')
                        .info()
                        .body(result)
                        .send();
                },
                onError: function (result) {
                    new FilamentNotification()
                        .title('Pembayaran gagal')
                        .danger()
                        .body(result)
                        .icon('heroicon-o-x-mark')
                        .send();
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
