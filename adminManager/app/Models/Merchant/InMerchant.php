<?php

namespace App\Models\Merchant;

use App\Extensions\RedisConPool;
use App\Models\ApiModel;
use App\Models\Config\PayType;
use App\Models\Config\PayConfig;
use App\Models\Config\ApiClients;

class InMerchant extends ApiModel
{
    protected $table = 'pay_merchant';

    protected $primaryKey = 'merchant_id';

    protected $guarded = ['merchant_id'];

    public static $MerchantRedisKey = 'payMerchant_';

    /**
     * 入款商户-支付类型关联
     */

    public function payWays()
    {
        return $this->belongsTo(PayType::class, 'pay_way', 'type_id');
    }

    /**
     * 入款商户-商户配置
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
        $data = [];
        if (isset($get['privateKey'])) {
            $data['rsa_private_key'] = $get['privateKey'];
        }
        if (isset($get['msgOne'])) {
            $data['message1'] = $get['msgOne'];
        }
        if (isset($get['msgTwo'])) {
            $data['message2'] = $get['msgTwo'];
        }
        if (isset($get['msgThr'])) {
            $data['message3'] = $get['msgThr'];
        }
        if (isset($get['publicKey'])) {
            $data['public_key'] = $get['publicKey'];
        }
        if (isset($get['callbackURL'])) {
            $data['callback_url'] = $get['callbackURL'];
        }
        if (isset($get['merURL'])) {
            $data['mer_url'] = $get['merURL'];
        }
        if (isset($get['businessNum'])) {
            $data['business_num'] = $get['businessNum'];
        }
        if (isset($get['status'])) {
            $data['is_status'] = $get['status'];
        }
        if (isset($get['isApp'])) {
            $data['is_app'] = $get['isApp'];
        }
        if (isset($get['md5Key'])) {
            $data['md5_private_key'] = $get['md5Key'];
        }
        if (isset($get['payId'])) {
            $data['pay_id'] = $get['payId'];
        }
        if (isset($get['payId'])) {
            $data['pay_way'] = $get['typeId'];
        }
        if (isset($get['agentId'])) {
            $data['agent_id'] = $get['agentId'];
        }
        if (isset($get['clientUserId'])) {
            $data['client_id'] = $get['clientUserId'];
        }
        if (isset($get['payCode'])) {
            $data['pay_code'] = $get['payCode'];
        }

        return $data;
    }

    /**
     * 代理 商户信息 缓存
     *
     * @param $clientId
     * @param $agentLine
     * @param $agentNum
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public static function getMerchant($clientId, $agentLine, $agentNum)
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