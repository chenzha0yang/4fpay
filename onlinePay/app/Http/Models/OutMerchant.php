<?php

namespace App\Http\Models;

use \Illuminate\Database\Eloquent\Model;
use App\Extensions\RedisConPool;
use Illuminate\Support\Facades\Cache;

class OutMerchant extends Model
{
    protected $table = 'pay_out_merchant';

    protected $primaryKey = 'merchant_id';

    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @param $merConfig
     * @return array|mixed
     */
    public static function getMerchant($merConfig)
    {
        return self::where('agent_id', $merConfig['agentId'])
            ->where('agent_num', $merConfig['agentNum'])
            ->where('merchant_id', $merConfig['merchantId'])
            ->where('is_status', 1)
            ->first();
    }


    /**
     * @param $where
     * @return bool|mixed|string
     */
    public static function getMerchantRedis($where)
    {
        $redisKey = "payMerchant_{$where['agent_id']}{$where['agent_num']}";
        $payMerchant = RedisConPool::get($redisKey);
        if (empty($payMerchant)) {
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
        return self::where('agent_id', $where['agent_id'])
            ->where('agent_num', $where['agent_num'])
            ->where('is_status', 1)
            ->orderBy('pay_way', 'asc')
            ->get();
    }
}