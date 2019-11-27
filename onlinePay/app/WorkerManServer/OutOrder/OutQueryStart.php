<?php

namespace App\WorkerManServer\OutOrder;

use App\Extensions\RedisConPool;
use App\Http\Controllers\OutOrder\SendOutOrder;
use App\Http\Controllers\WorkerController;
use Workerman\Lib\Timer;
use Workerman\Worker;

/**
 * 出款查询
 *
 * Class OutQueryStart
 * @package App\WorkerManServer\OutOrder
 */
class OutQueryStart extends WorkerController
{
    const PORT = 1113;

    private static $OutQyKey = 'outQueryList'; //出款查询队列

    public static function instance($serverDrive)
    {
        $queryOrderWorker            = new Worker(self::protocol(self::PORT));
        $queryOrderWorker->name      = 'outQueryOrderStart';
        $queryOrderWorker->onConnect = function ($connection) use ($serverDrive, $queryOrderWorker) {
            self::queryOrder($serverDrive);
            $queryOrderWorker->listen();
        };
    }

    private static function queryOrder()
    {
        $time_interval = 3;  //执行间隔时间 (s)
        $timeID        = Timer::add($time_interval, function () use (&$timeID) {
            $data = RedisConPool::lLen(self::$OutQyKey);
            static $i = 5;
            $i--;
            // 若为空列表，运行5次后删除当前定时器
            if ($i === 0 && empty($data)) {
                Timer::del($timeID);
                echo '暂无查询队列' . PHP_EOL;
            } else {
                Timer::add(30, function () { //30s执行一次查询查询
                    $data = RedisConPool::lRange(self::$OutQyKey, 0, 14);
                    RedisConPool::lTrim(self::$OutQyKey, 15, -1);
                    foreach ($data as $redisListData) {
                        echo 'OutQueryStart.php 获取查询队列: ' . $redisListData;
                        SendOutOrder::ListDataDo((json_decode($redisListData, true)));
                    }
                });
            }
        });
    }
}