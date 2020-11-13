<h1>Liste des utilisateurs</h1>

<?php if (!empty($users)) : ?>
    <ul>
        <?php foreach ($users as $key => $user) : ?>
            <li><a href="/list/<?= $key ?>"><?= $user['name'].' '.$user['firstName'] ?></a></li>
        <?php endforeach ?>
    </ul>
<?php endif ?>