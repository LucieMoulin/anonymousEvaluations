<?php
    /**
     * ETML
     * Autrice : Lucie Moulin
     * Date : 06.05.2021
     * Description : Barre de navigation pour tout le site
     */
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="<?= ROOT_DIR; ?>">Site</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?= ROOT_DIR; ?>">Accueil</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <?php if(isset($_SESSION['connectedUser'])) : ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= ROOT_DIR; ?>/auth/logout"><b><?= $_SESSION['connectedUser'] ?> - Se déconnecter</b></a>
                </li>
            <?php else : ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= ROOT_DIR; ?>/auth/login"><b>Se connecter</b></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>