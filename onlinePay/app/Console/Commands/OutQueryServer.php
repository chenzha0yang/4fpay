<?php

namespace App\Console\Commands;

use App\WorkerManServer\OutOrder\OutOrStart;
use App\WorkerManServer\OutOrder\OutQueryStart;
use Illuminate\Console\Command;
use Workerman\Worker;


class OutQueryServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'out:query {action} {--d}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Out Query Server';

    /**
     * Create a new command instance.
     *
     * @return void
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
        if (!in_array($action, ['start', 'stop', 'restart', 'status'])) {
            $this->error('Error Arguments');
            exit;
        }
        $argv[0]                    = 'out:query';
        $argv[1]                    = $action;
        $argv[2]                    = $this->option('d') ? '-d' : '';
        Worker::$logFile            = storage_path('logs/outQuery.log');
        Worker::$pidFile            = storage_path('logs/outQuery.pid');
        $outQueryServer             = new Worker('http://0.0.0.0:4324');
        $outQueryServer->count      = 1;
        $outQueryServer->conn       = [];
        $outQueryServer->task       = [];
        $outQueryServer->name       = 'OutQuery';
        $outQueryServer->listenData = [
            '1113'
        ];
        configFormat();
        //主程序
        OutOrStart::instance($outQueryServer);
        //出款查询
        OutQueryStart::instance($outQueryServer);
        $outQueryServer::runAll();
    }
}