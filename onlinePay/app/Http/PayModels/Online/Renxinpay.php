<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Renxinpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something

        self::$method = 'get';
        $data=array();
        $data['version'] = '3.0';/*接口版本号,目前固定值为3.0*/
        $data['method'] = "Rx.online.pay";/*接口名称: Rx.online.pay*/
        $data['partner'] = $payConf['business_num'];//商户id,由API分配
        $data['banktype'] = $bank;//银行类型 default为跳转到接口进行选择支付
        $data['paymoney'] = $amount;//单位元（人民币）,两位小数点
        $data['ordernumber'] = $order;//商户系统订单号，该订单号将作为接口的返回数据。该值需在商户系统内唯一
        $data['callbackurl'] = $ServerUrl;//下行异步通知的地址，需要以http://开头且没有任何参数
        $signStr = self::getSignStr($data, false);
        $sign = self::getMd5Sign($signStr, $payConf['md5_private_key']);

        $data['hrefbackurl'] = $returnUrl;//下行同步通知过程的返回地址
        $data['goodsname'] = 'chongzhi';//商品名称。若该值包含中文，请注意编码
        $data['attach'] = 'remark';//备注信息
        $data['isshow'] = '1';//该参数为支付宝扫码、微信、QQ钱包专用，默认为1，跳转到网关页面进行扫码，如设为0，则网关只返回二维码图片地址供用户自行调用
        $data['sign'] = $sign;
        unset($reqData);
        return $data;
    }
}