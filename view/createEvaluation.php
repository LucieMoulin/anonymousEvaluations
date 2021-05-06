<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 06.05.2021
 * Description : Formulaire de création d'évaluation
 * Paramètres :
 *      $groups => groupes d'élèves
 */
?>
<h1 class="text-center">Création d'évaluation</h1>

<form action="<?= ROOT_DIR ?>/evaluation/creationSubmitted" method="POST" class="mt-5" enctype="multipart/form-data">
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="moduleNumber" class="pt-2">Numéro du module</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control w-50" id="moduleNumber" name="moduleNumber">
        </div>
    </div>
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
                <option value="<?= $group['idGroup']; ?>"><?= $group['groName']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="date" class="pt-2">Date</label>
        </div>
        <div class="col-8">
            <input type="date" class="form-control w-50" id="date" name="date">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="length" class="pt-2">Durée</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control w-50" id="length" name="length">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="instructions" class="pt-2">Consigne</label>
        </div>
        <div class="col-8">
            <input type="file" class="form-control w-50 border-0" id="instructions" name="instructions">
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn mt-3" name="submit">Créer l'évaluation</button>
    </div>
</form>