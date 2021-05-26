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
 *      $counter => Nombre de listes restantes à initialiser
 */
if(!isset($counter)){
    $counter = 1;  
}
include_once('./view/listEvals-script.php');
?>
<h2 class="mt-4 text-center"><?= $title ?></h2>
<table id="evalsList<?= $counter ?>" class="display table-striped" style="width:100%">
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
            <th class="no-sort">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($evaluations as $evaluation) :
        ?>
            <tr>
                <td class="row mr-0 ml-0">
                    <?= htmlspecialchars($evaluation['evaModuleNumber']) ?>
                </td>
                <td>
                    <span style="display:none;"><?= strtotime($evaluation['evaDate']) ?></span><?= date('d.m.Y', strtotime($evaluation['evaDate'])) ?>
                </td>
                <?php
                    if($showOwner && isset($evaluation['owner'])) :
                ?>
                <td><?= htmlspecialchars($evaluation['owner']) ?></td>
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
    $('#evalsList<?= $counter ?>').on('DOMSubtreeModified', function(){
        $('#evalsList<?= $counter ?>_info').each(function() {
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
            if(text.indexOf('No data available in table') != -1){
                text = text.replace('No data available in table', 'Aucune données dans la liste');
                $(this).text(text);
            }
        })
    });
</script>