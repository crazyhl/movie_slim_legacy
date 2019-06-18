<?php
if (PHP_SAPI == 'cli') {
    ini_set('date.timezone','Asia/Shanghai');

    require __DIR__ . '/../vendor/autoload.php';

    // Instantiate the app
    $settings = require __DIR__ . '/../config/settings.php';
    // 设置时区
    ini_set('date.timezone', $settings['tz']);

    $app = new \Slim\App($settings);

    // Set up dependencies
    $dependencies = require __DIR__ . '/../config/dependencies.php';
    $dependencies($app);

    // 从 settings 中获取 command
    $commands = isset($settings['commands']) ? $settings['commands'] : [];

    // 初始化 console Application
    $consoleApplication = new \Symfony\Component\Console\Application();
    // 获取 slim 的 container
    $container = $app->getContainer();
    // 遍历 commands 在 symfony console 中注册相关命令，
    // 并且把 slim的 container 中注入进去，这样就可以使用 slim 的 container 了
    /**
     * @var $command App\Command\BaseCommand
     */
    foreach ($commands as $commandName => $command) {
        // 依次注册每个命令
        $consoleApplication->add(new $command($container, $commandName));
    }

    $consoleApplication->run();
}

return false;


