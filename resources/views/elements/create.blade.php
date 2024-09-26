@extends('layouts.app')

@section('content_body')
<div class="container">
    <h1>Agregar Nuevos Elementos</h1>
    <button type="button" class="btn btn-outline-success mb-3" id="add-element-field">
        <i class="bi bi-plus-circle"></i> 
    </button>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('elements.store') }}" method="POST">
        @csrf
        <div class="form-group" id="element-fields">
            @if ($errors->has('names.*'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->get('names.*') as $error)
                            <li>{{ $error[0] }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="input-group mb-3">
                <input type="text" name="names[]" class="form-control" placeholder="Nombre del Elemento" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-element-field" onclick="removeElementField(this)">
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
            @error('category_id')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="parent_id">Elemento Padre (Opcional)</label>
            <select name="parent_id" id="parent_id" class="form-control">
                <option value="">Ninguno</option>
                @foreach($elements as $element)
                <option value="{{ $element->id }}">{{ $element->name }}</option>
                @endforeach
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

    $(document).ready(function() {
        const addElementFieldButton = document.getElementById('add-element-field');
        const elementFieldsContainer = document.getElementById('element-fields');

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
                <input type="text" name="names[]" class="form-control" placeholder="Nombre del Elemento" required>
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

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
@endpush