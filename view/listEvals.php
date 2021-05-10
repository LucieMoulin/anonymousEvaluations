<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 07.05.2021
 * Description : Liste des évaluations
 * Paramètres :
 *      $evaluations => Tableau contenant les évaluations
 *      $showOwner => Booléen définissant si la vue doit afficher le propriétaire du groupe
 *      $title => Titre de la liste
 */
?>
<h2 class="mt-4 text-center"><?= $title ?></h2>
<table id="evalsList<?= $string = str_replace(' ', '', $title); ?>" class="display table-striped" style="width:100%">
    <thead>
        <tr>
            <th>Module</th>
            <th>Date</th>
            <?php
                if($showOwner) :
            ?>
            <th>Nom de l'enseignant-e</th>
            <?php
                endif;
            ?>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($evaluations as $evaluation) :
        ?>
            <tr>
                <td class="row mr-0 ml-0">
                    <?= $evaluation['evaModuleNumber'] ?>
                </td>
                <td>
                    <?= date('d.m.Y', strtotime($evaluation['evaDate'])) ?>
                </td>
                <?php
                    if($showOwner && isset($evaluation['owner'])) :
                ?>
                <td><?= $evaluation['owner'] ?></td>
                <?php
                    endif;
                ?>
                
                <td>
                    <a href="<?= ROOT_DIR ?>/evaluation/details?id=<?= $evaluation['idEvaluation'] ?>">
                        Détails
                    </a>
                </td>
            </tr>
        <?php
            endforeach;
        ?>
    </tbody>
</table>

<script>
    $(document).ready(function() {
        //Ajout des barres de recherche popur chaque colonne
        $('#evalsList<?= $string = str_replace(' ', '', $title); ?> thead tr').clone(true).appendTo( '#evalsList<?= $string = str_replace(' ', '', $title); ?> thead' );
        $('#evalsList<?= $string = str_replace(' ', '', $title); ?> thead tr:eq(1) th').each( function (i) {
            var title = $(this).text();
            if(title != 'Actions'){
                $(this).html( '<input type="text" placeholder="Recherche '+title+'" />' );
    
                $('input',this).on('keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table.column(i).search(this.value).draw();
                    }
                } );
            } else {
                $(this).html(' ');
            }
        } );
    
        var table = $('#evalsList<?= $string = str_replace(' ', '', $title); ?>').DataTable( {
            orderCellsTop: true,
            fixedHeader: true,
            dom: 'rti',
            "columnDefs": [
                { "orderable": false, "targets": [3] },
                { "orderable": true, "targets": [0, 1, 2] }
            ]
        } );
    } );

    $('#evalsList<?= $string = str_replace(' ', '', $title); ?>').bind('DOMSubtreeModified', function(){
        $('#evalsList<?= $string = str_replace(' ', '', $title); ?>_info').each(function() {
            var text = $(this).text();
            var text = text.replace('Showing', 'Affichage de');
            var text = text.replace('filtered', 'filtrées');
            var text = text.replace('from', 'depuis');
            var text = text.replace('total entries', 'entrées totales');
            var text = text.replace('to ', 'à ');
            var text = text.replace('of', 'sur');
            var text = text.replace('entries', 'entrées');
            $(this).text(text);
        });
        $('.dataTables_empty').each(function() {            
            var text = $(this).html();
            var text = text.replace('No matching records found', 'Aucune entrée correspondante trouvée');
            $(this).html(text);
        })
    });
</script>