<?php

namespace App\Http\Controllers\V1;

use App\Models\Merchant\InMerchant;
use App\Http\Controllers\ApiController;

class MerchantController extends ApiController
{
    const OPEN_THIS_API = false;

    public function runApi(&$params)
    {
        // 关闭接口对接权限
        if (self::OPEN_THIS_API) {
            return $this->replaceArray(['code' => '401']);
        }
        //验证代理是否存在
        if (empty($params['agentLine']) || empty($params['subAgentLine'])) {
            return $this->replaceArray(['code' => '1015']);
        }

        $preg    = '/^[a-z0-9]{1,10}$/i';
        $payPreg = '/^[0-9]{1,4}$/';

        $where = [];

        // 代理线验证，只有小写的1到3位的字母通过
        if (self::checkParameter($params, 'agentLine', $preg)) {
            return $this->replaceArray(['code' => '1015']);
        }

        // 子代理线验证
        if (self::checkParameter($params, 'subAgentLine', $preg, false)) {
            return $this->replaceArray(['code' => '1015']);
        }

        // 三方配置id验证
        if (self::checkParameter($params, 'payId', $payPreg, false)) {
            return $this->replaceArray(['code' => '1022']);
        }

        // 支付类型验证
        if (self::checkParameter($params, 'payType', '/^[0-9]{1,2}$/', false)) {
            return $this->replaceArray(['code' => '1027']);
        }

        // 状态验证
        if (self::checkParameter($params, 'payState', '/^[1,2]{1}$/', false)) {
            return $this->replaceArray(['code' => '1016']);
        }

        //三方支付ID验证
        if (self::checkParameter($params, 'merId', '/^[0-9]+$/', false)) {
            return $this->replaceArray(['code' => '1016']);
        }

        // 商户号
        if (self::checkParameter($params, 'merchantId', '', false)) {
            return $this->replaceArray(['code' => '1026']);
        }

        $where['agent_id']  = $params['agentLine'];
        $where['agent_num'] = $params['subAgentLine'];
        $where['client_id'] = $params['clientId'];
        //三方 配置类型
        if (!empty($params['payId'])) {
            $where['pay_id'] = $params['payId'];
        }
        //三方 支付类型
        if (!empty($params['payType'])) {
            $where['pay_way'] = $params['payType'];
        }
        //三方开关状态参数验证
        if (!empty($params['payState'])) {
            $where['is_status'] = $params['payState'];
        }
        // ID
        if (!empty($params['merId'])) {
            $where['merchant_id'] = $params['merId'];
        }

        //商户号
        if (!empty($params['merchantId'])) {
            $where['business_num'] = $params['merchantId'];
        }
        $where['client_id'] = $params['clientId'];
        $payMerchants       = InMerchant::getMerchant($params['clientId'], $params['agentLine'], $params['subAgentLine']);
        // 筛选数据
        foreach ($payMerchants as $k => $payMerchant) {
            foreach ($where as $ks => $vs) {
                if ($payMerchant->$ks != $vs) {
                    unset($payMerchants[$k]);
                }
            }
        }
        //参数伪装
        $data = [];
        if ($payMerchants) {
            foreach ($payMerchants as $key => $merchant) {
                $data[$key]['merId']         = (int)$merchant->merchant_id;    // id
                $data[$key]['merchantId']    = (string)$merchant->business_num;   // 商户号
                $data[$key]['payId']         = (int)$merchant->pay_id;         // 三方配置ID
                $data[$key]['payType']       = (int)$merchant->pay_way;        // 支付类型
                $data[$key]['md5PrivateKey'] = (string)$merchant->md5_private_key;    // md5私钥
                $data[$key]['rsaPrivateKey'] = (string)$merchant->rsa_private_key;    // rsa私钥
                $data[$key]['publicKey']     = (string)$merchant->public_key;     // 公钥
                $data[$key]['notifyUrl']     = (string)$merchant->callback_url;   // 回调地址
                $data[$key]['payStatus']     = (int)$merchant->is_status;      // 三方开关状态
                $data[$key]['payCode']       = (string)$merchant->pay_code;       // 编码自填
                $data[$key]['merUrl']        = (string)$merchant->mer_url;        // 支付网关自填
                $data[$key]['isApp']         = (int)$merchant->is_app;         // 跳转app/H5参数
                $data[$key]['remark1']       = (string)$merchant->message1;       // 预留字段信息1
                $data[$key]['remark2']       = (string)$merchant->message2;       // 预留字段信息2
                $data[$key]['remark3']       = (string)$merchant->message3;       // 预留字段信息3
                $data[$key]['levelId']       = (string)$merchant->user_level;     // 层级
            }
        }
        $data = array_values($data);
        return $this->replaceArray([
            'status' => true,
            'code'   => '200',
            'data'   => $data,
        ]);
    }
}