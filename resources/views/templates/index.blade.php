@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Plantillas</h1>
    <a href="{{ route('templates.create') }}" class="btn btn-primary">Crear Nueva Plantilla</a>
    <br><br>
    
    <div class="card">
        <div class="card-body">
            <table id="templates-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Frase</th>
                        <th class="no-sort">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                    <tr>
                        <td>{{ $template->id }}</td>
                        <td>{{ $template->sentence }}</td>
                        <td>
                            <a href="{{ route('templates.edit', $template->id) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('templates.destroy', $template->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                            <form action="{{ route('templates.generate') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="template_id" value="{{ $template->id }}">
                                <button type="submit" class="btn btn-sm btn-success">Generar Prompts</button>
                            </form>
                            <form action="{{ route('templates.executePrompts') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="template_id" value="{{ $template->id }}">
                                <button type="submit" class="btn btn-sm btn-primary">Ejecutar Prompts</button>
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
    //Generación de la datatable
    document.addEventListener('DOMContentLoaded', function() {
        const table = $('#templates-table').DataTable({
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