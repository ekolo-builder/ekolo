<?php
    require __DIR__.'/../vendor/autoload.php';

    use Ekolo\Builder\Routing\Router;
    use Ekolo\Builder\Bin\Application;

    $app = new Application;

    // routers
    $users = require('./routes/users.php');

    // set configuration of file folders
    $app->set('views', 'views');
    $app->set('public', 'public');

    // Middlwares
    $app->use(function ($req, $res) {
        
    });

    // routing
    $app->get('/', function ($req, $res) {
        $res->render('index', [
            'title' => 'Welcome to <span>Ekolo Builder</span>',
            'message' => 'Flexible, quick and easy to develop'
        ]);
    });

    $app->post('/list', function ($req, $res) {
        debug($req->body()->all());
    });

    $app->use('/users', $users);

    // error handler
    $app->trackErrors(function ($error, $req, $res) {
        echo $error->message;
        echo '<br>'.$error->status;
    });