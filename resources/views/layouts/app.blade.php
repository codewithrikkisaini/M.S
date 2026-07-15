<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $hotelName }} | {{ $title ?? 'Dashboard' }}</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" data-navigate-once>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js" data-navigate-once></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" data-navigate-once></script>
    @stack('styles')
</head>
<body class="h-full bg-slate-50 font-sans antialiased" x-data="{ sidebarOpen: true, mobileSidebarOpen: false }">

<div class="flex h-full">

    {{-- ===== SIDEBAR ===== --}}
    @include('layouts.sidebar')

    {{-- Mobile sidebar overlay --}}
    <div x-show="mobileSidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebarOpen = false"
         class="fixed inset-0 z-30 bg-slate-900/60 lg:hidden"
         style="display:none"></div>

    {{-- ===== MAIN AREA ===== --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-16'">

        {{-- Top Navbar --}}
        @include('layouts.navbar')

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto">
            <div class="px-4 py-5 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
                {{ $slot }}
            </div>
        </main>

        {{-- Footer --}}
        <footer class="border-t border-gray-200 bg-white px-6 py-3">
            <p class="text-xs text-gray-400 text-center">
                &copy; {{ date('Y') }} {{ $hotelName }} &mdash; All rights reserved.
            </p>
        </footer>
    </div>
</div>

{{-- Toast listener (SweetAlert2) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
    window.addEventListener('toast', event => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: event.detail[0]?.type ?? 'success',
            title: event.detail[0]?.message ?? event.detail.message ?? '',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    });
    @if(session('toast'))
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: @json(session('toast.type', 'success')),
            title: @json(session('toast.message')),
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    });
    @endif
</script>
@stack('scripts')
</body>
</html>
