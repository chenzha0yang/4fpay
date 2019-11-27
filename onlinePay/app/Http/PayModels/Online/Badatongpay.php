<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Badatongpay extends ApiModel
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
        //TODO: do something
        self::$reqType = 'curl';
        self::$method  = 'header';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$isAPP = true;

        $data['businessId']     = $payConf['business_num'];//商户号
        $data['payType']     = $bank;//银行编码
        $data['childOrderno']     = $order;//订单号
        $data['amount']       = sprintf('%.2f',$amount);//订单金额
        $data['time']   = date('YmdHis',time());
        $data['remark']      = 'test';
        $data['returnUrl'] = $returnUrl;
        $data['serverUrl']   = $ServerUrl;
        $signStr            = 'businessId='.$data['businessId'].'&payType='.$data['payType'].'&childOrderno='.$data['childOrderno'].'&amount='.$data['amount'].'&time='.$data['time'];
        $data['sign']            = md5($signStr . "&" . $payConf['md5_private_key']);

        $header                   = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data)),
        ];
        $postData['data']         = json_encode($data);
        $postData['httpHeaders']  = $header;
        $postData['childOrderno'] = $data['childOrderno'];
        $postData['amount']  = $data['amount'];
        unset($reqData);
        return $postData;
    }

    public static function getQrCode($response)
    {
        $data = json_decode($response, true);
        if ($data['code'] == '0') {
            $data['qrCode'] = $data['data']['payUrl'];
        }
        return $data;
    }

    
	public static function getVerifyResult($request, $mod)
	{
		$arr = $request->getContent();
		$res =  json_decode($arr,true);
		$data['childOrderno'] = $res['childOrderno'];
		$data['payAmount'] = $res['payAmount'];
		return $data;
	}
    
    

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $datas, $payConf)
    {
        $json = file_get_contents('php://input');  //获取POST流
        $data=json_decode($json,true);
        $sign     = $data['sign'];
        unset($data['sign']);
        $signStr  = 'childOrderno='.$data['childOrderno'].'&payAmount='.$data['payAmount'].'&orderStatus='.$data['orderStatus'].'&time='.$data['time'].'&'.$payConf['md5_private_key'];
        $signTrue = md5($signStr);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['orderStatus'] == '1') {
            return true;
        } else {
            return false;
        }
    }

}