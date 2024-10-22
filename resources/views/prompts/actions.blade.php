<a href="{{ route('prompts.edit', $prompt->id) }}" class="btn btn-sm btn-warning">Editar</a>
<form action="{{ route('prompts.destroy', $prompt->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
</form>