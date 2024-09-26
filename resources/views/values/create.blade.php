@extends('layouts.app')

@section('content_body')
<div class="container">
    <h1>Agregar Nuevos Conceptos</h1>
    <button type="button" class="btn btn-outline-success mb-3" id="add-concept-field">
        <i class="bi bi-plus-circle"></i> 
    </button>
    <form action="{{ route('values.store') }}" method="POST">
        @csrf
        <div class="form-group" id="concept-fields">
            <div class="input-group mb-3">
                <input type="text" name="names[]" class="form-control" placeholder="Nombre del Concepto" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-concept-field" onclick="removeConceptField(this)">
                        <i class="bi bi-dash-circle"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="category_id">Categoría</label>
            <select name="category_id" id="category_id" class="form-control" required>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="parent_id">Concepto Padre (Opcional)</label>
            <select name="parent_id" id="parent_id" class="form-control">
                <option value="">Ninguno</option>
                @foreach($values as $value)
                <option value="{{ $concept->id }}">{{ $concept->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection

@push('js')
<script>
    // Definir la función removeConceptField en el ámbito global
    function removeConceptField(button) {
        button.closest('.input-group').remove();
        checkAllInputs();
    }

    $(document).ready(function() {
        const addConceptFieldButton = document.getElementById('add-concept-field');
        const conceptFieldsContainer = document.getElementById('concept-fields');

        addConceptFieldButton.addEventListener('click', function() {
            addConceptField();
        });

        conceptFieldsContainer.addEventListener('input', function(event) {
            if (event.target.classList.contains('form-control')) {
                checkAllInputs();
            }
        });

        function addConceptField() {
            const newField = document.createElement('div');
            newField.className = 'input-group mb-3';
            newField.innerHTML = `
                <input type="text" name="names[]" class="form-control" placeholder="Nombre del Concepto" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-concept-field" onclick="removeConceptField(this)">
                        <i class="bi bi-dash-circle"></i>
                    </button>
                </div>
            `;
            conceptFieldsContainer.appendChild(newField);
        }

        function checkAllInputs() {
            const inputs = conceptFieldsContainer.querySelectorAll('.form-control');
            let allFilled = true;
            inputs.forEach(input => {
                if (input.value.trim() === '') {
                    allFilled = false;
                }
            });
            if (allFilled) {
                addConceptField();
            }
        }
    });
</script>
@endpush

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
@endpush