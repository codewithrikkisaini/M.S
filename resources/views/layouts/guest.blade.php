<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Merahkie PMS | Register Hotel</title>

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        body {
            font-family: 'Outfit', sans-serif !important;
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 font-sans antialiased overflow-x-hidden">

    {{ $slot }}

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
