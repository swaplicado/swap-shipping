<div class="accordion" id="accordionCfdi">
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingHeader">
        <button id="btnHeader" class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHeader" aria-expanded="true" aria-controls="collapseHeader">
          Comprobante
        </button>
      </h2>
      <div id="collapseHeader" class="accordion-collapse collapse show" aria-labelledby="headingHeader" data-bs-parent="#accordionCfdi">
        <div class="accordion-body">
            @include('ship.documents.header')
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingEmisor">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEmisor" aria-controls="collapseEmisor">
          Emisor
        </button>
      </h2>
      <div id="collapseEmisor" class="accordion-collapse collapse" aria-labelledby="headingEmisor" data-bs-parent="#accordionCfdi">
        <div class="accordion-body">
            @include('ship.documents.emisor')
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingReceptor">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReceptor" aria-controls="collapseReceptor">
          Receptor
        </button>
      </h2>
      <div id="collapseReceptor" class="accordion-collapse collapse" aria-labelledby="headingReceptor" data-bs-parent="#accordionCfdi">
        <div class="accordion-body">
            @include('ship.documents.receptor')
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingConcepts">
        <button id="btnConcepts" class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseConcepts" aria-expanded="false" aria-controls="collapseConcepts">
          Conceptos
        </button>
      </h2>
      <div id="collapseConcepts" class="accordion-collapse collapse" aria-labelledby="headingConcepts" data-bs-parent="#accordionCfdi">
        <div class="accordion-body">
            @include('ship.documents.concepts')
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingTaxes">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTaxes" aria-expanded="false" aria-controls="collapseTaxes">
          Impuestos
        </button>
      </h2>
      <div id="collapseTaxes" class="accordion-collapse collapse" aria-labelledby="headingTaxes" data-bs-parent="#accordionCfdi">
        <div class="accordion-body">
            @include('ship.documents.taxes')
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingComplement">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseComplement" aria-expanded="false" aria-controls="collapseComplement">
          Complemento Carta Porte (Encabezado)
        </button>
      </h2>
      <div id="collapseComplement" class="accordion-collapse collapse" aria-labelledby="headingComplement" data-bs-parent="#accordionCfdi">
        <div class="accordion-body">
            @include('ship.documents.complemento2_0.complement')
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingLocations">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLocations" aria-expanded="false" aria-controls="collapseLocations">
          Ubicaciones
        </button>
      </h2>
      <div id="collapseLocations" class="accordion-collapse collapse" aria-labelledby="headingLocations" data-bs-parent="#accordionCfdi">
        <div class="accordion-body">
            @include('ship.documents.complemento2_0.locations')
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingMerch">
        <button id="btnMerch" class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMerch" aria-expanded="false" aria-controls="collapseMerch">
          Mercancías
        </button>
      </h2>
      <div id="collapseMerch" class="accordion-collapse collapse" aria-labelledby="headingMerch" data-bs-parent="#accordionCfdi">
        <div class="accordion-body">
            @include('ship.documents.complemento2_0.merchandise')
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingTransport">
        <button id="btnTransport" class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTransport" aria-expanded="false" aria-controls="collapseTransport">
          Transporte
        </button>
      </h2>
      <div id="collapseTransport" class="accordion-collapse collapse" aria-labelledby="headingTransport" data-bs-parent="#accordionCfdi">
        <div class="accordion-body">
            @include('ship.documents.complemento2_0.transport')
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingFigure">
        <button id="btnFigure" class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFigure" aria-expanded="false" aria-controls="collapseFigure">
          Figura de transporte
        </button>
      </h2>
      <div id="collapseFigure" class="accordion-collapse collapse" aria-labelledby="headingFigure" data-bs-parent="#accordionCfdi">
        <div class="accordion-body">
            @include('ship.documents.complemento2_0.figure')
        </div>
      </div>
    </div>
</div>


