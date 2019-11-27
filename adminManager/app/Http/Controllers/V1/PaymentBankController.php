<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\ApiController;
use App\Models\Bank\PaymentBank;
use Illuminate\Support\Facades\Redis;


class PaymentBankController extends ApiController
{

    const OPEN_THIS_API = false;
    private static $TRUE_OR_FALSE = true;

    public function runApi(&$params)
    {
        // 关闭接口对接权限
        if (self::OPEN_THIS_API) {
            return $this->replaceArray(['code' => '401']);
        }
        // 代理线验证，只有小写的1到3位的字母通过
        if (self::checkParameter($params, 'agentLine', '/^[a-z0-9]{1,10}$/')) {
            return $this->replaceArray(['code' => '1015']);
        }

        // 子代理线验证
        if (self::checkParameter($params, 'subAgentLine', '/^([a-z]){1}$/', false)) {
            return $this->replaceArray(['code' => '1015']);
        }

        //修改可以不用传类型
        if (isset($params['merId']) && !empty($params['merId'])) {
            self::$TRUE_OR_FALSE = false;
        }

        //长链接
        if (self::checkParameter($params, 'long_url', '')) {
            return $this->replaceArray(['code' => '1030']);
        }

        //状态 1 正常，2停用
        if (self::checkParameter($params, 'status', '/^([1-9]){1}$/')) {
            return $this->replaceArray(['code' => '1031']);
        }

        if (empty($params['relevance_id'])) {
            //添加参数处理
            PaymentBank::$data = PaymentBank::addData($params);
            //添加数据
            $result = PaymentBank::addToData();
            //销毁
            PaymentBank::_destroy();
            if ($result->payment_id) {
                //添加成功后返回数据伪装
                $data = PaymentBank::returnDataCamouflage($result);
                //清除Redis
                Redis::del($params['agentLine'].$params['subAgentLine'].'_PayMenBank');
                return $this->replaceArray([
                    'status' => true,
                    'code'   => '200',
                    'data'   => $data,
                ]);
            } else {
                return $this->replaceArray(['code' => '500']);
            }

        } else {
            //编辑参数处理
            $bankUrl = PaymentBank::getBankUrl($params['relevance_id'],$params['agentLine'].$params['subAgentLine']);
            if (!$bankUrl) {
                return $this->replaceArray(['code' => '1032']);
            };
            PaymentBank::$where['payment_id'] = $params['relevance_id'];
            //提交参数
            PaymentBank::$data = PaymentBank::saveData($params);
            if (PaymentBank::editToData()) {
                //销毁
                PaymentBank::_destroy();
                //清除Redis
                Redis::del($params['agentLine'].$params['subAgentLine'].'_PayMenBank');
                return $this->replaceArray([
                    'status' => true,
                    'code'   => '200',
                    'data'   => [],
                ]);
            } else {
                return $this->replaceArray(['code' => '500']);
            }
        }
    }

}
