<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 10.05.2021
 * Description : Vue d'affichage des détails d'une évaluation
 * Paramètres : 
 *      $evaluation : Évaluation à afficher
 *      $displayState : booléen de définition de l'affichage de l'état
 */
?>
<?php
    if($displayState) :
?>
<div class="row">
    <div class="col-6 text-right pt-1">
        État
    </div>
    <div class="col-6">
        <?= $evaluation['staName'] ?>
        <?php
            switch ($evaluation['fkState']) {
                default:
                case STATE_WAITING:
        ?>
        <a class="btn btn-light ml-2" href="<?= ROOT_DIR ?>/evaluation/changeState?id=<?= $evaluation['idEvaluation'] ?>&state=<?= STATE_ACTIVE ?>" role="button">Activer</a>
        <?php
                    break;
                case STATE_ACTIVE:                    
        ?>
        <a class="btn btn-light ml-2" href="<?= ROOT_DIR ?>/evaluation/changeState?id=<?= $evaluation['idEvaluation'] ?>&state=<?= STATE_CLOSED ?>" role="button">Clôturer</a>

        <?php
                    break;
                case STATE_CLOSED:                    
        ?>
        <a class="btn btn-light ml-2" href="<?= ROOT_DIR ?>/evaluation/changeState?id=<?= $evaluation['idEvaluation'] ?>&state=<?= STATE_FINISHED ?>" role="button">Terminer</a>

        <?php
                    break;
                case STATE_FINISHED:                    
        ?>
        <a class="btn btn-light ml-2" href="<?= ROOT_DIR ?>/evaluation/changeState?id=<?= $evaluation['idEvaluation'] ?>&state=<?= STATE_ACTIVE ?>" role="button">Ré-activer</a>
        <?php
                    break;
            }
        ?>
    </div>
</div>
<?php
    endif;
?>