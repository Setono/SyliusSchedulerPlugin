# SetonoSyliusSchedulerPlugin

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

Sylius plugin to schedule jobs at admin panel and track their status.

![Admin screenshot][image-admin-screenshot]

## Installation

* Install plugin using `composer`:

    ```bash
    $ composer require setono/sylius-scheduler-plugin
    ```

* Add bundle to `config/bundles.php` before (!) `SyliusResourceBundle`:

    ```php
    <?php
    // config/bundles.php
    
    return [
        // ...
        Setono\SyliusSchedulerPlugin\SetonoSyliusSchedulerPlugin::class => ['all' => true],
        Sylius\Bundle\ResourceBundle\SyliusResourceBundle::class => ['all' => true],
    ];
    ```

* Import config:

    ```yaml
    # config/packages/_sylius.yaml
    imports:
        # ...
        - { resource: "@SetonoSyliusSchedulerPlugin/Resources/config/app/config.yaml" }
    ```

* Import routes:

    ```yaml
    # config/routes.yaml
    setono_sylius_scheduler_admin:
        resource: "@SetonoSyliusSchedulerPlugin/Resources/config/admin_routing.yaml"
        prefix: /admin
    ```

* Update your schema (for existing project):

    ```bash
    # Generate and edit migration
    bin/console doctrine:migrations:diff

    # Then apply migration
    bin/console doctrine:migrations:migrate
    ```

## Plugin configuration

```yaml
setono_sylius_scheduler:
    driver: doctrine/orm

    # Wipe logs in X days after command execution
    # Specify 0 to never wipe logs
    wipe_logs_in: 0

    # We can specify emails to receive error reports on every Job
    # But here we can specify emails which receive error reports for all Jobs
    error_report_emails: []
```

# Contribution

## Installation

To automatically execute installation steps, load fixtures 
and run server with just one command, run:

```bash
# Optional step, if 5 mins enough for webserver to try
# @see https://getcomposer.org/doc/06-config.md#process-timeout
composer config --global process-timeout 0

composer try
```

or follow next steps manually:

* Initialize:

    ```bash
    SYMFONY_ENV=test
    (cd tests/Application && yarn install) && \
        (cd tests/Application && yarn build) && \
        (cd tests/Application && bin/console assets:install public -e $SYMFONY_ENV) && \
        (cd tests/Application && bin/console doctrine:database:create -e $SYMFONY_ENV) && \
        (cd tests/Application && bin/console doctrine:schema:create -e $SYMFONY_ENV)
    ```

* If you want to manually play with plugin test app, run:

    ```bash
    SYMFONY_ENV=test
    (cd tests/Application && bin/console sylius:fixtures:load --no-interaction -e $SYMFONY_ENV && \
        (cd tests/Application && bin/console server:run -d public -e $SYMFONY_ENV)
    ```

## Running plugin tests

  - PHPSpec

    ```bash
    $ composer phpspec
    ```

  - Behat

    ```bash
    $ composer behat
    ```

  - All tests (phpspec & behat)
 
    ```bash
    $ composer test
    ```
    
## Pushing changes & making PRs

Please run `composer all` to run all checks and tests before making PR or pushing changes to repo.

[image-admin-screenshot]: docs/images/admin.png 

[ico-version]: https://img.shields.io/packagist/v/setono/sylius-scheduler-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Setono/SyliusSchedulerPlugin/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/SyliusSchedulerPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/sylius-scheduler-plugin
[link-travis]: https://travis-ci.org/Setono/SyliusSchedulerPlugin
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusSchedulerPlugin
