<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Huihepay extends ApiModel
{   
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str 

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

        $type          = $bank;
        if($payConf['pay_way'] == '1'){
            $type          = '1';
            self::$method  = 'get';
		} else {
            self::$reqType = 'curl';
            self::$payWay  = $payConf['pay_way'];
            self::$resType = 'json'; 
        }
        
        $data = array();
		$data['AppId']       = $payConf['business_num']; //系统分配给开发者的应用ID（等同于商户号）
		$data['Method']      = 'trade.page.pay'; //接口名称
		$data['Format']      = 'JSON'; //仅支持JSON
		$data['Charset']     = 'UTF-8'; //请求使用的编码格式，仅支持UTF-8
		$data['Version']     = '1.0'; //调用的接口版本，固定为：1.0
		$data['Timestamp']   = date('Y-m-d H:i:s'); //发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"
		$data['PayType']     = $type; //支付类型
		$data['BankCode']    = $bank; //银行代码，当PayType=1时，此值必填
		$data['OutTradeNo']  = $order; //商户订单号，64个字符以内、可包含字母、数字、下划线；需保证在商户端不重复
		$data['TotalAmount'] = $amount; //订单总金额，单位为元，精确到小数点后两位，取值范围[0.01,100000000]
		$data['Subject']     = 'Subject'; //订单标题
		$data['Body']        = 'Body'; //订单描述
		$data['NotifyUrl']   = $ServerUrl; //服务器主动通知商户服务器里指定的页面，http/https路径。
		$signStr             = self::getSignStr($data, true, true);//true1 为空不参与签名，true2排序
		$data['sign']        = self::getMd5Sign($signStr, $payConf['md5_private_key']);
        $data['SignType']    = 'MD5';
        unset($reqData);
		return $data;
    }
}




