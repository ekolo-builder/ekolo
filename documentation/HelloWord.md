# API Ekolo Builder

## Hello World

Once you have finished the installation you can now start developing your application.

Now create the application entry point `index.php` file

```bash
$ echo "" > index.php
```

Copy and paste this example of a simple hello world application

```php
<?php
    require __DIR__.'/vendor/autoload.php';

    use Ekolo\Builder\Bin\Application;
    use Ekolo\Builder\Http\Request;
    use Ekolo\Builder\Http\Response;

    $app = new Application;

    $app->get('/', function (Request $req, Response $res) {
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
```

Now to launch the application, position yourself on the console at the level of the project directory and type the following :

```bash
$ php -S localhost:3000
```

You can replace the 3000 with the port of your choice.

Open the address on which your application is launched in your browser

[Previous - Installation](/documentation/Installation.md)

[Next - Routing](/documentation/Routing.md)