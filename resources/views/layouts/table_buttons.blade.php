@if(isset($crear))
    <a href="{{route($crear)}}" class="btn btn-success" style="border-radius: 50%; padding: 5px 10px;" title="Nuevo registro"><span class="icon bx bx-plus"></span></a>
@endif

<button id="btn_edit" type="button" class="btn btn-warning" style="border-radius: 50%; padding: 5px 10px;" title="Editar registro">
    <span class="icon bx bx-edit-alt"></span>
</button>

@if(!isset($withOutEliminar))
    <form id="form_delete" class="d-inline" method="POST">
        @csrf @method("delete")
        <button id="btn_delete" type="button" class="btn btn-danger" style="border-radius: 50%; padding: 5px 10px;" title="Eliminar registro">
            <span class="icon bx bx-trash"></span>
        </button>
    </form>
@endif

@if(!isset($withOutRecuperar))
    <form id="form_recover" class="d-inline" method="POST">
        @csrf @method("put")
        <button id="btn_recover" type="button" class="btn btn-info" style="border-radius: 50%; padding: 5px 10px;" title="Recuperar registro">
            <span class="icon bx bx-recycle"></span>
        </button>
    </form>
@endif

@if (isset($moreButtons))
    @foreach ($moreButtons as $button)
        <button id="{{ $button['id'] }}" title="{{ $button['title'] }}" class="btn btn-{{ $button['class'] }}" style="border-radius: 50%; padding: 5px 10px;">
            <span class="icon bx {{ $button['icon'] }}"></span>
        </button>
    @endforeach
@endif

<br>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-2" style="float: left;">
            <select class="form-select" name="isDeleted" id="isDeleted">
                <option value="0" selected>Activos</option>
                <option value="1">Eliminados</option>
                <option value="2">Todos</option>
            </select>
        </div>
    @if (isset($moreFilters))
        @foreach ($moreFilters as $filter)
            @php
                echo $filter
            @endphp
        @endforeach
    @endif
    </div>
</div>
<br>

@if (isset($filterCarrier))
    @if ($filterCarrier)
        <div class="col-lg-2" style="float: left">
            <select class="form-select" name="carriers" id="carriers">
                <option value="0" selected>Transportista</option>
                @foreach ($carriers as $c)
                    <option value="{{$c->id_carrier}}">{{$c->fullname}}</option>
                @endforeach
            </select>
        </div>
    @endif
@endif