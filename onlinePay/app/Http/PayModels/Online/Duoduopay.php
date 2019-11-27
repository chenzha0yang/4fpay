<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Duoduopay extends ApiModel
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
        $data['MerId'] = $payConf['business_num'];      //  商户号
        $data['OrdId'] = $order;                        //  商户网站唯一订单号
        $data['OrdAmt'] = sprintf("%1\$.2f",$amount);   //  订单金额
        $data['PayType'] = "DT";                        //  支付类型，默认DT
        $data['CurCode'] = "CNY";                       //  支付币种，默认CNY
        $data['BankCode'] = $bank;                      //  银行代码，参考附录银行代码
        $data['ProductInfo'] = "iphone";                //  物品信息，可以随机填写
        $data['Remark'] = "remark";                     //  备注信息，可以随机填写
        $data['ReturnURL'] = $returnUrl;                //  前端页面返回地址
        $data['NotifyURL'] = $ServerUrl;                //  后台异步通知
        $data['SignType'] = "MD5";                      //  签名方式，默认MD5
        $signStr = self::getSignStr($data, false);
        $data['SignInfo'] = self::getMd5Sign("{$signStr}&MerKey=", $payConf['md5_private_key']);
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
