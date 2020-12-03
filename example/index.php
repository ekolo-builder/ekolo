<?php
    require __DIR__.'/../vendor/autoload.php';

    use Ekolo\Builder\Bin\Application;
    use Ekolo\Builder\Routing\Router;
    use Ekolo\Builder\Http\Request;
    use Ekolo\Builder\Http\Response;

    $app = new Application;

    // routers
    $users = require('./routes/users.php');

    // set configuration of file folders
    $app->set('views', 'views');
    $app->set('public', 'public');
    $app->set('template', 'layout');

    // Middlwares
    $app->use(function ($req, $res) {
        
    });

    $app->use('/users', $users);

    // routing
    // $app->get('/', function ($req, $res) {
    //     $res->render('index', [
    //         'title' => 'Welcome to <span>Ekolo Builder</span>',
    //         'message' => 'Flexible, quick and easy to develop'
    //     ]);
    // });

    $app->get('/', function ($req, $res) {
        $res->send('
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Welcome to Ekolo Builder</title>
                <style>
                    body {
                    padding: 5% 10%;
                    font-family: "Trebuchet MS", "Lucida Sans Unicode", "Lucida Grande", "Lucida Sans", Arial,
                    sans-serif;
                    }
                    .btn {
                    padding: 5px 15px;
                    }

                    .btn-primary {
                    border: 1px solid rgb(37, 61, 170);
                    background-color: rgb(37, 61, 170);
                    color: #ffffff;
                    text-decoration: none;
                    }

                    .btn-primary:hover {
                    transition: all linear .04s;
                    background-color: rgb(4, 33, 161);
                    }

                    .title span {
                    color: rgb(4, 33, 161) !important;
                    }

                    .text-primary {
                    color: rgb(37, 61, 170) !important;
                    }
                </style>
            </head>

            <body>
                <h1 class="title">Welcome to <span>Ekolo Builder</span></h1>
                <p>Flexible, quick and easy to develop</p>

                <p>Please click on the link below to view the documentantion</p>

                <p><a href="https://github.com/ekolo-builder/ekolo" class="btn btn-primary">Documentation</a></p>
            </body>

            </html>
        ');
    });

    // error handler
    $app->trackErrors(function ($error, $req, $res) {
        echo $error->message;
        echo '<br>'.$error->status;
    });