<?php

namespace App\WorkerManServer\OutOrder;

use App\Extensions\RedisConPool;
use App\Http\Controllers\OutOrder\SendOutOrder;
use App\Http\Controllers\WorkerController;
use Workerman\Lib\Timer;
use Workerman\Worker;

/**
 * 出款请求
 *
 * Class OutSendStart
 * @package App\WorkerManServer\OutOrder
 */
class OutSendStart extends WorkerController
{
    const PORT = 1112;

    private static $OutKey = 'OutMoneyListKey'; //出款列表key

    public static function instance($serverDrive)
    {
        $sendOrderWorker            = new Worker(self::protocol(self::PORT));
        $sendOrderWorker->name      = 'outSendOrderStart';
        $sendOrderWorker->onConnect = function ($connection) use ($serverDrive, $sendOrderWorker) {
            self::sendOrder();
            $sendOrderWorker->listen();
        };
    }

    private static function sendOrder()
    {
        $time_interval = 3;  //执行间隔时间 (s)
        Timer::add($time_interval, function () {
            for ($i = 1; $i <= 1; $i++) {
                $data = RedisConPool::lPop(self::$OutKey);
                if ($data) {
                    echo PHP_EOL . 'outSendOrderStart 40行 ' . $data . PHP_EOL;
                    SendOutOrder::sendOrder(json_decode($data, true));
                }
            }
        });
    }
}