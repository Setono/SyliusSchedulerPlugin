# SetonoSyliusSchedulerPlugin

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]

Send follow up emails to your customers to entice them to leave feedback for you. The plugin uses Schedulers [AFS service](https://support.scheduler.com/hc/en-us/articles/213703667-Automatic-Feedback-Service-AFS-2-0-setup-guide).

## Installation

* Install plugin using `composer`:

    ```bash
    $ composer require setono/sylius-scheduler-plugin
    ```

* Add bundle to `config/bundles.php`:

    ```php
    <?php
    // config/bundles.php
    
    return [
        // ...
        Setono\SyliusSchedulerPlugin\SetonoSyliusSchedulerPlugin::class => ['all' => true],
    ];
    ```

# Contribution

## Installation

To automatically execute installation steps, load fixtures 
and run server with just one command, run:

```bash
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

[ico-version]: https://img.shields.io/packagist/v/setono/sylius-scheduler-plugin.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Setono/SyliusSchedulerPlugin/master.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Setono/SyliusSchedulerPlugin.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/setono/sylius-scheduler-plugin
[link-travis]: https://travis-ci.org/Setono/SyliusSchedulerPlugin
[link-code-quality]: https://scrutinizer-ci.com/g/Setono/SyliusSchedulerPlugin
