<?php


namespace App\Command;


use App\Model\CronJob;
use App\Service\SourceMovieWebSite;
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
        // 设置运行不超时
        set_time_limit(0);

        sleep(rand(1, 10));
        // 检查锁
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $cronJob = CronJob::where('name', 'fullTask')
            ->where('type', 1)
            ->where('status', 0)
            ->where('execute_time', '<=', Carbon::now()->timestamp)
            ->first();

        if (empty($cronJob)) {
            $cronJob = CronJob::where('name', 'fullTask')
                ->where('type', 1)
                ->where('status', 1)
                ->where('start_time', '<=', Carbon::now()->timestamp - 86400 * 3)
                ->first();
        }

        if (empty($cronJob)) {
//            $this->container->logger->info('本次没有获取全部任务执行');
            return 0;
        }

        // 如果有任务就要更新状态了
        $cronJob->start_time = Carbon::now()->timestamp;
        $cronJob->status = 1; // 正在执行
        $cronJob->save();
        // 释放锁
        $this->release();
        // 获取了数据之后就去爬数据吧
        $params = json_decode($cronJob->params, true);

        $websiteId = $params['webSiteId'];

        $info= SourceMovieWebSite::getFullMovies($websiteId);
        $output->writeln($info);

        // 然后修改数据库
        $cronJob->delete();
    }
}