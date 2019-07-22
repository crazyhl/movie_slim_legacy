<?php


namespace App\Command;


use Carbon\Carbon;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FullCrawlerProcess extends BaseCommand
{
    use LockableTrait;

    // the name of the command (the part after "bin/console")
    // 这个name 可以不用了，因为我们在config里面会配置name
    protected static $defaultName = 'task:full';

    /**
     * 这东西就是写一些参数说明或者参数约定用的
     */
    protected function configure()
    {
        // ...
        $this->setHelp('这是一个测试用的命令 help 文本');
    }


//  */1 * * * * /usr/local/bin/php /var/www/movie_slim_legacy/console/index.php task:full >> /var/log/php/crontab.log 2>&1
//  */1 * * * * /usr/local/bin/php /var/www/movie_slim_legacy/console/index.php task:full >> /var/log/php/crontab.log 2>&1
//  */1 * * * * /usr/local/bin/php /var/www/movie_slim_legacy/console/index.php task:full >> /var/log/php/crontab.log 2>&1
//  */1 * * * * /usr/local/bin/php /var/www/movie_slim_legacy/console/index.php task:full >> /var/log/php/crontab.log 2>&1



    /**
     * 真正执行命令的地方
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        usleep(rand(100000, 1000000));
        // 检查锁
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }



        // 释放锁
        $this->release();
    }
}