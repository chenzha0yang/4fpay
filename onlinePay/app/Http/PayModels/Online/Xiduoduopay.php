<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xiduoduopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

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

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$httpBuildQuery = true;
        self::$isAPP = true;
        self::$unit = 2;

        //TODO: do something
        $data               = [];
        $data['merAccount'] = $payConf['public_key'];
        $data['customerNo'] = $payConf['business_num'];
        $data['payType']    = $bank;
        if ($payConf['pay_way'] == 2) {
            $data['payTypeCode'] = 'SCANPAY_WEIXIN';
            if ($payConf['is_app'] ==1) {
                $data['payTypeCode'] = 'H5_WEIXIN';
            }
        } elseif ($payConf['pay_way'] == 3) {
            $data['payTypeCode'] = 'SCANPAY_ALIPAY';
            if ($payConf['is_app'] ==1) {
                $data['payTypeCode'] = 'ALIPAY_H5';
            }
        } elseif ($payConf['pay_way'] == 4) {
            $data['payTypeCode'] = 'SCANPAY_QQ';
            if ($payConf['is_app'] == 1) {
                $data['payTypeCode'] = 'H5_QQ';
            }
        } elseif ($payConf['pay_way'] == 6) {
            $data['payTypeCode'] = 'SCANPAY_UNIONPAY';
        }
        $data['orderNo']     = $order;
        $data['time']        = time();
        $data['payAmount']   = $amount * 100;
        $data['productCode'] = '01';
        $data['productName'] = 'xdd';
        $data['productDesc'] = 'xdd';
        $data['userType']    = '0';
        $data['payIp']       = self::getIp();
        $data['returnUrl']   = $returnUrl;
        $data['notifyUrl']   = $ServerUrl;
        // 排序
        ksort($data);
        // 拼接排序后的value
        $str = "";
        foreach ($data as $key => $value) {
            $str = $str . $value;
        }
        $sign         = sha1($str . $payConf['md5_private_key']);
        $data['sign'] = $sign; //签名
        $post_data    = array(
            'merAccount' => $data['merAccount'], //商户标识
            'data'       => json_encode($data),
            'orderNo'    => $data['orderNo'],
            'payAmount'  => $data['payAmount'],
        );
        unset($reqData);
        return $post_data;
    }

    /***
     * 展示二维码
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response) {
        $results = json_decode($response,true);
        if ($results['code'] == '000000') {
            $results['payUrl'] = $results['data']['payUrl'];
        }
        return $results;
    }

    //金额特殊处理
    public static function getVerifyResult($request, $mod){
        $jsonStr = json_decode(stripslashes($request['data']), true);
        $result['orderNo'] = $jsonStr['orderNo'];
        $result['amount'] = $jsonStr['amount'];
        return $result;
    }

    //回调特殊处理
    public static function SignOther($model, $resp, $payConf) {
        $jsonStr = json_decode(stripslashes($resp['data']), true);
        ksort($jsonStr);
        $str = "";
        foreach ($jsonStr as $key => $value) {
            if ($key != "sign") {
                $str = $str . $value;
            }
        }
        $sign = sha1($str . $payConf['md5_private_key']);
        if ($sign == $jsonStr['sign']) {
            return true;
        } else {
            return false;
        }
    }
}
