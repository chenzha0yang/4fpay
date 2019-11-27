<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Suixinbaopay extends ApiModel
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
         *   参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];
        //TODO: do something

        static::$method = 'get';
        $data = [];
        $data['partner'] = $payConf['business_num'];                        //商户 ID
        $data['banktype'] = $bank;                                          //银行类型
        $data['paymoney'] = $amount;                                        //金额
        $data['ordernumber'] = $order;                                      //商户订单号
        $data['callbackurl'] = $ServerUrl;                                  //下行异步通知地址
        $signSource = self::getSignStr($data);
        $sign = self::getMd5Sign("{$signSource}", $payConf['md5_private_key']);
        $data['hrefbackurl'] = $returnUrl;                                  //下行同步通知地址
        $data['attach'] = "chongzhi";                                       //备注信息
        $data['isshow'] = "";                                               //是否显示收银台
        $data['sign'] = $sign;                                              //MD5 签名
        unset($reqData);
        return $data;
    }

	public static function signOther($mod, $data,$payConf)
	{
		$sign = $data['sign'];
		unset($data['sign']);
		unset($data['attach']);
		unset($data['sysnumber']);
		$signStr      = self::getSignStr($data);
		$mySign = self::getMd5Sign("{$signStr}", $payConf['md5_private_key']);

		if (strtolower($mySign) == strtolower($sign) && $data['orderstatus'] == '1') {
			return true;
		} else {
			return false;
		}
	}
}