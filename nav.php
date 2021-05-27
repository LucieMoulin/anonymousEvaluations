<?php
    /**
     * ETML
     * Autrice : Lucie Moulin
     * Date : 06.05.2021
     * Description : Barre de navigation pour tout le site
     * Paramètres :
     *      role : Rôle de la personne connectée, permettant de configurer l'affichage de la barre de navigation
     */
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="<?= ROOT_DIR; ?>">EvAnon - Évaluations Anonymes</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?= ROOT_DIR; ?>">Accueil</a>
            </li>
            <?php
                if($role != 0) :
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarTestDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Évaluations
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarTestDropdown">
                    <a class="dropdown-item" href="<?= ROOT_DIR; ?>/evaluation/list">Liste des évaluations</a>
                    <?php
                        if($role == ROLE_TEACHER || $role == ROLE_ADMIN) :
                    ?>
                    <a class="dropdown-item" href="<?= ROOT_DIR; ?>/evaluation/create">Créer une évaluation</a>
                    <?php
                        endif;
                    ?>
                </div>
            </li>
            <?php
                endif;
            ?>
            <?php
                if($role == ROLE_TEACHER || $role == ROLE_ADMIN) :
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="<?= ROOT_DIR; ?>/group/list" id="navbarTestDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Groupes
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarTestDropdown">
                    <a class="dropdown-item" href="<?= ROOT_DIR; ?>/group/list">Liste des groupe</a>
                    <a class="dropdown-item" href="<?= ROOT_DIR; ?>/group/create">Créer un groupe</a>
                </div>
            </li>
            <?php
                endif;
            ?>
        </ul>
        <ul class="navbar-nav ml-auto">
            <?php if(isset($_SESSION['connectedUser'])) : ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= ROOT_DIR; ?>/auth/logout"><b>Bonjour <?= $_SESSION['firstName'] ?> <?= $_SESSION['lastName'] ?> (<?= $_SESSION['connectedUser'] ?>) - Se déconnecter</b></a>
                </li>
            <?php else : ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= ROOT_DIR; ?>/auth/login"><b>Se connecter</b></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>