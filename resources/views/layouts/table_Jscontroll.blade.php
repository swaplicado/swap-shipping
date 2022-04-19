<script>
    var table = '';
    $(document).ready(function () {
        
        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                let registerVal = parseInt( $('#isDeleted').val(), 10 );
                let isDeleted = 0;

                switch (registerVal) {
                    case 0:
                        isDeleted = parseInt( data[1] );
                        return isDeleted === 0;
                        
                    case 1:
                        isDeleted = parseInt( data[1] );
                        return ! (isDeleted === 0);

                    case 2:
                        return true;

                    default:
                        break;
                }

                return false;
            }
        );

        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                let carrier = parseInt( $('#carriers').val(), 10 );
                let carrierId = 0;

                if(carrier != 0 && !isNaN(carrier)){
                    carrierId = parseInt( data[2] );
                    return carrierId == carrier;
                }

                return true;
            }
        );

        table = $('#{{$table_id}}').DataTable({
            language: {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
            columnDefs: [
                {
                    "targets": [0,1,2],
                    "visible": false,
                    "searchable": true
                }
            ],
            "colReorder": true,
            "initComplete": function(){ 
                $("#{{$table_id}}").show(); 
            }
        });

        $('#{{$table_id}} tbody').on('click', 'tr', function () {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            }
            else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });

        $('#maincontainer').click(function () {
            table.$('tr.selected').removeClass('selected');
        });

        $('#id_sign').click(function () {
            if (table.row('.selected').data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }

            var id = table.row('.selected').data()[0];
            var url = '{{ isset($signRoute) ? route($signRoute, ":id") : "#" }}';
            url = url.replace(':id',id);
            window.location.href = url;
        });

        $('#id_cancel').click(function () {
            if (table.row('.selected').data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }

            var idCancel = table.row('.selected').data()[0];

            $("#cancelModal").modal('show');
        });

        $('#id_cancel_confirm').click(function () {
            let e = document.getElementById("cancel_reason");
            let uuidss = document.getElementById("uuid_rel")
            let ref = uuidss.options[uuidss.selectedIndex].value;
            SGui.showWaiting(8000);

            if (table.row('.selected').data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }

            let idCancel = table.row('.selected').data()[0];
            if (idCancel != undefined && urlCancel != undefined) {
                url = urlCancel.replace(':id', idCancel).replace(':id_reason', e.value).replace(':ref', ref);
                window.location.href = url;
            }
        });
        
        $('#btn_edit').click(function () {
            if (table.row('.selected').data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }

            var id = table.row('.selected').data()[0];
            var url = '{{route($editar, ":id")}}';
            url = url.replace(':id',id);
            window.location.href = url;
        });

        $('#btn_delete').click(function  () {
            if (table.row('.selected').data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }

            Swal.fire({
                title: 'Desea eliminar?',
                text: table.row('.selected').data()[2],
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    if(parseInt(table.row('.selected').data()[1]) == 0){
                        var id = table.row('.selected').data()[0];
                        var url = '{{route( isset($eliminar) ? $eliminar : "home", ":id")}}';
                        url = url.replace(':id',id);

                        var fm = document.getElementById('form_delete');
                        fm.setAttribute('action', url);
                        fm.submit();
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'El registro esta eliminado'
                        })
                    }
                    
                }
            })
        });

        $('#btn_recover').click( function () {
            if (table.row('.selected').data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }

            Swal.fire({
                title: 'Desea recuperar?',
                text: table.row('.selected').data()[2],
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    if(parseInt(table.row('.selected').data()[1]) != 0){
                        var id = table.row('.selected').data()[0];
                        var url = '{{route( isset($recuperar) ? $recuperar : "home", ":id")}}';
                        url = url.replace(':id',id);

                        var fm = document.getElementById('form_recover');
                        fm.setAttribute('action', url);
                        fm.submit();
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'El registro no esta eliminado'
                        })
                    }
                    
                }
            })
        });

        $('#id_down_pdf').click(function () {
            if (table.row('.selected').data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }
            
            var id = table.row('.selected').data()[0];
            var url = '{{route("cfdiToPdf", ":id")}}';
            url = url.replace(':id',id);
            // window.location.href = url;
            window.open(url,'_blank');
        });

        $('#id_stock').click(function  () {
            if (table.row('.selected').data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }

            Swal.fire({
                title: 'Desea archivar?',
                text: table.row('.selected').data()[13],
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    if(parseInt(table.row('.selected').data()[3]) == 0){
                        var id = table.row('.selected').data()[0];
                        var url = '{{route( isset($toStock) ? $toStock : "home", ":id")}}';
                        url = url.replace(':id',id);
                        window.location.href = url;
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'El registro esta archivado'
                        })
                    }
                    
                }
            })
        });

        $('#id_restore').click(function  () {
            if (table.row('.selected').data() == undefined) {
                SGui.showError("Debe seleccionar un renglón");
                return;
            }

            Swal.fire({
                title: 'Desea recuperar?',
                text: table.row('.selected').data()[13],
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    if(parseInt(table.row('.selected').data()[3]) != 0){
                        var id = table.row('.selected').data()[0];
                        var url = '{{route( isset($restore) ? $restore : "home", ":id")}}';
                        url = url.replace(':id',id);
                        window.location.href = url;
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'El registro no está archivado'
                        })
                    }
                    
                }
            })
        });

        $('#isDeleted').change( function() {
            table.draw();
        });
        $('#carriers').change( function() {
            table.draw();
        });

    });
</script>