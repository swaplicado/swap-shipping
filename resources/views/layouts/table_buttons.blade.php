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

@if (isset($moreButtons))
    @foreach ($moreButtons as $button)
        <button id="{{ $button['id'] }}" title="{{ $button['title'] }}" class="btn btn-{{ $button['class'] }}" style="border-radius: 50%; padding: 5px 10px;">
            <span class="icon bx {{ $button['icon'] }}"></span>
        </button>
    @endforeach
@endif

{{-- <a style="border-radius: 50%; padding: 5px 10px;" class="btn btn-primary" href="#" title="Descagar XML">
    <span class="icon bx bx-download"></span>
</a>
<a style="border-radius: 50%; padding: 5px 10px;" class="btn btn-secondary" href="#" title="Descagar PDF">
    <span class="icon bx bxs-file-pdf"></span>
</a> --}}

<br>
<br>
<div class="col-lg-2" style="float: left;">
    <select class="form-select" name="isDeleted" id="isDeleted">
        <option value="0" selected>Activos</option>
        <option value="1">Eliminados</option>
        <option value="2">Todos</option>
    </select>
</div>

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