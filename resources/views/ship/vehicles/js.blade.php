<script>
  class TransCfg {
    constructor() {
      this.trans_part_id = 0;
      this.veh_tra_id = 0;
      this.figure_type_id = 2;
      this.figure_trans_id = 0;
    }
  }
</script>
<script>
    var app = new Vue({
      el: '#vehiclesApp',
      data: {
        bIsOwn: <?php echo json_encode(!isset($data) ? true : $data->is_own) ?>,
        lTransParts: <?php echo json_encode($lTransParts) ?>,
        lFigures: <?php echo json_encode($lFigures) ?>,
        oTransCfg: <?php echo json_encode(!isset($oTransCfg) || is_null($oTransCfg) ? null : $oTransCfg) ?>
      },
      mounted() {
        if (this.oTransCfg == null) {
          this.oTransCfg = new TransCfg();
        }
      },
      methods: {
        
      }
    })
</script>