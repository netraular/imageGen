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
                <tbody>
                    @foreach($llmResponses as $llmResponse)
                    <tr>
                        <td>{{ $llmResponse->id }}</td>
                        <td>{{ $llmResponse->response }}</td>
                        <td>{{ $llmResponse->source }}</td>
                        <td>{{ $llmResponse->prompt->sentence }}</td>
                        <td>
                            @if($llmResponse->status == 'pending')
                                <i class="fas fa-clock text-warning" data-toggle="tooltip" data-placement="top" title="Pendiente"></i>
                            @elseif($llmResponse->status == 'executing')
                                <i class="fas fa-spinner fa-spin text-info" data-toggle="tooltip" data-placement="top" title="Ejecutando"></i>
                            @elseif($llmResponse->status == 'success')
                                <i class="fas fa-check-circle text-success" data-toggle="tooltip" data-placement="top" title="Éxito"></i>
                            @elseif($llmResponse->status == 'error')
                                <i class="fas fa-exclamation-circle text-danger" data-toggle="tooltip" data-placement="top" title="Error"></i>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('llm_responses.edit', $llmResponse->id) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('llm_responses.destroy', $llmResponse->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                            <form action="{{ route('llm_responses.regenerate', $llmResponse->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Regenerar</button>
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
        return confirm('¿Estás seguro de que deseas eliminar esta respuesta LLM?');
    }

    // Generación de la datatable
    document.addEventListener('DOMContentLoaded', function() {
        const table = $('#llm-responses-table').DataTable({
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