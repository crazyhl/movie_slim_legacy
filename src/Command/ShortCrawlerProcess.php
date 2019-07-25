<?php


namespace App\Command;


use App\Model\CronJob;
use App\Service\SourceMovieWebSite;
use Carbon\Carbon;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShortCrawlerProcess extends BaseCommand
{
    use LockableTrait;

    // the name of the command (the part after "bin/console")
    // 这个name 可以不用了，因为我们在config里面会配置name
    protected static $defaultName = 'task:short';

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
        // 设置运行不超时
        set_time_limit(0);
        usleep(rand(100000, 1000000));
        // 检查锁
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }
        // 在获取数据的时候上锁，获取完后就释放锁，代码层面保障数据唯一性
        $cronJob = CronJob::where('name', 'shortTask')
            ->where('type', 2)
            ->where('status', 0)
            ->where('execute_time', '<=', Carbon::now()->timestamp)
            ->first();
        // 清理过期任务
        CronJob::where('name', 'shortTask')
                ->where('type', 2)
                ->where('status', 1)
                ->where('start_time', '<=', Carbon::now()->timestamp -  3600)
                ->delete();

        if (empty($cronJob)) {
//            $this->container->logger->info('本次没有获取短期任务执行');
            return 0;
        }

        // 如果有任务就要更新状态了
        $cronJob->start_time = Carbon::now()->timestamp;
        $cronJob->status = 1; // 正在执行
        $cronJob->save();
        // 插入下一次执行的任务
        $newJob = new CronJob();
        $newJob->name = $cronJob->name;
        $newJob->params = $cronJob->params;
        $newJob->type = $cronJob->type;
        $newJob->execute_time = $cronJob->execute_time + 3600;
        $newJob->max_execute_time = 0; // 一个任务执行3天不过分
        $newJob->start_time = 0;
        $newJob->status = 0;
        $newJob->save();
        // 释放锁
        $this->release();
        // 获取了数据之后就去爬数据吧
        $params = json_decode($cronJob->params, true);

        $websiteId = $params['webSiteId'];

        $info= SourceMovieWebSite::getDayMovies($websiteId);
        $output->writeln($info);

        // 然后修改数据库
        $cronJob->delete();
    }
}