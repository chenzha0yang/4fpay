<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Liebaopay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

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



        //TODO: do something

        $data['version'] = 'V6.79';
        $data['merchantid'] = $payConf['business_num'];
        $data['merordernum'] = $order;
        $data['orderamt'] = sprintf('%.2f',$amount);
        $data['bankcode'] = $bank;
        $data['paytime'] = date('Y-m-d H:i:s');
        $data['notifyurl'] = $ServerUrl;
        $data['returnurl'] = $returnUrl;

        $signStr      = self::getSignStr($data, true,true);

        $data['hmac'] = strtoupper(bin2hex(hash('sha256', $signStr . "&key=" . $payConf['md5_private_key'], true)));  //将组装好的参数以SHA256的方式进行加密，并且转换为大写。
        unset($reqData);
        return $data;

    }



    //回调处理
    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['hmac'];
        unset($data['hmac']);
        $signStr      = self::getSignStr($data, true,true);
        $isSign = strtoupper(bin2hex(hash('sha256', $signStr . "&key=" . $payConf['md5_private_key'], true)));  //将组装好的参数以SHA256的方式进行加密，并且转换为大写。
        if (strtoupper($sign) == $isSign && $data['respcode'] == '00') {
            return true;
        }
        return false;
    }

}