<?php

namespace App\Http\Models;

use \Illuminate\Database\Eloquent\Model;
use App\Extensions\RedisConPool;

class PayMerchant extends Model
{
    protected $table = 'pay_merchant';

    protected $primaryKey = 'merchant_id';

    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @param $merConfig
     * @return array|bool|object
     */
    public static function getMerchant($merConfig)
    {
        $redisKey    = "payMerchant_{$merConfig['client_id']}{$merConfig['agentId']}{$merConfig['agentNum']}";
        $payMerchant = RedisConPool::get($redisKey);
        if (empty($payMerchant) || $payMerchant == '[]') {
            $payMerchant = self::getMerchantList($merConfig);
            RedisConPool::set($redisKey, json_encode($payMerchant));
            sleep(1);
            $payMerchant = RedisConPool::get($redisKey);
        }
        if($payMerchant){
            $payMerchant = json_decode($payMerchant);
            $merchant = [];
            foreach ($payMerchant as $key => $value) {
                if ($value->merchant_id == $merConfig['merchantId'] && $value->pay_way == $merConfig['payWay']) {
                    $merchant = $value;
                    break;
                }
            }
            return $merchant;
        }
        return false;
    }

    /**
     * @param $where
     * @return bool|mixed|string
     */
    public static function getMerchantRedis($where)
    {
        $redisKey    = "payMerchant_{$where['client_id']}{$where['agentId']}{$where['agentNum']}";
        $payMerchant = RedisConPool::get($redisKey);
        if (empty($payMerchant) || $payMerchant == '[]') {
            $payMerchant = self::getMerchantList($where);
            RedisConPool::set($redisKey, json_encode($payMerchant));
        } else {
            $payMerchant = json_decode($payMerchant);
        }
        return $payMerchant;
    }

    /**
     * @param $where
     * @return \Illuminate\Support\Collection
     */
    public static function getMerchantList($where)
    {
        return self::where('agent_id', $where['agentId'])
            ->where('agent_num', $where['agentNum'])
            ->where('client_id', $where['client_id'])// 当前线路
            ->where('is_status', 1)
            ->orderBy('pay_way', 'asc')
            ->get();
    }
}