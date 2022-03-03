<script>
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

        

        var table = $('#{{$table_id}}').DataTable({
            "language": {
                "lengthMenu": "Mostrar _MENU_ registros",
                "zeroRecords": "No se encontraron resultados",
                "EmptyTable": "Ningún dato disponible en esta tabla",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                "search": "Buscar:",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "columnDefs": [
                {
                    "targets": [0,1,2],
                    "visible": false,
                    "searchable": true
                }
            ]
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

            var id = table.row('.selected').data()[0];
            var url = '{{ isset($cancelRoute) ? route($cancelRoute, ":id") : "#" }}';
            url = url.replace(':id',id);
            window.location.href = url;
            
            // const { value: fruit } = await Swal.fire({
            //     title: 'Select field validation',
            //     input: 'select',
            //     inputOptions: {
            //         'Fruits': {
            //             apples: 'Apples',
            //             bananas: 'Bananas',
            //             grapes: 'Grapes',
            //             oranges: 'Oranges'
            //         },
            //         'Vegetables': {
            //             potato: 'Potato',
            //             broccoli: 'Broccoli',
            //             carrot: 'Carrot'
            //         },
            //         'icecream': 'Ice cream'
            //     },
            //     inputPlaceholder: 'Select a fruit',
            //     showCancelButton: true,
            //     inputValidator: (value) => {
            //         return new Promise((resolve) => {
            //         if (value === 'oranges') {
            //             resolve()
            //         } else {
            //             resolve('You need to select oranges :)')
            //         }
            //         })
            //     }
            // })
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
                        var url = '{{route($eliminar, ":id")}}';
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
                        var url = '{{route($recuperar, ":id")}}';
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
            window.location.href = url;
        });

        $('#isDeleted').change( function() {
            table.draw();
        });
        $('#carriers').change( function() {
            table.draw();
        });

    });
</script>