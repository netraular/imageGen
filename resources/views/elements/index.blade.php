@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Elementos</h1>
    <a href="{{ route('elements.create') }}" class="btn btn-primary">Agregar Nuevo Elemento</a>
    <button id="bulk-delete-btn" class="btn btn-danger" style="display:none;">Eliminar Seleccionados</button>
    <br><br>

    <!-- Sección de Filtros -->
    <div id="filters-section" class="card" style="display:none;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Filtros</h5>
            <button id="close-filters-btn" class="btn btn-link text-danger"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="card-body">
            <div class="filter-group">
                <label for="filter-id">Filtrar por ID</label>
                <input type="number" id="filter-id" class="form-control" placeholder="ID">
            </div>
            <div class="filter-group">
                <label for="filter-name">Filtrar por Nombre</label>
                <input type="text" id="filter-name" class="form-control" placeholder="Nombre">
            </div>
            <div class="filter-group">
                <label for="filter-category">Filtrar por Categoría</label>
                <select id="filter-category" class="form-control">
                    <option value="">Seleccionar Categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <button id="apply-all-filters-btn" class="btn btn-primary mt-3">Aplicar Todos los Filtros</button>
        </div>
    </div>

    <!-- Tabla de Elementos -->
    <table id="elements-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th class="no-sort"><input type="checkbox" id="select-all"></th>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th class="no-sort">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($elements as $element)
            <tr>
                <td><input type="checkbox" class="element-checkbox" data-id="{{ $element->id }}"></td>
                <td>{{ $element->id }}</td>
                <td>{{ $element->name }}</td>
                <td>{{ $element->category->name }}</td>
                <td>
                    <a href="{{ route('elements.edit', $element->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('elements.destroy', $element->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        const elementCheckboxes = document.querySelectorAll('.element-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
        let selectedElementIds = [];

        // Función para actualizar el estado de los botones masivos
        function updateBulkButtons() {
            if (selectedElementIds.length > 0) {
                bulkDeleteBtn.style.display = 'inline-block';
            } else {
                bulkDeleteBtn.style.display = 'none';
            }
        }

        // Función para manejar el cambio en los checkboxes
        function handleCheckboxChange(checkbox) {
            const elementId = checkbox.getAttribute('data-id');
            if (checkbox.checked) {
                if (!selectedElementIds.includes(elementId)) {
                    selectedElementIds.push(elementId);
                }
            } else {
                const index = selectedElementIds.indexOf(elementId);
                if (index > -1) {
                    selectedElementIds.splice(index, 1);
                }
            }
            updateBulkButtons();
        }

        // Evento para el checkbox de "Seleccionar todos"
        selectAllCheckbox.addEventListener('change', function() {
            elementCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
                handleCheckboxChange(checkbox);
            });
        });

        // Evento para cada checkbox individual
        elementCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                handleCheckboxChange(checkbox);
            });
        });

        // Evento para el botón de eliminación masiva
        bulkDeleteBtn.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que quieres eliminar los elementos seleccionados?')) {
                fetch('{{ route('elements.bulkDelete') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ element_ids: selectedElementIds })
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          location.reload();
                      } else {
                          alert('Hubo un error al eliminar los elementos.');
                      }
                  });
            }
        });
    });
</script>
<script>
    //Generación de la datatable
    document.addEventListener('DOMContentLoaded', function() {
        const table = $('#elements-table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "paginate": {
                    "previous": "&#9664;", 
                    "next": "&#9654;" 
                },
                "lengthMenu": "Mostrar _MENU_"
            },
            "columnDefs": [
                { "orderable": false, "targets": 'no-sort' }
            ],
            "order": [[1, "asc"]],
            "dom": '<"row"<"col-sm-6"B><"col-sm-6"f><"col-sm-1">>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-5"i><"col-sm-2"l><"col-sm-5"p>>',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            "lengthMenu": [10, 25, 50, 100]
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtros
        $('#elements-table_filter').append('<button id="toggle-filters-btn" class="btn btn-outline-secondary global-filter-icon"><i class="bi bi-funnel"></i></button>');
        
        const closeFiltersBtn = document.getElementById('close-filters-btn');

        closeFiltersBtn.addEventListener('click', function() {
            filtersSection.style.display = 'none';
        });

        const applyAllFiltersBtn = document.getElementById('apply-all-filters-btn');

        // Toggle Filters Section
        const toggleFiltersBtn = document.getElementById('toggle-filters-btn');
        const filtersSection = document.getElementById('filters-section');

        toggleFiltersBtn.addEventListener('click', function() {
            filtersSection.style.display = filtersSection.style.display === 'none' ? 'block' : 'none';
        });

        applyAllFiltersBtn.addEventListener('click', function() {
            const filterId = document.getElementById('filter-id').value;
            const filterName = document.getElementById('filter-name').value;
            const filterCategory = document.getElementById('filter-category').value;

            table.column(1).search(filterId).draw();
            table.column(2).search(filterName).draw();
            table.column(3).search(filterCategory).draw();
        });
    });
</script>
@endsection

@section('css')
    <style>
        .global-filter-icon {
            margin-left: 10px;
            padding-left: 8px;
            padding-right: 8px;
        }
    </style>
@endsection