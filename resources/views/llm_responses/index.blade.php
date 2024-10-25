@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Respuestas LLM</h1>
    
    <div class="card">
        <div class="card-body">
            <table id="llm-responses-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Respuesta</th>
                        <th>Fuente</th>
                        <th>Prompt</th>
                        <th>Estado</th>
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
        return confirm('¿Estás seguro de que deseas eliminar esta respuesta LLM?');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const table = $('#llm-responses-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('llm_responses.data') }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'response', name: 'response'},
                {data: 'source', name: 'source'},
                {data: 'prompt_sentence', name: 'prompt.sentence'},
                {data: 'status', name: 'status'},
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