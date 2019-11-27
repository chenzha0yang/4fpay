<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Duodpay extends ApiModel
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
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something

        $data = array();
        $data['mchid'] = $payConf['business_num'];      //  商户号
        $data['mchno'] = $order;                        //  商户网站唯一订单号
        $data['pattern'] = 'general';   
        $data['tradetype'] = $bank;                        
        $data['totalfee'] = $amount;                       
        $data['descrip'] = date('YhdHis');                     
        $data['attach'] = "";                
        $data['clientip'] = "127.0.0.1";                 
        $data['notifyurl'] = $ServerUrl;                
        $data['returnurl'] = $returnUrl;                
        $signStr = self::getSignStr($data, false);
        $data['sign'] = strtolower(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['SignInfo'];
        unset($data['SignInfo']);
        $signStr = self::getSignStr($data, false,true);
        $SignLocal = self::getMd5Sign(md5($signStr), $payConf['private_key']);
        if($SignLocal == $sign){
            if($data['ResultCode'] == 'success002'){
                return true;
            }
        }
        return false;

    }
}
