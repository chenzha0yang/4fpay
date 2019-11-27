<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xinefpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址

        //TODO: do something
        self::$resType = 'other';
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$unit = '2';
        $motdata = [
            'attach' => 'tttt',
            'curType' => 'CNY',                     //人民币
            'merchId' => $payConf['business_num'],  //商户ID
            'notifyUrl' => $ServerUrl,              //需要使用服务器能访问的地址
            'orderId' => $order,                    //订单号
            'payWay' => $bank,                      //支付方式
            'title' => 'test',                      //标题
            'totalAmt' => $amount * 100,            //金额 按分为单位
            'tranTime' => time().rand(100, 200),    //自己订单号
        ];
        $mopost = json_encode($motdata);            //数组转换成JSON
        $md5data = self::getMd5Sign($mopost, $payConf['md5_private_key']);
        $data['partner'] = $motdata['merchId'];
        $data['encryptType'] = 'md5';
        $data['msgData'] = base64_encode($mopost);
        $data['signData'] = $md5data;
        $data['totalAmt'] = $motdata['totalAmt'];
        $data['orderId'] = $motdata['orderId'];
        unset($reqData);
        return $data;
    }

    //提交处理
    public static function getQrCode($response)
    {
        $result = json_decode($response, true);
        $datas = base64_decode($result['msgData'], true); //解码
        $Rarray = json_decode($datas, true);
        if ( $Rarray['respCode'] == '0000' ) {
            $Rarray['qrCode'] = $Rarray['qrCode'];
        }
        return $Rarray;
    }

    //回调处理
    public static function SignOther($mod, $data, $payConf)
    {
        $msgdata = $data["msgData"]; //接收字符串
        $BaseData = base64_decode($msgdata, true);
        $result = json_decode($BaseData, true);
        $md5data = self::getMd5Sign($msgdata, $payConf['md5_private_key']);
        if ( $md5data == $data['signData'] && $result['respCode'] == "0000" ) {
            return true;
        }
        return false;
    }
}