<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <x-head />

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <livewire:layout.impersonation-banner />
        <livewire:layout.navigation />

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{-- Global Toast Notifications --}}
            @if (session()->has('success') || session()->has('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="fixed top-4 right-4 z-[9999] max-w-sm w-full shadow-2xl rounded-2xl p-4 flex items-start gap-3 border {{ session()->has('error') ? 'bg-destructive text-destructive-foreground border-destructive/20' : 'bg-emerald-600 text-white border-emerald-500' }}">
                    <div class="shrink-0 pt-0.5">
                        @if(session()->has('error'))
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 text-sm font-semibold leading-relaxed">
                        {{ session('success') ?? session('error') }}
                    </div>
                    <button @click="show = false" class="shrink-0 opacity-70 hover:opacity-100 transition-opacity">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

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
</body>
</html>