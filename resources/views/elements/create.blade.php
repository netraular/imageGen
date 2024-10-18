@extends('layouts.app')

@section('content_body')
<div class="container">
    <h1>Agregar Nuevos Elementos</h1>
    
    <form action="{{ route('elements.store') }}" method="POST">
    @csrf
        <div class="form-group">
            <label for="input_mode">Modo de entrada:</label>
            <select id="input_mode" name="input_mode" class="form-control">
                <option value="individual" selected>Individual</option>
                <option value="bulk">En conjunto</option>
            </select>
        </div>

        <button type="button" class="btn btn-outline-success mb-3" id="add-element-field" style="display: block;">
            <i class="bi bi-plus-circle"></i> 
        </button>

        <div class="form-group" id="element-fields">
            <div class="input-group mb-3">
                <input type="text" name="names[]" class="form-control" placeholder="Nombre del Elemento" >
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-element-field" onclick="removeElementField(this)">
                        <i class="bi bi-dash-circle"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group" id="bulk-input" style="display: none;">
            <label for="bulk_names">Elementos (separados por el separador seleccionado):</label>
            <textarea name="bulk_names" id="bulk_names" class="form-control" rows="5"></textarea>
            <label for="separator">Seleccionar separador:</label>
            <select name="separator" id="separator" class="form-control">
                <option value="newline">Salto de línea (Enter)</option>
                <option value="comma">Coma (,)</option>
                <option value="semicolon">Punto y coma (;)</option>
                <option value="space">Espacio ( )</option>
                <option value="tab">Tabulación (Tab)</option>
                <option value="json">Json</option>
            </select>
        </div>
        <div class="form-group">
            <label for="category_id">Categoría</label>
            <select name="category_id" id="category_id" class="form-control" required>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="parent_category_id">Categoría del Elemento Padre (Opcional)</label>
            <select name="parent_category_id" id="parent_category_id" class="form-control" onchange="loadParentElements(this.value)">
                <option value="">Ninguno</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="parent_id">Elemento Padre (Opcional)</label>
            <select name="parent_id" id="parent_id" class="form-control">
                <option value="">Ninguno</option>
            </select>
            @error('parent_id')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection

@push('js')
<script>
    // Definir la función removeElementField en el ámbito global
    function removeElementField(button) {
        button.closest('.input-group').remove();
        checkAllInputs();
    }

    // Definir la función loadParentElements en el ámbito global
    function loadParentElements(categoryId) {
        const parentIdSelect = document.getElementById('parent_id');
        parentIdSelect.innerHTML = '<option value="">Cargando...</option>';

        if (categoryId) {
            fetch(`/elements/parent-elements/${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    parentIdSelect.innerHTML = '<option value="">Ninguno</option>';
                    data.forEach(element => {
                        const option = document.createElement('option');
                        option.value = element.id;
                        option.text = element.name;
                        parentIdSelect.appendChild(option);
                    });
                });
        } else {
            parentIdSelect.innerHTML = '<option value="">Ninguno</option>';
        }
    }

    $(document).ready(function() {
        const addElementFieldButton = document.getElementById('add-element-field');
        const elementFieldsContainer = document.getElementById('element-fields');
        const bulkInputContainer = document.getElementById('bulk-input');
        const inputModeSelect = document.getElementById('input_mode');

        inputModeSelect.addEventListener('change', function() {
            if (inputModeSelect.value === 'individual') {
                elementFieldsContainer.style.display = 'block';
                bulkInputContainer.style.display = 'none';
                addElementFieldButton.style.display = 'block';
            } else {
                elementFieldsContainer.style.display = 'none';
                bulkInputContainer.style.display = 'block';
                addElementFieldButton.style.display = 'none';
            }
        });

        addElementFieldButton.addEventListener('click', function() {
            addElementField();
        });

        elementFieldsContainer.addEventListener('input', function(event) {
            if (event.target.classList.contains('form-control')) {
                checkAllInputs();
            }
        });

        function addElementField() {
            const newField = document.createElement('div');
            newField.className = 'input-group mb-3';
            newField.innerHTML = `
                <input type="text" name="names[]" class="form-control" placeholder="Nombre del Elemento">
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-element-field" onclick="removeElementField(this)">
                        <i class="bi bi-dash-circle"></i>
                    </button>
                </div>
            `;
            elementFieldsContainer.appendChild(newField);
        }

        function checkAllInputs() {
            const inputs = elementFieldsContainer.querySelectorAll('.form-control');
            let allFilled = true;
            inputs.forEach(input => {
                if (input.value.trim() === '') {
                    allFilled = false;
                }
            });
            if (allFilled) {
                addElementField();
            }
        }
    });
</script>
@endpush