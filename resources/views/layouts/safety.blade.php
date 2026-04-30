<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<x-head :seoKey="$seoKey ?? null" />

<body class="font-sans antialiased bg-background text-foreground">
    <livewire:layout.impersonation-banner />
    <livewire:layout.safety-navigation />

    <main>
        {{-- Global Toast Notifications --}}
        {{-- Notification System is handled by SweetAlert2 in the script section below --}}

        {{ $slot }}
    </main>

    <livewire:layout.safety-footer />

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            showCloseButton: true,
            timer: 4000,
            timerProgressBar: true,
            background: '#ffffff',
            customClass: {
                popup: 'rounded-2xl border-none shadow-2xl p-4',
                title: 'text-sm font-black tracking-tight text-slate-900 ml-2',
                htmlContainer: 'text-xs text-slate-600 font-medium ml-2',
                closeButton: 'focus:outline-none'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        window.addEventListener('swal', function (e) {
            const data = Array.isArray(e.detail) ? e.detail[0] : e.detail;
            Toast.fire({
                title: data.title || 'Notification',
                text: data.text || '',
                icon: data.icon || 'info'
            });
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('request', ({ fail }) => {
                fail(({ status, preventDefault }) => {
                    preventDefault();
                    if (status === 419) {
                        Swal.fire({
                            title: 'Session Expired',
                            text: 'Your session has expired. Please refresh the page and try again.',
                            icon: 'warning',
                            confirmButtonText: 'Refresh Page',
                            confirmButtonColor: '#0f172a',
                            borderRadius: '1.5rem'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Server Error (' + status + ')',
                            text: 'Unable to communicate with the server. Please check your internet connection or contact support.',
                            icon: 'error',
                            confirmButtonText: 'Got it',
                            confirmButtonColor: '#0f172a',
                            borderRadius: '1.5rem'
                        });
                    }

                    // Force remove any loading states across the page to unlock UI
                    document.querySelectorAll('[wire\\:loading]').forEach(el => el.style.display = 'none');
                    document.querySelectorAll('[wire\\:loading\\.attr="disabled"]').forEach(el => el.removeAttribute('disabled'));
                    document.querySelectorAll('[wire\\:loading\\.class]').forEach(el => el.classList.remove('opacity-50'));
                });
            });
        });
    </script>

    @if(session('success') || session('error'))
        <script>
            function fireSessionToast() {
                Toast.fire({
                    title: '{{ session("success") ? "Success!" : "Error!" }}',
                    text: "{{ session('success') ?? session('error') }}",
                    icon: '{{ session("success") ? "success" : "error" }}'
                });
            }

            if (window.Livewire) {
                document.addEventListener('livewire:navigated', fireSessionToast, { once: true });
            } else {
                fireSessionToast();
            }
        </script>
    @endif

</body>

</html>