<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 07.05.2021
 * Description : Liste des groupes
 * Paramètres :
 *      $groups => Tableau contenant les groupes
 *      $showOwner => Booléen définissant si la vue doit afficher le propriétaire du groupe
 */
?>
<h1 class="text-center">Liste des groupes</h1>
<table id="groupList" class="display table-striped" style="width:100%">
    <thead>
        <tr>
            <th>Nom</th>
            <?php
                if($showOwner) :
            ?>
            <th>Propriétaire</th>
            <?php
                endif;
            ?>
            <th>Membres</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($groups as $group) :
        ?>
            <tr>
                <td>
                    <a href="<?= ROOT_DIR ?>/group/edit?id=<?= $group['idGroup'] ?>">
                        <?= $group['groName'] ?>
                    </a>
                </td>
                <?php
                    if($showOwner && isset($group['owner'])) :
                ?>
                <td><?= $group['owner'] ?></td>
                <?php
                    endif;
                ?>
                <td>
                    <?php
                        foreach ($group['students'] as $student) :
                    ?>
                    <?= $student['useLastName'] ?> <?= $student['useFirstName'] ?>;
                    <?php
                        endforeach;
                    ?>
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
        $('#groupList thead tr').clone(true).appendTo( '#groupList thead' );
        $('#groupList thead tr:eq(1) th').each( function (i) {
            var title = $(this).text();
            $(this).html( '<input type="text" placeholder="Recherche '+title+'" />' );
    
            $('input',this).on('keyup change', function () {
                if ( table.column(i).search() !== this.value ) {
                    table.column(i).search(this.value).draw();
                }
            } );
        } );
    
        var table = $('#groupList').DataTable( {
            orderCellsTop: true,
            fixedHeader: true,
            dom: 'rti'
        } );
    } );

    $('#groupList').bind('DOMSubtreeModified', function(){
        $('#groupList_info').each(function() {
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