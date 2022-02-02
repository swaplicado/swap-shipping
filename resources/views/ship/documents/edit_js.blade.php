<script type="text/javascript">
    function GlobalData () {
        this.idDocument = <?php echo json_encode($idDocument) ?>;
        this.oData = <?php echo json_encode($oObjData) ?>;
        this.lVehicles = <?php echo json_encode($lVehicles) ?>;
        this.lTrailers = <?php echo json_encode($lTrailers) ?>;
        this.lFigures = <?php echo json_encode($lFigures) ?>;
        this.oVehicle = <?php echo json_encode($oVehicle) ?>;
        this.oFigure = <?php echo json_encode($oFigure) ?>;
        this.lPayMethods = <?php echo json_encode($lPayMethods) ?>;
        this.lPayForms = <?php echo json_encode($lPayForms) ?>;
        this.lCurrencies = <?php echo json_encode($lCurrencies) ?>;
        this.lCarrierSeries = <?php echo json_encode($lCarrierSeries) ?>;
    }
    
    var oServerData = new GlobalData();
    console.log(oServerData);
</script>
<script src="{{ asset('js/myapp/documents/SDocumentsApp.js') }}"></script>