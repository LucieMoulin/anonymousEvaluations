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
 *      $displayConfirm : booléen de définition de l'affichage de la confirmation de changement d'état pour la fermeture d'une éval
 *      $displayReturns : booléen de définition de l'affichage des retours des élèves
 *      $displayGradesForm : booléen de définition de l'affichage du formulaire de modification/ajout de notes
 *      $returns : si $displayReturns est true, retours à afficher
 *      $displayDownloadAllButton : si $displayReturns est true, définit si le bouton de téléchargement de tous les retous doit être affiché
 *      $toggleActive : définit si le bouton d'affichage des noms est actif
 *      $showNames : définit si les noms doivent être affichés
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
<!-- Bouton de modification de l'évaluation -->
<div class="row">
    <div class="col-12 text-center pt-1">
        <a class="btn btn-light" href="<?= ROOT_DIR ?>/evaluation/edit?id=<?= $evaluation['idEvaluation'] ?>" role="button">Modifier</a>
    </div>
</div>
<?php
    endif;
?>

<!-- Informations sur l'évaluation -->
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

<!-- Document de consigne -->
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
<!-- Identifiant anonyme de l'élève connecté-e -->
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
<!-- Résultat obtenu par l'élève connecté-e -->
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
<!-- Retour d'évaluation de l'élève connecté-e -->
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
<!-- État de l'évaluation -->
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
        ?>
        <a class="btn btn-light" href="<?= ROOT_DIR ?>/evaluation/changeState?id=<?= $evaluation['idEvaluation'] ?>&state=<?= STATE_CLOSED ?>" role="button" <?php if($displayConfirm): ?>onClick="return confirm('Tous les élèves n\'ont pas rendu, voulez-vous vraiment clôturer cette évalaution ?');"<?php endif; ?>>Clôturer</a>

        <?php
                    break;
                case STATE_CLOSED:                    
        ?>
        <a class="btn btn-light" href="<?= ROOT_DIR ?>/evaluation/changeState?id=<?= $evaluation['idEvaluation'] ?>&state=<?= STATE_FINISHED ?>" role="button">Terminer</a>

        <?php
                    break;
                case STATE_FINISHED:                    
        ?>
        <a class="btn btn-light" href="<?= ROOT_DIR ?>/evaluation/changeState?id=<?= $evaluation['idEvaluation'] ?>&state=<?= STATE_CLOSED ?>" role="button">Ré-activer</a>
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
<!-- Retours d'évalaution de tous les élève -->
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

<div id="returnsDest" class="mt-2 mb-2">
    <div id="returnsSource">
        <?php 
            if($displayGradesForm) :
        ?>
        <form action="<?= ROOT_DIR ?>/evaluation/saveGrades?id=<?= $evaluation['idEvaluation'] ?>" method="POST">
        <?php 
            endif;
        ?>
            <table class="table table-striped">
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
                                <?= $return['anonymousId']['id'] ?><?php
                                    if($showNames):
                                ?> - <?= $return['useFirstName'] ?> <?= $return['useLastName'] ?>
                                <?php endif; ?>
                                
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
                                <?php 
                                    if($displayGradesForm) :
                                ?>
                                <input type="text" class="form-control" id="grades[<?= $return['anonymousId']['id']; ?>][grade]" name="grades[<?= $return['anonymousId']['id']; ?>][grade]" 
                                <?php
                                    //Affichage de la note                            
                                    if ($return['useGrade'] != NULL) {                                   
                                        echo("value=\"".$return['useGrade']."\"");
                                    }
                                ?> style="max-width:50px">
                                <?php 
                                    else :
                                ?>
                                <?= $return['useGrade'] ?>
                                <?php 
                                    endif;
                                ?>
                            </td>
                            <td>
                                <?php 
                                    if($displayGradesForm) :
                                ?>
                                <input type="text" class="form-control" id="grades[<?= $return['anonymousId']['id']; ?>][comment]" name="grades[<?= $return['anonymousId']['id']; ?>][comment]" 
                                <?php
                                    //Affichage de la note                            
                                    if ($return['useComment'] != NULL) {                                   
                                        echo("value=\"".$return['useComment']."\"");
                                    }
                                ?>>
                                <?php 
                                    else :
                                ?>
                                <?= $return['useComment'] ?>
                                <?php 
                                    endif;
                                ?>
                            </td>
                        </tr>
                    <?php
                        endforeach;
                    ?>
                </tbody>
            </table>
            <?php 
                if($displayGradesForm) :
            ?>
            <div class="col-12 text-center mb-4">
                <button type="submit" class="btn btn-light" name="submit">Enregistrer les notes et commentaires</button>
            </div>
        </form>
        <?php 
            endif;
        ?>
    </div>
</div>

<div class="row">
    <div class="col-6 text-right mt-1">
        <h4>Noms des élèves</h4>
    </div>
    <div class="col-6 mb-4">
        <div class="form-check pl-0" id="toggle-wrapper">
            <input id="toggle" class="form-check-input" type="checkbox" data-toggle="toggle" data-onstyle="outline-secondary" data-on="Affichés" data-off="Cachés" <?= $showNames ? 'checked' : '' ?>>
        </div>
    </div>
</div>

<!-- Script de gestion de la table de données -->
<script>
    $(document).ready(function() {
        if(<?= !$toggleActive ? 'true' : 'false' ?>){
            $('#toggle').bootstrapToggle('disable');
        }

        $('#toggle').change(function() {
            if($(this).prop('checked') && <?= $toggleActive ? 'true' : 'false' ?>){
                //Charge le tableau avec les noms des élèves
                $('#returnsDest').load('<?= ROOT_DIR ?>/evaluation/details?id=<?= $id ?>&showNames=true #returnsSource');
            } else {
                //Charge le tableau sans les noms des élèves
                $('#returnsDest').load('<?= ROOT_DIR ?>/evaluation/details?id=<?= $id ?> #returnsSource');
            }
        });
        
        $('#toggle-wrapper').click(function() {
            if($('#toggle').attr('disabled') == 'disabled'){
                alert('Il est nécessaire de terminer l\'évaluation avant de pouvoir afficher les noms');
            }
        });
    });
</script>
<?php
    endif;
?>