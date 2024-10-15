@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Nueva Categoría</h1>
    <button type="button" class="btn btn-outline-success mb-3" id="add-category-field">
        <i class="bi bi-plus-circle"></i> Añadir Categoría
    </button>
    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div id="category-fields">
            <div class="input-group mb-3">
                <input type="text" name="names[]" class="form-control" placeholder="Nombre de la Categoría" required>
                <input type="text" name="descriptions[]" class="form-control" placeholder="Descripción de la Categoría">
                <input type="number" name="orders[]" class="form-control" placeholder="Orden de la Categoría" value="0">
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-category-field" onclick="removeCategoryField(this)">
                        <i class="bi bi-dash-circle"></i>
                    </button>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>

<script>
    // Definir la función removeCategoryField en el ámbito global
    function removeCategoryField(button) {
        button.closest('.input-group').remove();
        checkAllInputs();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const addCategoryFieldButton = document.getElementById('add-category-field');
        const categoryFieldsContainer = document.getElementById('category-fields');

        addCategoryFieldButton.addEventListener('click', function() {
            addCategoryField();
        });

        categoryFieldsContainer.addEventListener('input', function(event) {
            if (event.target.classList.contains('form-control')) {
                checkAllInputs();
            }
        });

        function addCategoryField() {
            const newField = document.createElement('div');
            newField.className = 'input-group mb-3';
            newField.innerHTML = `
                <input type="text" name="names[]" class="form-control" placeholder="Nombre de la Categoría" required>
                <input type="text" name="descriptions[]" class="form-control" placeholder="Descripción de la Categoría">
                <input type="number" name="orders[]" class="form-control" placeholder="Orden de la Categoría" value="0">
                <div class="input-group-append">
                    <button type="button" class="btn btn-outline-danger remove-category-field" onclick="removeCategoryField(this)">
                        <i class="bi bi-dash-circle"></i>
                    </button>
                </div>
            `;
            categoryFieldsContainer.appendChild(newField);
        }

        function checkAllInputs() {
            const inputs = categoryFieldsContainer.querySelectorAll('.form-control');
            let allFilled = true;
            inputs.forEach(input => {
                if (input.value.trim() === '') {
                    allFilled = false;
                }
            });
            if (allFilled) {
                addCategoryField();
            }
        }
    });
</script>
@endsection