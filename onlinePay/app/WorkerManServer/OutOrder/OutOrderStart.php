<?php

namespace App\WorkerManServer\OutOrder;

use App\Http\Controllers\WorkerController;
use Workerman\Connection\AsyncTcpConnection;

/**
 *
 *
 * Class OutOrder
 * @package App\WorkerManServer\OutOrder
 */
class OutOrderStart extends WorkerController
{
    public static function instance($serverDrive)
    {
        $serverDrive->onWorkerStart = function ($serverDrive) {
            echo 'start success' . PHP_EOL;
            foreach ($serverDrive->listenData as $value) {
                $$value = new AsyncTcpConnection(self::protocol($value));
                $$value->connect();
            }
        };

        $serverDrive->onMessage = function ($connection, $data) use ($serverDrive) {
            ///参数校验
            if (isset($_GET['type'])) { //启停
                switch ($_GET['type']) {
                    case 'restart':
                        break;

                    default:
                        # code...
                        break;
                }
            }
            $connection->send("<font style='color:green'>正常</font>");
            return;
        };
    }
}