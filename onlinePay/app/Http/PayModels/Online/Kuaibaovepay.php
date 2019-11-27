<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Kuaibaovepay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $UserName = '';

    private static $reqData = [];
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
        self::$UserName = isset($reqData['username']) ? $reqData['username'] : 'chongzhi';
        //TODO: do something
        $data['goodsname'] = 'kuaibao';
        $data['istype']     = (int)$bank;//银行编码
        $data['notify_url'] = $ServerUrl;
        $data['orderid']     = $order;//订单号
        $data['orderuid'] = self::$UserName;
        $data['price']       = sprintf('%.2f',$amount);//订单金额
        $data['return_url'] = $returnUrl;
        $data['token'] = $payConf['md5_private_key'];
        $data['uid']     = $payConf['business_num'];//商户号
        $signStr = '';
        foreach ($data as $key => $value) {
            $signStr .= $value;
        }
        $data['key']            = md5($signStr);
        unset($data['token']);
        unset($reqData);
        return $data;
    }
	
	
	//回调金额化分为元
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->getContent();
		$arr = json_decode($arr, true);
		self::$reqData = $arr;
		$data['price'] = $arr['price'];
		$data['orderid'] = $arr['orderid'];
		return $data;
	}
    
    
    public static function SignOther($type, $datas, $payConf)
    {
        $data = self::$reqData;
        $sign = $data['key'];
        $signStr  = $data['orderid'].$data['orderuid'].$data['yftNo'].$data['price'].$data['istype'].$data['realprice'].$data['createAt'].$data['uid'].$payConf['md5_private_key'].$data['notifyurl'];
        $signTrue = md5($signStr);
        if (strtoupper($sign) == strtoupper($signTrue)) {
            return true;
        }
        return false;
    }


}