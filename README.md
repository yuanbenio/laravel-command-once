# Yuanben Command Once

## Installion

To get started with Command Once, add to your `composer.json` file as a dependency:

    composer require yuanben/laravel-command-once

## Configure

After installing the Command Once library, register the `Yuanben\CommandOnce\CommandOnceServiceProvider` in your `config/app.php` configuration file:

    'providers' => [
        // Other service providers...

        Yuanben\CommandOnce\CommandOnceServiceProvider::class,
    ]

## Publish

Next, you need execute publish command:

    php artisan vendor:publish
    
This command will publish `command.php` config file to you application config directory. also, it will publish create command once table migration file to you migration directory. So, you also need to do migrate:

    php artisan migrate
    
## Usage

Within `command.php` file, you need list what commands do you want execute just only once:

    return [
        'execs' => [
            'storage:link' => 'v0.0.1',
            'db:seed' => 'my version',
        ]
    ];

Then you can execute command like this:

    php artisan command:once
    
This command will compare the listed command and version above with the database, if they never executed, the command will do it for you.
