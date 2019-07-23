<?php


namespace App\Command;


use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDataBase extends BaseCommand
{
    use LockableTrait;

    // the name of the command (the part after "bin/console")
    // 这个name 可以不用了，因为我们在config里面会配置name
//    protected static $defaultName = 'test:process';

    /**
     * 这东西就是写一些参数说明或者参数约定用的
     */
    protected function configure()
    {
        // ...
        $this->setHelp('初始化数据库用的');
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

        // 初始化db连接，否则连不上数据库
        $this->container->db;

        // 用户
        $tableName = 'user';
        Manager::schema()->dropIfExists($tableName);
        Manager::schema()->create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique()->comment('用户名');
            $table->string('password', 255)->comment('密码');
            $table->tinyInteger('is_admin')->default(0)->comment('是否是管理员标识');
            $table->string('token')->default('')->unique()->comment('是否是管理员标识');
            $table->timestamps();
        });
        $output->writeln($tableName . ' 创建完成');
        // 分类
        $tableName = 'category';
        Manager::schema()->dropIfExists($tableName);
        Manager::schema()->create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('分类名称');
            $table->string('slug')->unique()->comment('分类别名用来url跳转用的');
            $table->integer('parent_id')->index()->default(0)->comment('父分类id');
            $table->tinyInteger('is_show')->default(0)->comment('是否外显');
            $table->tinyInteger('order')->default(0)->comment('排序，数字越大越靠前');
            $table->timestamps();
        });
        $output->writeln($tableName . ' 创建完成');
        // 来源网站
        $tableName = 'source_movie_website';
        Manager::schema()->dropIfExists($tableName);
        Manager::schema()->create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('来源网站名称');
            $table->string('api_url')->unique()->comment('来源网站 api url');
            $table->tinyInteger('status')->default(0)
                ->comment('状态，不准备删除资源网站，可以禁用，不使用');
            $table->timestamps();
        });
        $output->writeln($tableName . ' 创建完成');
        // 定时任务
        $tableName = 'cron_job';
        Manager::schema()->dropIfExists($tableName);
        Manager::schema()->create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index()
                ->comment('就是 command 的name');
            $table->string('params')->index()
                ->comment('json 形式的参数，在执行的时候会被映射为 --xxx=xxx --aaa=aaa 这种');
            $table->tinyInteger('type')->index()
                ->comment('任务类型，1 一次性任务 2 每小时 执行一次的任务，3 每天执行一次 ....');
            $table->integer('execute_time')->unsigned()
                ->comment('任务执行的时间，就是在遍历的时候如果这个时间小于当前时间了，就说明可以执行了');
            $table->integer('max_execute_time')->unsigned()
                ->comment('任务执行的最大时间，如果超过这个时间了，会考虑给杀死进程,如果是0 则不限');
            $table->integer('start_time')->unsigned()
                ->comment('开始时间');
            $table->tinyInteger('status')->unsigned()
                ->comment('任务执行状态 0 未执行 1 正在执行');
            $table->timestamps();
        });
        $output->writeln($tableName . ' 创建完成');

        // 来源站分类和本地分类关联表
        $tableName = 'source_website_category_local_category_relation';
        Manager::schema()->dropIfExists($tableName);
        Manager::schema()->create($tableName, function (Blueprint $table) {
            $table->integer('source_website_id');
            $table->integer('source_website_category_id');
            $table->integer('local_category_id');
            $table->tinyInteger('is_show')->comment('就是来源网站下的这个分类抓回来的影片是否显示出来');
            $table->unique([
                'source_website_id',
                'source_website_category_id',
                'local_category_id',
            ],'source_cate_id_local_cate_id');
        });
        $output->writeln($tableName . ' 创建完成');

        // 电影
        $tableName = 'movie';
        Manager::schema()->dropIfExists($tableName);
        Manager::schema()->create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('影片名称');
            $table->string('name_md5')->unique()->comment('影片名称md5 用来做唯一性验证');
            $table->string('category_id')->index()->comment('本地分类id');
            $table->string('pic')->comment('本地图片路径');
            $table->string('lang')->comment('语言');
            $table->string('area')->comment('地区');
            $table->string('year')->comment('年份');
            $table->string('note')->comment('简要说明');
            $table->string('actor')->comment('演员');
            $table->string('director')->comment('导演');
            $table->text('description')->comment('简介');
            $table->integer('is_show')->comment('是否外显');
            $table->timestamps();
        });
        $output->writeln($tableName . ' 创建完成');

        // 源站电影
        $tableName = 'source_movie';
        Manager::schema()->dropIfExists($tableName);
        Manager::schema()->create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('source_website_id')->comment('源站id');
            $table->integer('source_website_category_id')->comment('源站分类id');
            $table->integer('source_website_movie_id')->comment('源站影片id');
            $table->integer('local_id')->index()->comment('本地影片id');
            $table->string('name')->comment('影片名称');
            $table->string('name_md5')->index()->comment('影片名称md5 用来做唯一性验证');
            $table->string('category_id')->index()->comment('本地分类id');
            $table->string('pic')->comment('本地图片路径');
            $table->string('lang')->comment('语言');
            $table->string('area')->comment('地区');
            $table->string('year')->comment('年份');
            $table->string('note')->comment('简要说明');
            $table->string('actor')->comment('演员');
            $table->string('director')->comment('导演');
            $table->text('description')->comment('简介');
            $table->text('movie_list')->comment('影片列表');
            $table->timestamps();

            $table->index([
                'source_website_id',
                'source_website_movie_id',
            ]);
        });
        $output->writeln($tableName . ' 创建完成');

//        // 资源图片
        $tableName = 'resource_image';
        Manager::schema()->dropIfExists($tableName);
        Manager::schema()->create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_md5')->unique()->comment('文件md5');
            $table->string('file_path')->unique()->comment('文件本地路径');
            $table->timestamps();
        });
        $output->writeln($tableName . ' 创建完成');

        // 释放锁
        $this->release();
    }
}