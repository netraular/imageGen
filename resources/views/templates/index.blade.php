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
                        <th>Status</th>
                        <th class="no-sort">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                    <tr>
                        <td>{{ $template->id }}</td>
                        <td>{!! nl2br(e($template->sentence)) !!}</td>
                        <td>
                            <div style="width: 150px; height: 150px;">
                                <canvas class="promptsChart" data-success="{{ $template->getPromptsCount()['success'] }}" data-error="{{ $template->getPromptsCount()['error'] }}" data-other="{{ $template->getPromptsCount()['other'] }}"></canvas>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('templates.show', $template->id) }}" class="btn btn-sm btn-info">Ver</a>
                            <a href="{{ route('templates.edit', $template->id) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('templates.destroy', $template->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                            
                            @if($template->prompts_count == 0)
                                <form action="{{ route('templates.generate') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="template_id" value="{{ $template->id }}">
                                    <button type="submit" class="btn btn-sm btn-success">Generar Prompts</button>
                                </form>
                            @endif

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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
                { "orderable": false, "targets": 'no-sort' },
                { "width": "150px", "targets": 2 } // Ajustar el ancho de la columna "Status"
            ],
            "dom": '<"row table-container"<"col-sm-6"B><"col-sm-6"f><"col-sm-1">>' +
                    '<"row table-row-with-margin"<"col-sm-12 px-0"tr>>' +
                    '<"row"<"col-sm-5"i><"col-sm-2"l><"col-sm-5"p>>',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
        });

        // Crear gráficos para cada fila
        $('.promptsChart').each(function() {
            const ctx = this.getContext('2d');
            const success = parseInt($(this).data('success'));
            const error = parseInt($(this).data('error'));
            const other = parseInt($(this).data('other'));

            const data = {
                labels: ['Success', 'Error', 'Other'],
                datasets: [{
                    data: [success, error, other],
                    backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                    hoverOffset: 4
                }]
            };

            const config = {
                type: 'doughnut',
                data: data,
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(2);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '50%', // Hace que el gráfico sea un donut en lugar de un pie
                    responsive: true,
                    maintainAspectRatio: false
                }
            };

            new Chart(ctx, config);
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