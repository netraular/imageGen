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
    @yield('content_body')
@stop

@push('js')

@endpush

@push('css')
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
</style>
@endpush