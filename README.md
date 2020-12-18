:exclamation: :exclamation:

This Bundle has been useful, but is not necessary anymore since Doctrine Migrations Bundle 2.2.
Prefer using multiple directories from doctrine migrations bundle :)

https://symfony.com/doc/2.2/bundles/DoctrineMigrationsBundle/index.html#configuration

I won't maintain this bundle anymore :)
Thank you all !

# DataMigrationsBundle

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Build Status](https://travis-ci.com/GregoireHebert/data-migrations-bundle.svg?branch=1.3)](https://travis-ci.com/GregoireHebert/data-migrations-bundle)

This bundle is the missing piece of the [Doctrine2 Migrations bundle](https://symfony.com/doc/master/bundles/DoctrineMigrationsBundle/index.html)
into Symfony applications. Data migrations help you version the changes in your data, and apply them in a predictable way on every server running the application.

## Installation

```sh
$ composer require gheb/data-migrations-bundle
```

The library can be found  at ``vendor/gheb/DataMigrationsBundle``.
Finally, be sure to enable the bundle in ``AppKernel.php`` by including the following:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        //...
        new Gheb\DataMigrationsBundle\DataMigrationsBundle(),
    );
}
```

## Configuration

You can configure the path, namespace, table_name, name, organize_migrations and custom_template in your ``config.yml``. The examples below are the default values.

```yml
# app/config/config.yml
data_migrations:
    dir_name: "%kernel.root_dir%/DataMigrations"
    namespace: "Application\\Migrations"
    table_name: "data_migration_versions"
    name: Application Data Migrations
    organize_migrations: false # Possible values are: "BY_YEAR", "BY_YEAR_AND_MONTH", false
    custom_template: ~ # Path to your custom migrations template
```

## Usage

### Caution

    If your application is based on Symfony 3, replace `php app/console` by `php bin/console` before executing any of the console commands included
    in this article.

All of the migrations functionality is contained in a few console commands:

```bash
 gheb:data-migrations:execute             [execute] Execute a single migration version up or down manually.
 gheb:data-migrations:generate            [generate] Generate a blank migration class.
 gheb:data-migrations:latest              [latest] Outputs the latest version number
 gheb:data-migrations:migrate             [migrate] Execute a migration to a specified version or the latest available version.
 gheb:data-migrations:status              [status] View the status of a set of migrations.
 gheb:data-migrations:version             [version] Manually add and delete migration versions from the version table.
```

## Documentation

This bundle is based on the doctrine migrations bundle.
If you need more information, please refer to the [original bundle documentation](https://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html).

## Running tests & cs checks

`$ ./vendor/bin/phpunit --bootstrap vendor/autoload.php --testdox tests`
`$ ./vendor/bin/php-cs-fixer fix --config .php_cs.dist --verbose --dry-run src`
