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
    $app->get('/', function ($req, $res) {
        $res->render('index', [
            'title' => 'Welcome to <span>Ekolo Builder</span>',
            'message' => 'Flexible, quick and easy to develop'
        ]);
    });

    $app->get('/list', function ($req, $res) {
        $res->render('list', [
            'users' => [
                [
                    'id' => 1,
                    'name' => "Ambulasi",
                    'firstName' => "Clovis",
                ],
                [
                    'id' => 2,
                    'name' => "Ambulasi",
                    'firstName' => "Divine",
                ],
                [
                    'id' => 3,
                    'name' => "Mampuya",
                    'firstName' => "Gladis",
                ],
                [
                    'id' => 4,
                    'name' => "Mampuya",
                    'firstName' => "Gloria",
                ],
                [
                    'id' => 5,
                    'name' => "Etokila",
                    'firstName' => "Chico",
                ],
                [
                    'id' => 6,
                    'name' => "Etokila",
                    'firstName' => "Diani",
                ]
            ]
        ]);
    });

     $app->get('/list/:id', function ($req, $res) {
        
        $users = [
            [
                'id' => 1,
                'name' => "Ambulasi",
                'firstName' => "Clovis",
            ],
            [
                'id' => 2,
                'name' => "Ambulasi",
                'firstName' => "Divine",
            ],
            [
                'id' => 3,
                'name' => "Mampuya",
                'firstName' => "Gladis",
            ],
            [
                'id' => 4,
                'name' => "Mampuya",
                'firstName' => "Gloria",
            ],
            [
                'id' => 5,
                'name' => "Etokila",
                'firstName' => "Chico",
            ],
            [
                'id' => 6,
                'name' => "Etokila",
                'firstName' => "Diani",
            ]
        ];

        $res->render('detail', [
            'user' => $users[$req->params->id]
        ]);
    });

    $app->post('/list', function (Request $req, Response $res) {
        $rules = [
            'name' => 'required|min:5|field:Nom de famille',
            'firstName' => 'required|max:2'
        ];

        if ($req->validator()->verify($rules)) {
            echo "TOut est cool";
        }else {
            debug(session("errors"));
        }
    });

    // error handler
    $app->trackErrors(function ($error, $req, $res) {
        echo $error->message;
        echo '<br>'.$error->status;
    });