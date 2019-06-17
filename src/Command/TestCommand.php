<?php


namespace App\Command;


use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends BaseCommand
{
    use LockableTrait;

    // the name of the command (the part after "bin/console")
    // 这个name 可以不用了，因为我们在config里面会配置name
    protected static $defaultName = 'test:command';

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
        // 检查锁
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }
        // ...
        $output->writeln('console 控制台输出1');
        sleep(10);
        $output->writeln('console 控制台输出2');

        $this->container->logger->info("来自控制台的日志");

        // 释放锁
        $this->release();
    }
}