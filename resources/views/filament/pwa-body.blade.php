<!-- PWA Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(reg) {
                    console.log('PWA ServiceWorker registered with scope: ', reg.scope);
                })
                .catch(function(err) {
                    console.log('PWA ServiceWorker registration failed: ', err);
                });
        });
    }
</script>
