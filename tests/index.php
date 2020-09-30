<?php
    require __DIR__.'/../vendor/autoload.php';

    use Ekolo\Builder\Routing\Router;
    use Ekolo\Builder\Bin\Application;

    $app = new Application;

    // Routers
    $users = require('./routes/users.php');

    $app->set('views', 'views');

    $app->use(function ($req, $res) {
        // debug($req->params()->all());
    });

    $app->use('/users', $users);

    $app->trackErrors(function ($error, $req, $res) {
        echo $error->message;
    });