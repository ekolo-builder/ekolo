<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <h1 class="title"><?= $title ?></h1>
    <p><?= $message ?></p>

    <p>Please click on the link below to view the documentantion</p>

    <p><a href="" class="btn btn-primary">Documentation</a></p>

    <form action="/list" method="post">
        <p><input type="text" name="name"></p>
        <p><input type="text" name="firstName"></p>
        <p><button type="submit">Valider</button></p>
    </form>
</body>
</html>