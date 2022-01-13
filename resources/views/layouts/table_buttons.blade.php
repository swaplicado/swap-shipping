<a href="{{route($crear)}}" class="btn btn-success" style="border-radius: 50%; padding: 5px 10px;"><span class="icon bx bx-plus"></span></a>

<button id="btn_edit" type="button" class="btn btn-warning" style="border-radius: 50%; padding: 5px 10px;">
    <span class="icon bx bx-edit-alt"></span>
</button>

<form id="form_delete" class="d-inline" method="POST">
    @csrf @method("delete")
    <button id="btn_delete" type="button" class="btn btn-danger" style="border-radius: 50%; padding: 5px 10px;">
        <span class="icon bx bx-trash"></span>
    </button>
</form>

<form id="form_recover" class="d-inline" method="POST">
    @csrf @method("put")
    <button id="btn_recover" type="button" class="btn btn-info" style="border-radius: 50%; padding: 5px 10px;">
        <span class="icon bx bx-recycle"></span>
    </button>
</form>

<br>
<br>
<div class="row">
    <div class="col-lg-2">
        <select class="form-select" name="filter" id="filter">
            <option value="0" selected>Activos</option>
            <option value="1">Eliminados</option>
            <option value="2">Todos</option>
        </select>
    </div>
</div>
<br>