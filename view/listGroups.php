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
            if(text.indexOf('Showing') != -1){
                text = text.replace('Showing', 'Affichage de');
                $(this).text(text);
            }
            if(text.indexOf('filtered') != -1){
                text = text.replace('filtered', 'filtrées');
                $(this).text(text);
            }
            if(text.indexOf('from') != -1){
                text = text.replace('from', 'depuis');
                $(this).text(text);
            }
            if(text.indexOf('total entries') != -1){
                text = text.replace('total entries', 'entrées totales');
                $(this).text(text);
            }
            if(text.indexOf('to') != -1){
                text = text.replace('to ', 'à ');
                $(this).text(text);
            }
            if(text.indexOf('of') != -1){
                text = text.replace('of', 'sur');
                $(this).text(text);
            }
            if(text.indexOf('entries') != -1){
                text = text.replace('entries', 'entrées');
                $(this).text(text);
            }
        });
        $('.dataTables_empty').each(function() {            
            var text = $(this).html();
            if(text.indexOf('No matching records found') != -1){
                text = text.replace('No matching records found', 'Aucune entrée correspondante trouvée');
                $(this).text(text);
            }
        })
    });
</script>