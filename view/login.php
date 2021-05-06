<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 06.05.2021
 * Description : Formulaire de connexion
 */
?>
<h1 class="text-center">Connexion</h1>

<form action="<?= ROOT_DIR ?>/auth/authenticate" method="POST" class="mt-5">
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="login" class="pt-2">Login</label>
        </div>
        <div class="col-8">
            <input type="text" class="form-control w-50" id="login" name="login" value="">
        </div>
    </div>
    <div class="row form-group">
        <div class="col-4 text-right">
            <label for="password" class="pt-2">Mot de passe</label>
        </div>
        <div class="col-8">
            <input type="password" class="form-control w-50" id="password" name="password" value="">
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn mt-3" name="submit">Se connecter</button>
    </div>
</form>