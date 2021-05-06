<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 06.05.2021
 * Description : Formulaire de création de compte
 * Paramètres :
 *      $login => login de l'utilisateur à utiliser pour la création du compte
 *      $name => tableau avec nom et prénom de l'utilisateur
 *      $roles => tableau des rôles
 */
?>
<h1 class="text-center">Création de compte</h1>
<p class="text-center">Bonjour <?= $login ?>. Bienvenue sur [Nom Plateforme].</p>

<form action="<?= ROOT_DIR ?>/auth/formSubmitted" method="POST" class="mt-5">
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="lastName" class="pt-2">Nom</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control w-50" id="lastName" name="lastName" value="<?= $name['lastName'] ?>">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="firstName" class="pt-2">Prénom</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control w-50" id="firstName" name="firstName" value="<?= $name['firstName'] ?>">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="role">Rôle</label>
        </div>
        <div class="col-8">
            <?php
                //boucle sur les rôles
                foreach ($roles as $role) :
            ?>
            <div class="row ml-1">
                <div class="form-check col-3">                
                    <input class="form-check-input" type="radio" name="role" id="role<?= $role['idRole'] ?>" value="<?= $role['idRole'] ?>">
                    <label class="form-check-label" for="role<?= $role['idRole'] ?>">
                        <?= $role['rolName'] ?>
                    </label>
                </div>
                <p class="col-9">
                    <?= $role['rolDescription'] ?>
                </p>
            </div>
            <?php
                endforeach;
            ?>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn mt-3" name="submit">Créer le compte</button>
    </div>
</form>