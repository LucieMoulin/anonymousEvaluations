<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 06.05.2021
 * Description : Formulaire de création ou de modification d'évaluation
 * Paramètres :
 *      $groups => groupes d'élèves
 *      $isEdition => booléen de définition si le fomulaire est pour la modification d'une évaluation
 *      $evaluation => si $isEdition est à true, évaluation à modifier
 */
?>
<?php
    if($isEdition):
?>
<h1 class="text-center">Modification d'évaluation</h1>
<?php
    else:
?>
<h1 class="text-center">Création d'évaluation</h1>
<?php
    endif;
?>

<form action="<?= ROOT_DIR ?>/evaluation/creationSubmitted" method="POST" class="mt-5" enctype="multipart/form-data">
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="moduleNumber" class="pt-2">Numéro du module</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control w-50" id="moduleNumber" name="moduleNumber" value="<?php if($isEdition) { echo htmlspecialchars($evaluation['evaModuleNumber']); } ?>">
        </div>
    </div>
    <?php
        if(!$isEdition):
    ?>
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="group" class="pt-2">Sélection du groupe</label>
        </div>
        <div class="col-8">
            <select class="form-control w-50" name="group" id="group">
                <?php if(empty($groups)) : ?>
                <option value="-1">Aucuns groupes</option>
                <?php else : ?>
                <option value="-1">Sélectionner...</option>
                <option disabled>────────────────────</option>
                <?php endif; ?>
                <?php
                    //Boucle sur tous les groupes
                    foreach($groups as $group):
                ?>
                <option value="<?= $group['idGroup']; ?>"><?= htmlspecialchars($group['groName']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php
        endif;
    ?>
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="date" class="pt-2">Date</label>
        </div>
        <div class="col-8">
            <input type="date" class="form-control w-50" id="date" name="date" value="<?php if($isEdition) { echo $evaluation['evaDate']; } ?>">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="length" class="pt-2">Durée</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control w-50" id="length" name="length" value="<?php if($isEdition) { echo htmlspecialchars($evaluation['evaLength']); } ?>">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="instructions" class="pt-2">Consigne</label>
        </div>
        <div class="col-8">
            <?php
                if($isEdition && $evaluation['evaInstructions'] == null) :
            ?>
                Aucun document de consigne
            <?php
                elseif($isEdition) :
            ?>
            <a href='<?= ROOT_DIR ?>/uploads/<?= $evaluation['evaInstructions'] ?>' target="blank"><?= $evaluation['evaInstructions'] ?></a><br>
            <input type="checkbox" id="removeFile" name="removeFile">
            <label for="removeFile">Supprimer la consigne</label>
            <?php
                endif;
            ?>
            <input type="file" class="form-control w-50 border-0" id="instructions" name="instructions">
        </div>
    </div>
    <div class="text-center">    
        <?php
            if($isEdition):
        ?>
        <button type="submit" class="btn mt-3" name="submit" value="<?= $evaluation['idEvaluation'] ?>">Modifier l'évaluation</button>
        <?php
            else:
        ?>
        <button type="submit" class="btn mt-3" name="submit">Créer l'évaluation</button>
        <?php
            endif;
        ?>
    </div>
</form>