<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class SendCallbackLog extends Model
{
    protected $table = 'send_callback_log';

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * 下发日志
     *
     * @param  string $order 订单号
     * @param  string $returnMsg 响应信息
     * @param  array $array 下发数据
     * @param  string $callbackUrl 下发地址
     * @param  int $issue 是否自动下发 1: 自动; 2: 手动
     * @param  int $way 下发方式：1 入款; 2 出款下发
     * @return SendCallbackLog|Model
     */
    public static function createLog($order, $returnMsg, $array, $callbackUrl, $issue, $way)
    {
        if ($returnMsg === 'SUCCESS') {
            $httpCode = 200;
        } else {
            $httpCode = $returnMsg;
        }
        $data['order']        = $order;
        $data['return_msg']   = $returnMsg;
        $data['send_msg']     = json_encode($array);
        $data['callback_url'] = $callbackUrl;
        $data['http_code']    = $httpCode;
        $data['is_auto_send'] = $issue;
        $data['way']          = $way;
        return self::create($data);
    }
}