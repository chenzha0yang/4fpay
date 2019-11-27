<?php

namespace App\Models\Bank;

use App\Extensions\RedisConPool;
use App\Models\ApiModel;

class PaymentBank extends ApiModel
{
    protected $table = 'short_urls';

    protected $primaryKey = 'payment_id';

    protected $guarded = ['longUrl'];

    /**
     * 长链接 - 查询longUrl是否存在
     * @param $longUrl
     * @return array|mixed
     */
    public static function getBankUrl($longUrl,$agentDd)
    {
        $payArr = self::getPaymentRedis($agentDd);
        $payWay = [];
        foreach ($payArr as $key => $value) {
            if ($value['is_status'] == 1 && $longUrl == $value['payment_id']) {
                $payWay = $value;
            }
        }
        return $payWay;
    }

    /**
     * 长链接 - redis
     * @return mixed
     */
    public static function getPaymentRedis($agentDd)
    {
        $TypeRedisKey = $agentDd . "_PayMenBank";
        $payTypes = RedisConPool::get($TypeRedisKey);

        if ($payTypes) {
            $payTypes = json_decode($payTypes, true);
        } else {
            $payTypes = self::where("is_status", 1)->get()->toArray();
            RedisConPool::set($TypeRedisKey, json_encode($payTypes));
        }
        return $payTypes;
    }

    /**
     * 长链接 - 需要修改的数据
     * @param $put
     * @return array
     */
    public static function saveData($put)
    {
        $data = [];
        if (isset($put['long_url'])) {
            $data['longUrl'] = $put['long_url'];
        }
        if (isset($put['status'])) {
            $data['is_status'] = $put['status'];
        }
        return $data;
    }

    /**
     * 长链接 - 需要添加的数据
     * @param $post
     * @return array
     */
    public static function addData($post)
    {
        $data = [
            'longUrl'   => $post['long_url'],
            'is_status' => $post['status'],
            'agent_id'  => $post['agentLine'],
            'agent_num' => $post['subAgentLine'],
        ];
        return $data;
    }

    /***
     * 长链接 - 添加后返回伪装参数
     * @param $result
     * @return array
     */
    public static function returnDataCamouflage($result)
    {
        $data = [[
                     'relevanceId' => (int)$result->payment_id,
                     'longUrl'      => (int)$result->long_rl,
                     'status'       => (string)$result->is_status,
                     'agentLine'    => (string)$result->agent_id,
                     'subAgentLine' => (string)$result->agent_num,
                 ]];
        return $data;
    }
}
