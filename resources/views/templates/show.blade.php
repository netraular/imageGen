@extends('layouts.app')

@section('content_body')
<div class="container">
    <h1>Detalles de la Plantilla</h1>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $template->sentence }}</h5>
            <div class="ml-auto">
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
            </div>
        </div>
        <div class="card-body">
            <h5>Elementos y Categorías Asociadas</h5>
            <div class="tags">
                @foreach($extractedValues as $value)
                    <span class="badge badge-primary">{{ $value }}</span>
                @endforeach
            </div>
            <hr>
            <h5>Prompts Generados</h5>
            @php
                $promptsCount = $template->getPromptsCount();
            @endphp
            <div style="width: 300px; height: 300px;">
                <canvas id="promptsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Prompts Generados</h5>
        </div>
        <div class="card-body">
            <table id="prompts-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Frase</th>
                        <th class="no-sort">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="llmResponsesModal" tabindex="-1" role="dialog" aria-labelledby="llmResponsesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="llmResponsesModalLabel">Respuestas LLM</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Execution ID</th>
                            <th>Respuesta</th>
                            <th>Fecha de Actualización</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="llmResponsesBody">
                        <!-- Aquí se cargarán las respuestas LLM -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
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
        const table = $('#prompts-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('templates.prompts.data', $template->id) }}",
            columns: [
                {data: 'id', name: 'id'},
                {data: 'sentence', name: 'sentence'},
                {
                    data: 'actions', 
                    name: 'actions', 
                    orderable: false, 
                    searchable: false,
                    render: function(data, type, row) {
                        return `<button class="btn btn-sm btn-secondary view-llm-responses" data-prompt-id="${row.id}">Ver Respuestas LLM</button>`;
                    }
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

        $('#prompts-table').on('click', '.view-llm-responses', function() {
            const promptId = $(this).data('prompt-id');
            $.ajax({
                url: "{{ route('llm_responses.by_prompt') }}",
                type: 'GET',
                data: { prompt_id: promptId },
                success: function(data) {
                    const llmResponsesBody = $('#llmResponsesBody');
                    llmResponsesBody.empty();
                    data.data.forEach(function(response) {
                        llmResponsesBody.append(`
                            <tr>
                                <td>${response.execution_id}</td>
                                <td>${response.response}</td>
                                <td>${response.updated_at}</td>
                                <td>${response.status}</td>
                            </tr>
                        `);
                    });
                    $('#llmResponsesModal').modal('show');
                }
            });
        });

        // Datos para el gráfico
        const promptsCount = @json($promptsCount);
        const data = {
            labels: ['Success', 'Error', 'Other'],
            datasets: [{
                data: [promptsCount.success, promptsCount.error, promptsCount.other],
                backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
                hoverOffset: 4
            }]
        };

        // Configuración del gráfico
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
                }
            }
        };

        // Crear el gráfico
        const ctx = document.getElementById('promptsChart').getContext('2d');
        new Chart(ctx, config);
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