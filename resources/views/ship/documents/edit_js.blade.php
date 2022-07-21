<script type="text/javascript">
    function GlobalData () {
        this.idDocument = <?php echo json_encode($idDocument) ?>;
        this.oData = <?php echo json_encode($oObjData) ?>;
        this.lVehicles = <?php echo json_encode($lVehicles) ?>;
        this.lVehicleKeys = <?php echo json_encode($lVehicleKeys) ?>;
        this.lTrailers = <?php echo json_encode($lTrailers) ?>;
        this.lSuburbs = <?php echo json_encode($lSuburbs) ?>;
        this.lFigures = <?php echo json_encode($lFigures) ?>;
        this.oVehicle = <?php echo json_encode($oVehicle) ?>;
        this.oTrailer = <?php echo json_encode($oTrailer) ?>;
        this.bShipCfg = <?php echo json_encode(env('WITH_CFG_ORIGIN')) ?>;
        this.bCustomAtts = <?php echo json_encode(env('WITH_CUSTOM_ATTRIBUTES')) ?>;
        this.oFigure = <?php echo json_encode($oFigure) ?>;
        this.lPayMethods = <?php echo json_encode($lPayMethods) ?>;
        this.lPayForms = <?php echo json_encode($lPayForms) ?>;
        this.lCurrencies = <?php echo json_encode($lCurrencies) ?>;
        this.lCarrierSeries = <?php echo json_encode($lCarrierSeries) ?>;
        this.enablePay = <?php echo json_encode($oConfigurations->habilitaPago) ?>;

        this.oData.serie = this.oData.serie == '' && this.lCarrierSeries.length == 1 ? this.lCarrierSeries[0].prefix : this.oData.serie;
        this.oVehicle = this.oVehicle.id_vehicle == undefined && this.lVehicles.length == 1 ? this.lVehicles[0] : this.oVehicle;
        this.oFigure = this.oFigure.id_trans_figure == undefined && this.lFigures.length == 1 ? this.lFigures[0] : this.oFigure;
    }
    
    var oServerData = new GlobalData();
    
</script>
<script src="{{ asset('js/myapp/documents/SDocumentsApp.js') }}"></script>