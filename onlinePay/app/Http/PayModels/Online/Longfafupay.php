<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayOrder;
use App\Http\Models\CallbackMsg;

class Longfafupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $signTrue = false; // 验证签名

    /**
     * @param array       $reqData 接口传递的参数
     * @param array $payConf
     * @return array
     */
    public static function getAllInfo($reqData, $payConf)
    {
        /**
         * 参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

		//判断是否需要跳转链接 is_app=1开启 2-关闭
	    if ($payConf['is_app'] == 1) {
		    self::$isAPP = true;
	    }

        self::$reqType  = 'curl';
        self::$unit     = 2;
        self::$postType = true;
        self::$payWay   = $payConf['pay_way'];

        $data['merchNo']       = $payConf['business_num'];//商户号
        $data['netwayType']    = $bank; //支付方式
        $data['randomNo']      = self::randStr(8);//随机数
        $data['orderNo']       = $order;//订单号
        $data['amount']        = (string)($amount * 100);//订单金额 单位 分
        $data['goodsName']     = 'goodsName';//商品名称
        $data['notifyUrl']     = $ServerUrl;//通知地址
        $data['notifyViewUrl'] = $returnUrl;//回显地址
        ksort($data);
        $data['sign'] = strtoupper(md5(json_encode($data, 320) . $payConf['md5_private_key']));
        ksort($data);
        //生成 json字符串
        $json = json_encode($data, 320);
        //加密
        $pub_key     = openssl_pkey_get_public($payConf['public_key']);
        $dataStr = self::getRsaPublicSign($json, $pub_key);
        //请求字符串
        $param['data']    = urlencode($dataStr);
        $param['merchNo'] = $data['merchNo'];
        $param['version'] = 'V3.6.0.0';
        // 提交失败 展示信息
        $param['orderNo'] = $data['orderNo'];
        $param['amount']  = $data['amount'];


        unset($reqData);
        return $param;
    }

    /**
     * 提交数据
     *
     * @param $data
     * @return string
     */
    public static function getRequestByType($data)
    {
        $post = 'data=' . $data['data'] . '&merchNo=' . $data['merchNo'] . '&version=' . $data['version'];
        return $post;
    }

    /**
     * 回调特殊处理
     *
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request, $mod)
    {
        $data = $request->all();
        $order     = $data['orderNo'];
        $bankOrder = PayOrder::getOrderData($order);//根据订单号 获取入款注单数据
        $payConf   = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
        if (strpos($data['data'],'%')) {
            //被urlencode处理过
            $decode     = urldecode($data['data']);
        } else {
            //没被urlencode处理过
            $decode     = $data['data'];
        }
        $cryPto     = self::getPubData($decode, $payConf['rsa_private_key']);
        $array      = json_decode($cryPto, true);
        if (is_array($array)) {
            $signString = strtoupper($array['sign']);
            unset($array['sign']);
            ksort($array);
            $amount = $array['amount'] / 100;
            $md5    = strtoupper(md5(json_encode($array, 320) . $payConf['md5_private_key']));
            // 数据直接处理完成验签
            if ($md5 == $signString && $array['payStateCode'] = '00') {
                self::$signTrue   = true;
                $data['orderNo'] = $order;
                $data['amount']   = $amount;
                $data['payStateCode']   = $array['payStateCode'];
                return $data;
            } else {
                echo trans("success.{$mod}");
                CallbackMsg::AddCallbackMsg($request, $bankOrder, 1);
                exit();
            }
        } else {
            echo trans("success.{$mod}");
            CallbackMsg::AddCallbackMsg($request, $bankOrder, 1);
            exit();
        }
    }

    /**
     * 特殊处理已经验签， 直接返回true
     *
     * @return bool
     */
    public static function SignOther()
    {
        return true;
    }
}