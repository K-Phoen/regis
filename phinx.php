<?php

$loader = require __DIR__.'/vendor/autoload.php';

$env = getenv('SYMFONY_ENV') ?: 'dev';

$kernel = new AppKernel($env, getenv('SYMFONY_DEBUG') ?: true);
$kernel->boot();

$connection = $kernel->getContainer()->get('doctrine.dbal.default_connection')->getWrappedConnection();

return [
    'version_order' => 'creation',

    'paths' => [
        'migrations' => __DIR__.'/app/Resources/db/migrations',
        'seeds' => __DIR__.'/app/Resources/db/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => $env,

        $env => [
            'name' => 'regis',
            'connection' => $connection,
        ],
    ],
];
