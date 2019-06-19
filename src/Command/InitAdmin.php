<?php


namespace App\Command;


use App\Model\User;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitAdmin extends BaseCommand
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
        $this->setHelp('初始化管理员');
        $this->addArgument('username', InputArgument::REQUIRED);
        $this->addArgument('password', InputArgument::OPTIONAL, '', '123456789');
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

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $user = new User();
        $user->username = $username;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->is_admin = 1;
        $user->save();

        $output->writeln('管理员' . $username . ' 创建完成 uid:' . $user->id);

        // 释放锁
        $this->release();
    }
}