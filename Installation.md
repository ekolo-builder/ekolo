# API Ekolo Builder

## Installation

To install it you must have already dialed installed. If not go to [Composer](https://getcomposer.org/)

Assuming you have [Composer](https://getcomposer.org/) installed already, go to your working directory and type the following commands

```bash
$ mkdir myapp
$ cd myapp
```

`myapp` is the name of your application

use the `composer init` command to create the` composer.json` file which will contain your application configurations and installed dependencies.
For more information on how the `package.json` file works, see [The composer.json schema](https://getcomposer.org/doc/04-schema.md)

```bash
$ composer init
```

This command asks you for a number of things, such as the name and version of your app. For now, you can just press enter to accept the defaults.

```bash
$ echo "" > .htaccess
```

The above command allows you to create the `.htaccess` file through which we rewrite the urls.

Once this file is created, please place the below content in this file

```htaccess
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(REQUEST_FILENAME) %{REQUEST_FILENAME} [L]

RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule (.*) index.php/$1 [L]
```

Now create the application entry point `index.php` file

```bash
$ echo "" > index.php
```

Now install `Ekolo Builder` by typing the command ci

```bash
$ composer require ekolobuilder/ekolo
```

[Previous - Getting started](https://github.com/ekolo-builder/ekolo)
[Next - Hello World](HelloWorld.md)