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

        

        var table = $('#{{$table_id}}').DataTable({
            "columnDefs": [
                {
                    "targets": [0,1],
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
            var id = table.row('.selected').data()[0];
            var url = '{{ isset($signRoute) ? route($signRoute, ":id") : "#" }}';
            url = url.replace(':id',id);
            window.location.href = url;
        });
        
        $('#btn_edit').click(function () {
            var id = table.row('.selected').data()[0];
            var url = '{{route($editar, ":id")}}';
            url = url.replace(':id',id);
            window.location.href = url;
        });

        $('#btn_delete').click(function  () {
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

        $('#isDeleted').change( function() {
            table.draw();
        });
        $('#carriers').change( function() {
            table.draw();
        });

    });
</script>