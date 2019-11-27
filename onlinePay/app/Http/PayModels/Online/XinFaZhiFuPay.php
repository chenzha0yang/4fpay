<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Extensions\File;
use App\Http\Models\PayOrder;
use App\Http\Models\PayMerchant;
use App\Http\Models\CallbackMsg;

class XinFaZhiFuPay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $signTrue = false;  //签名判断

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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        if( $payConf['is_app'] == 1 ){
            self::$isAPP = true;
        }
        //TODO: do something
        self::$unit = 2 ;       //单位：分
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$postType = true;
        $data = array(
            'version'      =>   'V3.3.0.0',                        //版本号，固定值
            'merchNo'      =>   $payConf['business_num'],          //商户号
            'payType'      =>   $bank,                             //支付方式
            'randomNum'    =>   self::randStr(8),            //随机字符串
            'orderNo'      =>   $order,                            //商户订单号
            'amount'       =>   (string)($amount * 100),           //订单金额
            'goodsName'    =>   'dulex',                           //商品名称
            'notifyUrl'    =>   $ServerUrl,                        //异步通知地址
            'notifyViewUrl'=>   $returnUrl,                        //同步通知地址
            'charsetCode'  =>   'UTF-8',                           //客户端系统编码格式，UTF-8、GBK
        );
        //签名
        ksort($data);
        $stringA = json_encode($data,320) . $payConf['md5_private_key'];
        $data['sign'] = strtoupper(md5($stringA));
        ksort($data);
        //传输数据
        $jsonData = json_encode($data);
        //公钥非对称加密

        $DataA['data'] = urlencode(self::getRsaPublicSign($jsonData,$payConf['public_key'])) . '&merchNo=' . $payConf['business_num'];
        $DataA['orderNo'] = $data['orderNo'];
        $DataA['amount'] = $data['amount'];
        unset($reqData);
        return $DataA;
    }

    // 提交
    public static function getRequestByType($data)
    {
        $post = 'data=' . $data['data'];
        return $post;
    }


    public static function getVerifyResult($request, $mod)
    {
        $req = $request->all();
        $order = $req['orderNo'];  //订单号
        $bankOrder = PayOrder::getOrderData($order);//根据订单号 获取入款注单数据
        if (isset($bankOrder->merchant_id)) {
            $payConf   = PayMerchant::findOrFail($bankOrder->merchant_id);//根据订单中的商户表ID获取配置信息
        } else {
            echo trans("success.{$mod}");
            if(!empty($bankOrder)){
                CallbackMsg::AddCallbackMsg($request, $bankOrder, 1);
                exit();
            }
            File::logResult($request->all());
            exit();
        }
        if (strpos($req['data'],'%')) {
            //被urlencode处理过
            $decode     = urldecode($req['data']);
        } else {
            //没被urlencode处理过
            $decode     = $req['data'];
        }
        $cryPto     = self::getPubData($decode, $payConf['rsa_private_key']);
        //得到解密信息
        $array      = json_decode($cryPto, true);
        if (is_array($array)) {
            $signString = strtoupper($array['sign']);
            unset($array['sign']);
            ksort($array);
            $amount = $array['amount'] / 100;
            $md5    = strtoupper(md5(json_encode($array, 320) . $payConf['md5_private_key']));
            // 数据直接处理完成验签
            if ($md5 == $signString && $array['payResult'] = '00') {
                self::$signTrue   = true;
                $data['orderNum'] = $order;
                $data['amount']   = $amount;
                $data['payStateCode'] = $array['payStateCode'];
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
