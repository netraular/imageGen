@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Elementos</h1>
    <a href="{{ route('elements.create') }}" class="btn btn-primary">Agregar Nuevo Elemento</a>
    <button id="bulk-delete-btn" class="btn btn-danger" style="display:none;">Eliminar Seleccionados</button>
    <br><br>

    <!-- Sección de Filtros -->
    <div id="filters-section" class="card" style="display:none;">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <input type="number" id="filter-id" class="form-control" placeholder="Filtrar por ID">
                </div>
                <div class="col-md-4">
                    <input type="text" id="filter-name" class="form-control" placeholder="Filtrar por Nombre">
                </div>
                <div class="col-md-4">
                    <select id="filter-category" class="form-control">
                        <option value="">Seleccionar Categoría</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <br>
            <button class="btn btn-primary btn-apply-filter" data-column="1">Filtrar por ID</button>
            <button class="btn btn-primary btn-apply-filter" data-column="2">Filtrar por Nombre</button>
            <button class="btn btn-primary btn-apply-filter" data-column="3">Filtrar por Categoría</button>
        </div>
    </div>

    <!-- Botón Funnel -->

    <br><br>

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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        const elementCheckboxes = document.querySelectorAll('.element-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');

        selectAllCheckbox.addEventListener('change', function() {
            elementCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBulkButtons();
        });

        elementCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkButtons);
        });

        function updateBulkButtons() {
            const checkedCheckboxes = document.querySelectorAll('.element-checkbox:checked');
            if (checkedCheckboxes.length > 0) {
                bulkDeleteBtn.style.display = 'inline-block';
            } else {
                bulkDeleteBtn.style.display = 'none';
            }
        }

        bulkDeleteBtn.addEventListener('click', function() {
            const checkedCheckboxes = document.querySelectorAll('.element-checkbox:checked');
            const elementIds = Array.from(checkedCheckboxes).map(checkbox => checkbox.getAttribute('data-id'));

            if (confirm('¿Estás seguro de que quieres eliminar los elementos seleccionados?')) {
                fetch('{{ route('elements.bulkDelete') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ element_ids: elementIds })
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


        // Apply Filters
        const applyFilterButtons = document.querySelectorAll('.btn-apply-filter');
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
            "dom": '<"row"<"col-sm-6"B><"col-sm-6"f><"col-sm-1">>' +
                '<"row"<"col-sm-12"tr>>' +
                '<"row"<"col-sm-5"i><"col-sm-2"l><"col-sm-5"p>>',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            "lengthMenu": [10, 25, 50, 100]
        });

        // Agregar el botón de filtro al DOM
        $('#elements-table_filter').append('<button id="toggle-filters-btn" class="btn btn-outline-secondary global-filter-icon"><i class="bi bi-funnel"></i></button>');

        // Toggle Filters Section
        const toggleFiltersBtn = document.getElementById('toggle-filters-btn');
        const filtersSection = document.getElementById('filters-section');

        toggleFiltersBtn.addEventListener('click', function() {
            filtersSection.style.display = filtersSection.style.display === 'none' ? 'block' : 'none';
        });

        applyFilterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const column = this.getAttribute('data-column');
                let value;

                if (column === '1') {
                    value = document.getElementById('filter-id').value;
                } else if (column === '2') {
                    value = document.getElementById('filter-name').value;
                } else if (column === '3') {
                    value = document.getElementById('filter-category').value;
                }

                table.column(column).search(value).draw();
            });
        });
    });
</script>
@endsection

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)

@section('css')
    <style>
        .global-filter-icon {
            margin-left: 10px;
            padding-left: 8px;
            padding-right: 8px;
        }
    </style>
@endsection