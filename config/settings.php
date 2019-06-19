<?php

use App\Command\InitAdmin;
use App\Command\InitDataBase;
use App\Command\TestCommand2;
use App\Command\TestProcess;
use App\Command\TestCommand;

return [
    // slim settings
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'view' => [
            'template_path' => __DIR__ . '/../templates/',
            'cache' => false,
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        'db' => [
            'driver' => 'mysql',
            'host' => 'mysql',
            'database' => 'movie',
            'username' => 'root',
            'password' => '123456789',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
        ],
    ],
    // console commands
    'commands' => [
        'test:command' => TestCommand::class,
        'test:process' => TestProcess::class,
        'test:command2' => TestCommand2::class,
        'db:init' => InitDataBase::class,
        'admin:init' => InitAdmin::class,
    ],
    'tz' => 'Asia/Shanghai',
];
