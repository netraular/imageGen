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
                <tbody>
                    @foreach($prompts as $prompt)
                    <tr>
                        <td>{{ $prompt->id }}</td>
                        <td>{{ $prompt->sentence }}</td>
                        <td>{{ $prompt->template->sentence }}</td>
                        <td>
                            <a href="{{ route('prompts.edit', $prompt->id) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('prompts.destroy', $prompt->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
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

    // Generación de la datatable
    document.addEventListener('DOMContentLoaded', function() {
        const table = $('#prompts-table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "responsive": true,
            "language": {
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "paginate": {
                    "previous": "&#9664;", 
                    "next": "&#9654;" 
                },
                "lengthMenu": "Mostrar _MENU_",
            },
            "columnDefs": [
                { "orderable": false, "targets": 'no-sort' }
            ],
            "dom": '<"row table-container"<"col-sm-6"B><"col-sm-6"f><"col-sm-1">>' +
                    '<"row table-row-with-margin"<"col-sm-12 px-0"tr>>' +
                    '<"row"<"col-sm-5"i><"col-sm-2"l><"col-sm-5"p>>',
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