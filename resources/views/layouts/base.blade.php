@extends('adminlte::page')

@section('adminlte_css_pre')
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon.png') }}?v={{ config('app.version', '1.0') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}?v={{ config('app.version', '1.0') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('favicon.png') }}">
    @vite('resources/css/adminlte-theme.css')
    <style>
        /* Fix para paginación de Laravel con AdminLTE */
        .pagination {
            margin: 0;
        }
        .pagination .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        /* Fix para preloader - quitar círculo/óvalo alrededor del logo */
        .preloader img {
            border-radius: 0 !important;
            background: transparent !important;
            padding: 0 !important;
            box-shadow: none !important;
        }
        .preloader {
            background-color: rgba(255, 255, 255, 0.95) !important;
        }
    </style>
@stop

@section('js')
    @parent
    <script src="{{ asset('js/sweetalert-helpers.js') }}"></script>
    
    {{-- Mostrar mensajes flash con SweetAlert --}}
    @if(session('success'))
        <script>
            mostrarExito("{{ session('success') }}");
        </script>
    @endif
    
    @if(session('error'))
        <script>
            mostrarError("{{ session('error') }}");
        </script>
    @endif
    
    @if(session('warning'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                html: `{!! str_replace(["\n", "\r"], '<br>', session('warning')) !!}`,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#f39c12',
                width: '600px',
            });
        </script>
    @endif
    
    @if(session('info'))
        <script>
            mostrarInfo("Información", "{{ session('info') }}");
        </script>
    @endif
    
    @stack('scripts')
@stop
