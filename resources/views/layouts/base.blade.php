@extends('adminlte::page')

@section('adminlte_css_pre')
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon.png') }}?v={{ config('app.version', '1.0') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}?v={{ config('app.version', '1.0') }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('favicon.png') }}">
    @vite('resources/css/adminlte-theme.css')
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
    
    @if(session('info'))
        <script>
            mostrarInfo("Información", "{{ session('info') }}");
        </script>
    @endif
    
    @stack('scripts')
@stop
