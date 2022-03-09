<!-- Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Cancelación de CFDI</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="">Seleccione motivo de cancelación:</label>
                <select class="form-control" name="cancel_reason" id="cancel_reason" onchange="onChangeReason()">
                    @foreach ($lCancelReasons as $oReason)
                        <option value="{{ $oReason->id_reason }}" {{ $oReason->id_reason == 2 ? 'selected' : ''}}>
                            {{ $oReason->reason_code.' - '.$oReason->reason }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div id="uuids_div" style="display: none">
                <br>
                <div class="form-group">
                  <label for="uuid_rel">Seleccione documento relacionado:</label>
                  <select class="form-control" name="uuid_rel" id="uuid_rel">
                    @foreach ($lUuids as $oUuid)
                        <option value="{{ $oUuid->id_document }}">
                            {{ $oUuid->requested_at.' / '.$oUuid->serie.'-'.str_pad($oUuid->folio, 6, "0", STR_PAD_LEFT).' / '.$oUuid->uuid }}
                        </option>
                    @endforeach
                  </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Regresar</button>
          <button type="button" class="btn btn-danger" id="id_cancel_confirm">Cancelar CFDI</button>
        </div>
      </div>
    </div>
  </div>