<link rel="icon" href="{{ url('favicon.png') }}">

@extends('adminlte::page')

{{-- Extend and customize the browser title --}}

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle') | @yield('subtitle') @endif
@stop

{{-- Extend and customize the page content header --}}

@section('content_header')
    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title')

            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif
@stop

@section('content')
    {{-- Mostrar mensajes de éxito o error con botón de cierre --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @yield('content_body')
@stop

@push('js')
@vite(['resources/js/app.js'])

@endpush

@push('css')
@vite(['resources/sass/app.scss'])

<style>
.main-sidebar {
    height: 100vh; /* Ocupa todo el alto de la ventana */
    display: flex;
    flex-direction: column;
}

.sidebar {
    flex: 1; /* Hace que el sidebar ocupe el espacio restante */
}

.sidebar-footer {
    padding: 10px;
    border-top: 1px solid #dee2e6;
}

/* Añade un margen superior al contenido */
.content-wrapper {
    padding-top: 20px; /* Ajusta el valor según tu preferencia */
}

.wrapper {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.content-wrapper {
    flex: 1;
}
</style>
@endpush