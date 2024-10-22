@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Prompts</h1>
    <a href="{{ route('prompts.create') }}" class="btn btn-primary">Crear Nuevo Prompt</a>
    <br><br>
    <div class="card">
        <div class="card-body">
            <table id="prompts-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Frase</th>
                        <th>Plantilla</th>
                        <th class="no-sort">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('js')
<script>
    function confirmDelete() {
        return confirm('¿Estás seguro de que deseas eliminar este prompt?');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const table = $('#prompts-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('prompts.data') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'sentence', name: 'sentence'},
                {data: 'template_sentence', name: 'template.sentence'},
                {
                    data: 'actions', 
                    name: 'actions', 
                    orderable: false, 
                    searchable: false
                }
            ],
            "language": {
                "processing": "Procesando...",
                "lengthMenu": "Mostrar _MENU_",
                "zeroRecords": "No se encontraron resultados",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "&#9654;",
                    "previous": "&#9664;"
                }
            },
            "dom": '<"row table-container"<"col-sm-6"B><"col-sm-6"f><"col-sm-1">>' +
                   '<"row table-row-with-margin"<"col-sm-12 px-0"tr>>' +
                   '<"row"<"col-sm-4"i><"col-sm-2"l><"col-sm-6"p>>',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
        });
    });
</script>
@endsection

@section('css')
<style>
    .table-row-with-margin {
        margin-left: -16px;
        margin-right: -16px;
    }

    .card-body{
        overflow-x:auto;
    }
</style>
@endsection