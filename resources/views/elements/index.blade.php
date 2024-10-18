@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Elementos</h1>
    <a href="{{ route('elements.create') }}" class="btn  btn-primary">Agregar Nuevo Elemento</a>
    <button id="bulk-delete-btn" class="btn btn-danger" style="display:none;">Eliminar Seleccionados</button>
    <br><br>

    <!-- Sección de Filtros -->
    <div id="filters-section" class="card" style="display:none;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Filtros</h5>
            <div class="ml-auto">
                <button id="clear-filters-btn" class="btn btn-link text-secondary">Limpiar Filtros</button>
                <button id="close-filters-btn" class="btn btn-link text-danger"><i class="bi bi-x-lg"></i></button>
            </div>
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
        </div>
    </div>


    <!-- Tabla de Elementos -->
    <div class="card">
        <div class="card-body">
            <table id="elements-table" class="table table-striped table-bordered table-hover">
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
                            <a href="{{ route('elements.edit', $element->id) }}" class="btn btn-sm btn-warning">Editar</a>
                            <form action="{{ route('elements.destroy', $element->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete(event)">
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
    //Checkbox select events
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        const elementCheckboxes = document.querySelectorAll('.element-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
        let selectedElementIds = [];

        // Función para actualizar el estado de todos los elementos seleccionados
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
                "lengthMenu": "Mostrar _MENU_",
            },
            "columnDefs": [
                { "orderable": false, "targets": 'no-sort' }
            ],
            "order": [[1, "asc"]],
            "dom": '<"row table-container"<"col-sm-6"B><"col-sm-6"f><"col-sm-1">>' +
                    '<"row table-row-with-margin"<"col-sm-12 px-0"tr>>' +
                    '<"row"<"col-sm-5"i><"col-sm-2"l><"col-sm-5"p>>',

            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        });
        
        // Filtros
        $('#elements-table_filter').append(`
            <button id="toggle-filters-btn" class="btn btn-outline-secondary global-filter-icon position-relative">
                <i id="filter-icon" class="bi bi-funnel"></i>
                <span id="filter-counter" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;">0</span>
            </button>
        `);
        
        const toggleFiltersBtn = document.getElementById('toggle-filters-btn');
        const filtersSection = document.getElementById('filters-section');
        const filterIcon = document.getElementById('filter-icon');
        const filterCounter = document.getElementById('filter-counter');
        const filterIdInput = document.getElementById('filter-id');
        const filterNameInput = document.getElementById('filter-name');
        const filterCategorySelect = document.getElementById('filter-category');
        const clearFiltersBtn = document.getElementById('clear-filters-btn');
        let activeFilters = 0;

        // Función para contar y mostrar el número de filtros activos
        function updateFilterCounter() {
            activeFilters = 0;

            const filterId = document.getElementById('filter-id').value.trim();
            const filterName = document.getElementById('filter-name').value.trim();
            const filterCategory = document.getElementById('filter-category').value.trim();

            // Si hay valores en los filtros, aumentamos el contador de filtros activos
            if (filterId !== "") activeFilters++;
            if (filterName !== "") activeFilters++;
            if (filterCategory !== "") activeFilters++;

            // Actualizamos el icono y el contador
            if (activeFilters > 0) {
                filterIcon.classList.remove('bi-funnel');
                filterIcon.classList.add('bi-funnel-fill');
                filterCounter.textContent = activeFilters;
                filterCounter.style.display = 'inline';  // Muestra el contador
            } else {
                filterIcon.classList.remove('bi-funnel-fill');
                filterIcon.classList.add('bi-funnel');
                filterCounter.style.display = 'none';  // Oculta el contador
            }
        }


        // Función para aplicar los filtros a la tabla
        function applyFilters() {
            const filterId = document.getElementById('filter-id').value;
            const filterName = document.getElementById('filter-name').value;
            const filterCategory = document.getElementById('filter-category').value;

            // Aplicar los filtros a las columnas de la DataTable
            table.column(1).search(filterId).draw();
            table.column(2).search(filterName).draw();
            table.column(3).search(filterCategory).draw();

            // Actualizamos el contador de filtros activos
            updateFilterCounter();
        }

        // Eventos para los campos de filtro para que apliquen los filtros en tiempo real
        filterIdInput.addEventListener('input', applyFilters);
        filterNameInput.addEventListener('input', applyFilters);
        filterCategorySelect.addEventListener('change', applyFilters);

        // Evento para limpiar todos los filtros
        clearFiltersBtn.addEventListener('click', function() {
            // Limpiar los campos de filtro
            filterIdInput.value = '';
            filterNameInput.value = '';
            filterCategorySelect.value = '';

            // Aplicar filtros vacíos (lo que efectivamente limpia los filtros)
            applyFilters();
        });
        
        // Toggle Filters Section
        toggleFiltersBtn.addEventListener('click', function() {
            filtersSection.style.display = filtersSection.style.display === 'none' ? 'block' : 'none';
        });

        // Cerrar el panel de filtros
        document.getElementById('close-filters-btn').addEventListener('click', function() {
        filtersSection.style.display = 'none';
    });
});
</script>
<script>
    function confirmDelete(event) {
        if (!confirm('¿Estás seguro de que quieres eliminar este elemento?')) {
            event.preventDefault(); // Evita que el formulario se envíe si el usuario cancela
        }
    }
</script>
@endsection

@section('css')
    <style>
        .global-filter-icon {
            margin-left: 10px;
            padding-left: 8px;
            padding-right: 8px;
        }
        .table-row-with-margin {
            margin-left: -16px;
            margin-right: -16px;
        }
        .global-filter-icon {
            margin-left: 10px;
            padding-left: 8px;
            padding-right: 8px;
        }

        .badge {
            font-size: 0.75rem;
        }
        .card-body{
            overflow-x:auto;
        }
    </style>
@endsection