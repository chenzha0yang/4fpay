<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Models\CallbackMsg;
use App\Http\Models\PayOrder;

class Jiutong extends ApiModel
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
     * @param array $reqData 接口传递的参数
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

        $data['orderNum']        = $order;//订单号
        $data['version']         = 'V3.1.0.0';//版本号
        $data['charset']         = 'UTF-8';//编码
        $data['random']          = (string)rand(100000, 999900);//随机数
        $data['merNo']           = $payConf['business_num'];//商户号
        $data['netway']          = $bank; //支付方式
        $data['amount']          = $amount * 100;//订单金额 单位 分
        $data['goodsName']       = 'goodsName';//商品名称
        $data['callBackUrl']     = $ServerUrl;//通知地址
        $data['callBackViewUrl'] = $returnUrl;//回显地址
        ksort($data);
        $sign = strtoupper(md5(json_encode($data, 320) . $payConf['md5_private_key']));
        //生成签名
        $data['sign'] = $sign;
        //生成 json字符串
        $json = json_encode($data, 320);
        //加密
        $dataStr = self::getRsaPublicSign($json, $payConf['public_key']);
        //请求字符串
        $param['data']    = urlencode($dataStr);
        $param['merchNo'] = $data['merNo'];
        $param['version'] = $data['version'];
        // 提交失败 展示信息
        $param['orderNum'] = $data['orderNum'];
        $param['amount']   = $data['amount'];

        self::$reqType  = 'curl';
        self::$unit     = 2;
        self::$isAPP    = true;
        self::$postType = true;
        self::$payWay   = $payConf['pay_way'];
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
        return 'data=' . $data['data'] . '&merchNo=' . $data['merchNo'] . '&version=' . $data['version'];
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
        $order     = $request['orderNum'];
        $bankOrder = PayOrder::getOrderData($order);//根据订单号 获取入款注单数据
        $payConf   = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息

        $decode     = $request['data'];
        $cryPto     = self::getPubData($decode, $payConf['rsa_private_key']);
        $array      = json_decode($cryPto, true);
        $signString = strtoupper($array['sign']);
        unset($array['sign']);
        ksort($array);
        $amount = $array['amount'] / 100;
        $md5    = strtoupper(md5(json_encode($array, JSON_UNESCAPED_SLASHES) . $payConf['md5_private_key']));
        // 数据直接处理完成验签
        if ($md5 == $signString && $array['payResult'] = '00') {
            self::$signTrue   = true;
            $data['orderNum'] = $order;
            $data['amount']   = $amount;
            return $data;
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