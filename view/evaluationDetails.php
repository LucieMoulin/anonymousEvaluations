<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 10.05.2021
 * Description : Vue d'affichage des détails d'une évaluation
 * Paramètres : 
 *      $evaluation : Évaluation à afficher
 *      $displayEditButton : booléen de définition de l'affichage du bouton de modification
 *      $displayId : booléen de définition de l'affichage de l'identifiant anonyme
 *      $displayResult : booléen de définition de l'affichage du résultat obtenu
 *      $displayReturn : booléen de définition de l'affichage des informations sur le retour
 *      $displayReturnForm : booléen de définition de l'affichage du formulaire de retour
 *      $displayState : booléen de définition de l'affichage de l'état
 *      $displayReturns : booléen de définition de l'affichage des retours des élèves
 *      $returns : si $displayReturns est true, retours à afficher
 *      $displayDownloadAllButton : si $displayReturns est true, définit si le bouton de téléchargement de tous les retous doit être affiché
 */
?>
<div class="row">
    <div class="col-12 text-center pt-1">
        <h2>Détails de l'évaluation</h2>
    </div>
</div>

<?php 
    if($displayEditButton) :        
?>
<div class="row">
    <div class="col-12 text-center pt-1">
        <a class="btn btn-light" href="<?= ROOT_DIR ?>/evaluation/edit?id=<?= $evaluation['idEvaluation'] ?>" role="button">Modifier</a>
    </div>
</div>
<?php
    endif;
?>

<div class="row mt-4 pt-1">
    <div class="col-6 text-right">
        Numéro du module
    </div>
    <div class="col-6">
        <?= $evaluation['evaModuleNumber'] ?>
    </div>
</div>

<div class="row pt-1">
    <div class="col-6 text-right">
        Groupe
    </div>
    <div class="col-6">
        <?= $evaluation['groName'] ?>
    </div>
</div>

<div class="row pt-1">
    <div class="col-6 text-right">
        Enseignant-e
    </div>
    <div class="col-6">
        <?= $evaluation['owner'] ?>
    </div>
</div>

<div class="row pt-1">
    <div class="col-6 text-right">
        Date
    </div>
    <div class="col-6">
        <?= date('d.m.Y', strtotime($evaluation['evaDate'])) ?>
    </div>
</div>

<div class="row pt-1">
    <div class="col-6 text-right">
        Durée
    </div>
    <div class="col-6">
        <?= $evaluation['evaLength'] ?>
    </div>
</div>

<div class="row pt-1">
    <div class="col-6 text-right">
        Document de consigne
    </div>
    <div class="col-6">
        <?php
            if($evaluation['evaInstructions'] == null) :
        ?>
        Aucun document de consigne
        <?php
            else :
        ?>
        <a href='<?= ROOT_DIR ?>/uploads/<?= $evaluation['evaInstructions'] ?>' target="blank"><?= $evaluation['evaInstructions'] ?></a>
        <?php
            endif;
        ?>
    </div>
</div>

<?php
    if($displayId) :
?>
<div class="row mt-4 pt-1">
    <div class="col-6 text-right">
        Identifiant anonyme
    </div>
    <div class="col-6">
        <?php
            if(isset($evaluation['anonymousId']['secondarySymbol'])) {
                echo $evaluation['anonymousId']['secondarySymbol'].' ';
            }
            if(isset($evaluation['anonymousId']['symbol'])) {
                echo $evaluation['anonymousId']['symbol'].' ';
            }
        ?>
        <?= $evaluation['anonymousId']['id'] ?>
    </div>
</div>
<?php
    endif;
?>

<?php
    if($displayResult) :
?>
<div class="row pt-1">
    <div class="col-6 text-right">
        Note obtenue
    </div>
    <div class="col-6">
        <?= $evaluation['evaGrade'] ?>
    </div>
</div>
<div class="row pt-1">
    <div class="col-6 text-right">
        Commentaire d'évaluation
    </div>
    <div class="col-6">
        <?php
            if(isset($evaluation['evaComment']) && $evaluation['evaComment'] != null) :
        ?>
        <span class="font-italic"><?= $evaluation['evaComment'] ?></span>
        <?php
            else :
        ?>
        Aucun commentaire d'évaluation
        <?php
            endif;
        ?>
    </div>
</div>
<?php
    endif;
?>

<?php
    if($displayReturn) :
?>
<div class="row pt-1">
    <div class="col-6 text-right">
        Retour
    </div>
    <div class="col-6">
        <?php
            if(!isset($evaluation['anonymousReturn']) || $evaluation['anonymousReturn'] == null) :
        ?>
        Aucun retour uploadé
        <?php
            else :
        ?>
        <a href='<?= ROOT_DIR ?>/uploads/<?= $id ?>/<?= $evaluation['anonymousReturn'] ?>' target="blank"><?= $evaluation['anonymousReturn'] ?></a>
        <?php
            endif;
        ?>        

        <?php
            if($displayReturnForm) :
        ?>
        <form action="<?= ROOT_DIR ?>/evaluation/return?id=<?= $evaluation['idEvaluation'] ?>" method="POST" class="form-inline" enctype="multipart/form-data">
            <input type="file" class="form-control border-0" id="return" name="return">
            <button type="submit" class="btn" name="submit">Transmettre</button>
        </form>
        <?php
            endif;
        ?>
    </div>
</div>
<?php
    endif;
?>

<?php
    if($displayState) :
?>
<div class="row mt-3">
    <div class="col-6 text-right">
        État
    </div>
    <div class="col-6">
        <?= $evaluation['staName'] ?></br>
    </div>
</div>

<div class="row pt-1">
    <div class="col-12 text-center">
        <?php
            switch ($evaluation['fkState']) {
                default:
                case STATE_WAITING:
        ?>
        <a class="btn btn-light" href="<?= ROOT_DIR ?>/evaluation/changeState?id=<?= $evaluation['idEvaluation'] ?>&state=<?= STATE_ACTIVE ?>" role="button">Activer</a>
        <?php
                    break;
                case STATE_ACTIVE:
                    //TODO confirmation clôture si pas tous les élèves ont rendu
        ?>
        <a class="btn btn-light" href="<?= ROOT_DIR ?>/evaluation/changeState?id=<?= $evaluation['idEvaluation'] ?>&state=<?= STATE_CLOSED ?>" role="button">Clôturer</a>

        <?php
                    break;
                case STATE_CLOSED:                    
        ?>
        <a class="btn btn-light" href="<?= ROOT_DIR ?>/evaluation/changeState?id=<?= $evaluation['idEvaluation'] ?>&state=<?= STATE_FINISHED ?>" role="button">Terminer</a>

        <?php
                    break;
                case STATE_FINISHED:                    
        ?>
        <a class="btn btn-light" href="<?= ROOT_DIR ?>/evaluation/changeState?id=<?= $evaluation['idEvaluation'] ?>&state=<?= STATE_ACTIVE ?>" role="button">Ré-activer</a>
        <?php
                    break;
            }
        ?>
    </div>
</div>
<?php
    endif;
?>


<?php
    if($displayReturns):
?>
<div class="col-12 text-center mt-4">
    <h3>Retours des élèves :</h3>
    <?php
        if($displayDownloadAllButton):
    ?>
        <a class="btn btn-light" href="<?= ROOT_DIR ?>/evaluation/getAllReturns?id=<?= $evaluation['idEvaluation'] ?>" role="button">Télécharger tous les retours</a>
    <?php
        endif;
    ?>
</div>

<form action="<?= ROOT_DIR ?>/evaluation/saveGrades?id=<?= $evaluation['idEvaluation'] ?>" method="POST">
    <table id="returnsList" class="display table-striped" style="width:100%">
        <thead>
            <tr>
                <th>Élève</th>
                <th class="no-sort">Retour d'évaluation</th>
                <th class="no-sort">Note</th>
                <th class="no-sort">Commentaire</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($returns as $return) :
            ?>
                <tr>
                    <td>
                        <?php
                            if(isset($return['anonymousId']['secondarySymbol'])) {
                                echo $return['anonymousId']['secondarySymbol'].' ';
                            }
                            if(isset($return['anonymousId']['symbol'])) {
                                echo $return['anonymousId']['symbol'].' ';
                            }
                        ?>
                        <?= $return['anonymousId']['id'] ?>
                    </td>
                    <td>
                        <?php
                            if(!isset($return['useReturn']) || $return['useReturn'] == null) :
                        ?>
                        Aucun retour uploadé
                        <?php
                            else :
                        ?>
                        <a href='<?= ROOT_DIR ?>/uploads/<?= $id ?>/<?= $return['useReturn'] ?>' target="blank"><?= $return['useReturn'] ?></a>
                        <?php
                            endif;
                        ?> 
                    </td>
                    <td>
                        <input type="text" class="form-control" id="grades[<?= $return['idUser']; ?>][grade]" name="grades[<?= $return['idUser']; ?>][grade]" 
                        <?php
                            //Affichage de la note                            
                            if ($return['useGrade'] != NULL) {                                   
                                echo("value=\"".$return['useGrade']."\"");
                            }
                        ?> style="max-width:50px">
                    </td>
                    <td>
                        <input type="text" class="form-control" id="grades[<?= $return['idUser']; ?>][comment]" name="grades[<?= $return['idUser']; ?>][comment]" 
                        <?php
                            //Affichage de la note                            
                            if ($return['useComment'] != NULL) {                                   
                                echo("value=\"".$return['useComment']."\"");
                            }
                        ?>>                        
                    </td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
    </table>
    <div class="col-12 text-center">
        <button type="submit" class="btn btn-light" name="submit">Enregistrer les notes et commentaires</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        //Ajout des barres de recherche popur chaque colonne
        $('#returnsList thead tr').clone(true).appendTo( '#returnsList thead' );
        $('#returnsList thead tr:eq(1) th').each( function (i) {
            var title = $(this).text();
            if(title == 'Élève'){
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
    
        var table = $('#returnsList').DataTable( {
            orderCellsTop: true,
            fixedHeader: true,
            dom: 'rti',
            "columnDefs": [
                { "orderable": false, "targets": "no-sort" }
            ]
        } );
    } );

    $('#returnsList').bind('DOMSubtreeModified', function(){
        $('#returnsList_info').each(function() {
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
<?php
    endif;
?>

