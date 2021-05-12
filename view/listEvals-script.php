<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 12.05.2021
 * Description : script d'initialisation des listes d'évaluations
 * Paramètre :
 *      $counter => Nombre de liste à initialiser
 */
?>
<script>
    $(document).ready(function() {
        <?php
            for($i = 1; $i <= $counter; $i++) :
        ?>
        //Ajout des barres de recherche pour chaque colonne
        $('#evalsList<?= $i ?> thead tr').clone(true).appendTo( '#evalsList<?= $i ?> thead' );
        $('#evalsList<?= $i ?> thead tr:eq(1) th').each( function (i) {
            var title = $(this).text();
            if(title != 'Actions'){
                $(this).html( '<input type="text" placeholder="Recherche '+title+'" />' );
    
                $('input',this).on('keyup change', function () {
                    if ( table<?= $i ?>.column(i).search() !== this.value ) {
                        table<?= $i ?>.column(i).search(this.value).draw();
                    }
                } );
            } else {
                $(this).html(' ');
            }
        } );

        var table<?= $i ?> = $('#evalsList<?= $i ?>').DataTable( {
            orderCellsTop: true,
            fixedHeader: true,
            dom: 'rti',
            "columnDefs": [
                { "orderable": false, "targets": "no-sort" }
            ]
        } );
        <?php
            endfor;
        ?>
    } );
</script>