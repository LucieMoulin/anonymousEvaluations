<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 07.05.2021
 * Description : Formulaire de création de groupe
 * Paramètres :
 *      $group => Informations du groupe
 *      $students => tableau contenant les élèves
 */
?>
<div class="text-center">
    <h1><?= isset($group['groName']) ? "Modification de ".$group['groName'] : "Création de groupe" ?></h1>
    <?php if(isset($group['groName'])): ?><a href="<?= ROOT_DIR ?>/group/delete"><button class="btn" onclick="return confirm('Supprimer le groupe <?= $group['groName'] ?> ?')">Supprimer ce groupe</button></a><?php endif; ?>
</div>

<form action="<?= ROOT_DIR ?>/group/formSubmitted" method="POST" class="mt-5">
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="name" class="pt-2">Nom du groupe</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control w-50" id="name" name="name"<?= isset($group['groName']) ? ' value="'.$group['groName'].'"' : ' ' ?>>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-4 text-right">
            <label>Élèves :</label>
        </div>
        <div class="col-8">
            <div class="row mb-4" style="max-height:150px;overflow:auto;">
                <?php
                    foreach($students as $student) :
                ?>
                <div class="col-3">                
                    <input type="checkbox" id="student[<?= $student['idUser']; ?>]" name="student[<?= $student['idUser']; ?>]" 
                        <?php
                            if(isset($student['isMember']) && $student['isMember']){
                                echo("checked");
                            }
                        ?>>
                    <label for="student[<?= $student['idUser']; ?>]"><?= $student['useLastName']." ".$student['useFirstName']; ?></label> 
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn mt-3" name="submit"><?= isset($group['groName']) ? "Sauvegarder les modifications de ".$group['groName'] : "Créer le groupe" ?></button>
    </div>
</form>