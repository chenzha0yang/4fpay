<?php

namespace App\Http\Controllers\V1;

use App\Models\Merchant\OutMerchant;
use App\Models\Config\PayConfig;
use App\Models\Config\PayType;
use App\Http\Controllers\ApiController;
use App\Extensions\RedisConPool;

class OutBusinessController extends ApiController
{
    const AGENT_PREG  = '/^[a-z0-9]{1,10}$/';
    const PAY_PREG    = '/^[0-9]{1,4}$/';
    const MER_ID_PREG = '/^[0-9]+$/';
    const URL_PREG    = '/^((ht|f)tps?):\/\/[\w\-]+(\.[\w\-]+)+([\w\-\.,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?$/';

    const OPEN_THIS_API = false;
    /**
     * 逻辑处理方法体，每个类中必须要有此方法
     * 用于添加和修改商户
     * @param $params
     * @return array Runapi
     */

    public function runApi(&$params)
    {
        // 关闭接口对接权限
        if (self::OPEN_THIS_API) {
            return $this->replaceArray(['code' => '401']);
        }
        // 代理线验证，只有小写的1到3位的字母通过
        if (self::checkParameter($params, 'agentLine', self::AGENT_PREG)) {
            return $this->replaceArray(['code' => '1015']);
        }

        // 子代理线验证
        if (self::checkParameter($params, 'subAgentLine', self::AGENT_PREG, false)) {
            return $this->replaceArray(['code' => '1015']);
        }


        //三方支付ID验证
        if (self::checkParameter($params, 'merId', self::MER_ID_PREG, false)) {
            return $this->replaceArray(['code' => '1016']);
        }


        // 添加时必传参数
        if (empty($params['merId'])) {

            if (self::checkParameter($params, 'notifyUrl', self::URL_PREG)) {
                return $this->replaceArray(['code' => '1021']);
            }

            if (self::checkParameter($params, 'payId', self::PAY_PREG)) {
                return $this->replaceArray(['code' => '1022']);
            }
            // 商户号
            if (self::checkParameter($params, 'merchantId', '')) {
                return $this->replaceArray(['code' => '1026']);
            }


        } else {

            // 商户号
            if (self::checkParameter($params, 'merchantId', '', false)) {
                return $this->replaceArray(['code' => '1026']);
            }

            if (self::checkParameter($params, 'notifyUrl', self::URL_PREG, false)) {
                return $this->replaceArray(['code' => '1021']);
            }

            if (self::checkParameter($params, 'payId', self::PAY_PREG, false)) {
                return $this->replaceArray(['code' => '1022']);
            }
        }

        /******************************* 修改 ****************************/

        $data = $where = array();
        //三方 配置类型验证，1到3位的数字
        if (!empty($params['payId'])) {
            if (self::checkParameter($params, 'payId', self::PAY_PREG)) {
                return $this->replaceArray(['code' => '1016']);
            }

            $payId = PayConfig::getPayType($params['payId'],2);
            if ($payId) {
                return $this->replaceArray(['code' => '1024']);
            };
            $data['pay_id'] = (int)$params['payId'];     //三方类型id
        }


        /******************************* 修改end ****************************/
        $where['agent_id']     = (string)$params['agentLine'];    // 代理线
        $where['agent_num']    = isset($params['subAgentLine']) && empty($params['subAgentLine']) ? $params['subAgentLine'] : 'a'; // 子代理线
        $where['client_id']    = (int)$params['clientId'];
        // merId 为空表示添加商户信息
        if (empty($params['merId'])) {
            // 可以重复添加
            $where['merchant_id']     = 0;    //
            $data['client_id'] = $params['clientId'];
        } else {
            $where['merchant_id'] = (int)$params['merId'];            // id
            $result                = OutMerchant::where($where)->first();
            if (!$result) {
                return $this->replaceArray(['code' => '1017']);
            }
        }

        /******************************* 添加 ****************************/

        if (!empty($params['merchantId'])) {
            $data['business_num'] = (string)$params['merchantId'];     // 商户id
        }

        if (!empty($params['md5PrivateKey'])) {
            $data['md5_private_key'] = (string)$params['md5PrivateKey'];     // 秘钥
        }

        if (!empty($params['rsaPrivateKey'])) {
            $data['private_key'] = (string)$params['rsaPrivateKey'];     // 私钥
        }

        if (!empty($params['publicKey'])) {
            $data['public_key'] = (string)$params['publicKey'];      // 公钥
        }

        if (!empty($params['notifyUrl'])) {
            $data['callback_url'] = (string)$params['notifyUrl'];      // 返回地址
        }

        if (!empty($params['levelId'])) {
            $data['level'] = (string)json_encode(explode(',', $params['levelId']));       //层级
        }

        $data['is_status'] = isset($params['payStatus']) && !empty($params['payStatus']) ? (int)$params['payStatus'] : 1;                             //状态默认为开启
        // 为空添加
        /******************************* 添加end ****************************/
        $merchant = OutMerchant::updateOrCreate($where, $data);
        if ($merchant) {
            $data = array();
            $key = OutMerchant::$MerchantRedisKey . $params['clientId'] . '_' . $params['agentLine'] . '_' . $params['subAgentLine'];
            RedisConPool::del($key);
            if (empty($params['merId'])) {
                $merchant = self::addDefaultParameter($merchant);
            }
            $data['agentLine']     = (string)$merchant->agent_id;       // 代理线
            $data['subAgentLine']  = (string)$merchant->agent_num;      // 子代理线
            $data['merId']         = (int)$merchant->merchant_id;       // id
            $data['merchantId']    = (string)$merchant->business_num;   // 商户号
            $data['payId']         = (int)$merchant->pay_id;            // 三方配置ID
            $data['rsaPrivateKey'] = (string)$merchant->private_key;// 私钥
            $data['md5PrivateKey'] = (string)$merchant->md5_private_key;// md5私钥
            $data['publicKey']     = (string)$merchant->public_key;     // 公钥
            $data['notifyUrl']     = (string)$merchant->callback_url;   // 回调地址
            $data['payStatus']     = (int)$merchant->is_status;         // 三方开关状态
            return $this->replaceArray([
                'status' => true,
                'code'   => '200',
                'data'   => $data,
            ]);
        } else {
            return $this->replaceArray(['code' => '500']);
        }
    }

    /** 添加商户部分返回数据设置默认数据
     *
     * @param $merchant
     * @return mixed
     */
    public function addDefaultParameter($merchant)
    {
        $merchantKey = ['agent_id', 'agent_num', 'merchant_id', 'business_num', 'pay_id', 'md5_private_key','private_key', 'public_key',
                        'callback_url', 'status', 'mer_url', 'message1', 'message1', 'message2', 'message3',
                        'level'];
        foreach ($merchantKey as $key) {
            if (!isset($merchant->$key)) {
                $merchant->$key = null;
            }
        }
        return $merchant;
    }

}
