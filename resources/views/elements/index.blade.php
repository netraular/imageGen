@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Elementos</h1>
    <a href="{{ route('elements.create') }}" class="btn btn-primary">Agregar Nuevo Elemento</a>
    <!-- <button id="bulk-edit-btn" class="btn btn-warning" style="display:none;">Editar Seleccionados</button> -->
    <button id="bulk-delete-btn" class="btn btn-danger" style="display:none;">Eliminar Seleccionados</button>
    <br><br>
    <table class="table">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Acciones</th>
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
        // const bulkEditBtn = document.getElementById('bulk-edit-btn');
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
                // bulkEditBtn.style.display = 'inline-block';
                bulkDeleteBtn.style.display = 'inline-block';
            } else {
                // bulkEditBtn.style.display = 'none';
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
    });
</script>
@endsection