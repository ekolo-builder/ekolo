<?php
    require __DIR__.'/../vendor/autoload.php';

    use Ekolo\Builder\Routing\Router;
    use Ekolo\Builder\Bin\Application;

    $app = new Application;

    // routers
    // $users = require('./routes/users.php');

    // view engine setup
    $app->set('views', 'views');

    // Middlwares
    $app->use(function ($req, $res) {
        
    });

    // routing
    // $app->use('/users', $users);
    $app->get('/commandes/mm/mm', function ($req, $res) { echo "Cooooolll"; }, function ($req, $res) {
        $res->send("Salut");
    });

    // error handler
    $app->trackErrors(function ($error, $req, $res) {
        echo $error->message;
        echo '<br>'.$error->status;
    });