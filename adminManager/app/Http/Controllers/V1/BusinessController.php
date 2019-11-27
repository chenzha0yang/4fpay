<?php

namespace App\Http\Controllers\V1;

use App\Models\Merchant\InMerchant;
use App\Models\Config\PayConfig;
use App\Models\Config\PayType;
use App\Http\Controllers\ApiController;
use App\Extensions\RedisConPool;

class BusinessController extends ApiController
{
    const AGENT_PREG  = '/^[a-z0-9]{1,10}$/';
    const PAY_PREG    = '/^[0-9]{1,4}$/';
    const MER_ID_PREG = '/^[0-9]+$/';
    const URL_PREG    = '/^((ht|f)tps?):\/\/[\w\-]+(\.[\w\-]+)+([\w\-\.,@?^=%&:\/~\+#]*[\w\-\@?^=%&\/~\+#])?$/';
    private static $TRUE_OR_FALSE = true;

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

        //修改可以不用传类型
        if (isset($params['merId']) && !empty($params['merId'])) {
            self::$TRUE_OR_FALSE = false ;
        }
        // 支付类型
        if (self::checkParameter($params, 'payType', '/^[0-9]{1,2}$/',self::$TRUE_OR_FALSE)) {
            return $this->replaceArray(['code' => '1027']);
        }

        //三方支付ID验证
        if (self::checkParameter($params, 'merId', self::MER_ID_PREG, false)) {
            return $this->replaceArray(['code' => '1016']);
        }

        // 商户号
        if (self::checkParameter($params, 'merchantId', '',true)) {
            return $this->replaceArray(['code' => '1026']);
        }

        // 添加时必传参数
        if (empty($params['merId'])) {

            if (self::checkParameter($params, 'notifyUrl', self::URL_PREG)) {
                return $this->replaceArray(['code' => '1021']);
            }

            if (self::checkParameter($params, 'payId', self::PAY_PREG)) {
                return $this->replaceArray(['code' => '1022']);
            }

        } else {

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

            $payId = PayConfig::getPayType($params['payId']);
            if ($payId) {
                return $this->replaceArray(['code' => '1024']);
            };
            $data['pay_id'] = (int)$params['payId'];     //三方类型id
        }

        //三方 支付类型验证
        if (!isset($params['merId']) || empty($params['merId'])) {
            //添加
            $payType = PayType::getWayPay($params['payType']);
            if (empty($payType)) {
                return $this->replaceArray(['code' => '1025']);
            };
            $data['pay_way'] = (int)$params['payType'];     //支付方式
        }
        if (!empty($params['merId']) && isset($params['payType'])) {
            $data['pay_way'] = $params['payType'];
        }
        /******************************* 修改end ****************************/
        $where['agent_id']     = (string)$params['agentLine'];    // 代理线
        $where['agent_num']    = (string)$params['subAgentLine']; // 子代理线
        $where['client_id']    = (int)$params['clientId'];      // 支付类型
        // merId 为空表示添加商户信息
        if (empty($params['merId'])) {
            // 可以重复添加
            $where['merchant_id']     = 0;    //
            $data['client_id'] = $params['clientId'];
        } else {

            //不是批量修改状态
            if (!is_array($params['merId'])) {
                $where['merchant_id'] = (int)$params['merId'];            // id
                $result                = InMerchant::where($where)->first();
                if (!$result) {
                    return $this->replaceArray(['code' => '1017']);
                }
            }
            if (array_key_exists('code',$params)) {
                if (empty($params['code'])) {
                    $data['pay_code'] = '';
                } else {
                    $data['pay_code'] = $params['code'];
                }

            }
        }

        /******************************* 添加 ****************************/

        if (!empty($params['merchantId'])) {
            $data['business_num'] = (string)$params['merchantId'];     // 商户id
        }

        if (!empty($params['md5PrivateKey'])) {
            $data['md5_private_key'] = (string)$params['md5PrivateKey'];     // 私钥
        }

        if (!empty($params['rsaPrivateKey'])) {
            $data['rsa_private_key'] = (string)$params['rsaPrivateKey'];     // 私钥
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

        if (!empty($params['code'])) {
            $data['pay_code'] = (string)$params['code'];    //支付编码
        } else {
            $data['pay_code'] = '';
        }

        if (!empty($params['remark1'])) {
            $data['message1'] = (string)$params['remark1'];   //预留字段1
        }

        if (!empty($params['remark2'])) {
            $data['message2'] = (string)$params['remark2'];   //预留字段2
        }

        if (!empty($params['remark3'])) {
            $data['message3'] = (string)$params['remark3'];   //预留字段3
        }

        if (!empty($params['isApp'])) {
            $data['is_app'] = (int)$params['isApp'];     //是否跳转App／H5
        }

        if (!empty($params['merUrl'])) {
            $data['mer_url'] = (string)$params['merUrl'];     //自填写支付网关
        }

        $data['is_status'] = isset($params['payStatus']) && !empty($params['payStatus']) ? $params['payStatus'] : 1;//状态默认为开启
        // 为空添加
        /******************************* 添加end ****************************/
        $merchant = InMerchant::updateOrCreate($where, $data);
        if ($merchant) {
            $data = array();
            RedisConPool::del(InMerchant::$MerchantRedisKey . $params['clientId'] . $params['agentLine'] . $params['subAgentLine']);
            if (empty($params['merId'])) {
                $merchant = self::addDefaultParameter($merchant);
                //对应三方当前使用数量
                $count = (int)PayConfig::getOne($merchant->pay_id)->count_use;
                //更新三方使用数量
                PayConfig::$data            = ['count_use' => $count + 1];
                PayConfig::$where['pay_id'] = $merchant->pay_id;
                PayConfig::editToData();
                PayConfig::_destroy();
            }
            $data['agentLine']     = (string)$merchant->agent_id;       // 代理线
            $data['subAgentLine']  = (string)$merchant->agent_num;      // 子代理线
            $data['merId']         = (int)$merchant->merchant_id;       // id
            $data['merchantId']    = (string)$merchant->business_num;   // 商户号
            $data['payId']         = (int)$merchant->pay_id;            // 三方配置ID
            $data['payType']       = (int)$merchant->pay_way;           // 支付类型
            $data['rsaPrivateKey'] = (string)$merchant->rsa_private_key;// 私钥
            $data['md5PrivateKey'] = (string)$merchant->md5_private_key;// md5私钥
            $data['publicKey']     = (string)$merchant->public_key;     // 公钥
            $data['notifyUrl']     = (string)$merchant->callback_url;   // 回调地址
            $data['payStatus']     = (int)$merchant->is_status;         // 三方开关状态
            $data['payCode']       = (string)$merchant->pay_code;       // 编码自填
            $data['merUrl']        = (string)$merchant->mer_url;        // 支付网关自填
            $data['isApp']         = (int)$merchant->is_app;            // 跳转app/H5参数
            $data['remark1']       = (string)$merchant->message1;       // 预留字段信息1
            $data['remark2']       = (string)$merchant->message2;       // 预留字段信息2
            $data['remark3']       = (string)$merchant->message3;       // 预留字段信息3
            $data['levelId']       = (string)$merchant->level;          // 层级

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
        $merchantKey = ['agent_id', 'agent_num', 'merchant_id', 'business_num', 'pay_id', 'pay_way', 'md5_private_key','rsa_private_key', 'public_key',
                        'callback_url', 'status', 'pay_code', 'mer_url', 'is_app', 'message1', 'message1', 'message2', 'message3',
                        'level'];
        foreach ($merchantKey as $key) {
            if (!isset($merchant->$key)) {
                $merchant->$key = null;
            }
        }
        return $merchant;
    }

}
