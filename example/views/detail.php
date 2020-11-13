<?php if (!empty($user)) : ?>
    <h1>Utilisateur <span class="text-primary"><?= $user['name'].' '.$user['firstName'] ?></span></h1>

    <p>Bienvenue sur notre plateforme</p>

    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus, asperiores voluptates. Accusamus labore laborum non ea dolores magni officia voluptates explicabo ab neque, unde nostrum eligendi sint voluptatem! Rerum, incidunt?</p>
<?php else: ?>
    <h1>Aucun utilisateur trouvé...</h1>
<?php endif ?>

<p><a href="/list"><- Retour</a></p>