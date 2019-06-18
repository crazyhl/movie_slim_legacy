<?php


namespace App\Command;


use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class TestProcess extends BaseCommand
{
    use LockableTrait;

    // the name of the command (the part after "bin/console")
    // 这个name 可以不用了，因为我们在config里面会配置name
    protected static $defaultName = 'test:process';

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
        $process = new Process(['php', '/var/www/movie_slim_legacy/console/index.php', 'test:command']);
        $process->start();
        $this->container->logger->info("process 开始". microtime(true));
        $code = $process->stop(5);
        $code2 = $process->wait();
        $this->container->logger->info("process 结束" . $code . '--' . $code2);


        // 释放锁
        $this->release();
    }
}