<?php


namespace App\Command;


use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand2 extends BaseCommand
{
    // the name of the command (the part after "bin/console")
    // 这个name 可以不用了，因为我们在config里面会配置name
    protected static $defaultName = 'test:command2';

    /**
     * 这东西就是写一些参数说明或者参数约定用的
     */
    protected function configure()
    {
        // ...
        $this->setHelp('这是一个测试用的命令 help 文本');
    }

    /**
     * 真正执行命令的地方
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $procStatus = proc_get_status();
        // 检查锁

        // ...
        $this->container->logger->info("来自控制台的日志" . $procStatus['pic'], [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'status' => '开始执行耗时任务',
        ]);
        sleep(10);

        $this->container->logger->info("来自控制台的日志" . $procStatus['pic'], [
            'a' => 1,
            'b' => 2,
            'c' => 3,
            'status' => '开始执行耗时任务结束',
        ]);

        // 释放锁
    }
}