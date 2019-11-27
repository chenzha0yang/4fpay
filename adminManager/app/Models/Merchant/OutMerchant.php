<?php

namespace App\Models\Merchant;

use App\Models\ApiModel;
use App\Models\Config\PayType;
use App\Models\Config\PayConfig;
use App\Models\Config\ApiClients;
use App\Extensions\RedisConPool;

class OutMerchant extends ApiModel
{
    protected $table = 'pay_out_merchant';

    protected $primaryKey = 'merchant_id';

    protected $guarded = ['merchant_id'];

    public static $MerchantRedisKey = 'outPayMerchant_';



    /**
     * 出款商户-商户配置
     */
    public function payConfig()
    {
        return $this->belongsTo(PayConfig::class,'pay_id','pay_id');
    }
    /**
     * 入款商户-客户接口
     */
    public function clients()
    {
        return $this->belongsTo(ApiClients::class,'client_id','user_id');
    }
    public static function getEditData($get)
    {

        $updateData = [];
        if (isset($get['agentId'])) {
            $updateData['agent_id'] = $get['agentId'];
        }
        if (isset($get['clientUserId'])) {
            $updateData['client_id'] = $get['clientUserId'];
        }
        if (isset($get['businessNum'])) {
            $updateData['business_num'] = $get['businessNum'];
        }
        if (isset($get['callbackURL'])) {
            $updateData['callback_url'] = $get['callbackURL'];
        }
        if (isset($get['status'])) {
            $updateData['is_status'] = $get['status'];
        }
        if (isset($get['md5Key'])) {
            $updateData['md5_private_key'] = $get['md5Key'];
        }
        if (isset($get['privateKey'])) {
            $updateData['private_key'] = $get['privateKey'];
        }
        if (isset($get['publicKey'])) {
            $updateData['public_key'] = $get['publicKey'];
        }
        if (isset($get['payId'])) {
            $updateData['pay_id'] = $get['payId'];
        }

        return $updateData;
    }

    /**
     * 代理 商户信息 缓存
     *
     * @param $clientId
     * @param $agentLine
     * @param $agentNum
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public static function getOutMerchant($clientId, $agentLine, $agentNum)
    {
        $key = self::$MerchantRedisKey . $clientId . "_" . $agentLine . "_" . $agentNum;
        $merchants = RedisConPool::get($key);
        if ($merchants) {
            $payMerchants = json_decode($merchants);
        } else {
            $payMerchants = self::getLine($clientId, $agentLine, $agentNum);
            RedisConPool::set($key, json_encode($payMerchants));
        }
        return $payMerchants;
    }

    /**
     * @param $clientId
     * @param $agentLine
     * @param $agentNum
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getLine($clientId, $agentLine, $agentNum)
    {
        $where['agent_id']  = $agentLine;
        $where['agent_num'] = $agentNum;
        $where['client_id'] = $clientId;
        return self::MerChants($where);
    }

    /**
     * @param array $where
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function MerChants($where = [])
    {
        $MerchantModel = self::select();
        if (!empty($where)) {
            $MerchantModel->where($where);
        }
        return $MerchantModel->get();
    }
}