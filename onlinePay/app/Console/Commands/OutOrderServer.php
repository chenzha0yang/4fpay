<?php

namespace App\Console\Commands;

use App\WorkerManServer\OutOrder\OutOrderStart;
use App\WorkerManServer\OutOrder\OutSendStart;
use Illuminate\Console\Command;
use Workerman\Worker;

/**
 * 出款
 *
 * Class OutOrderServer
 * @package App\Console\Commands
 */
class OutOrderServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'out:order {action} {--d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Out Order Server';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        global $argv;
        $action = $this->argument('action');
        $option = $this->option('d') ? '-d' : '';
        // 限制可以执行的命令 可以不写
        if (!in_array($action, ['start', 'stop', 'restart', 'status'])) {
            $this->error('Error Arguments');
            exit;
        }

        $argv[0]                  = 'out:order';
        $argv[1]                  = $action;
        $argv[2]                  = $option;
        Worker::$logFile          = storage_path('logs/outOrder.log');
        Worker::$pidFile          = storage_path('logs/outOrder.pid');
        $outOrderServer             = new Worker('http://0.0.0.0:1111');
        $outOrderServer->count      = 1;
        $outOrderServer->conn       = array();
        $outOrderServer->task       = array();
        $outOrderServer->name       = 'outOrder';
        $outOrderServer->listenData = array(
            '1112'
        );
        configFormat();
        // 出款主程序 http 1111
        OutOrderStart::instance($outOrderServer);
        // 出款请求 text 1112
        OutSendStart::instance($outOrderServer);
        $outOrderServer::runAll();
    }

}
