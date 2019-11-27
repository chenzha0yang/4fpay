<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tongsaopay extends ApiModel
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

        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        self::$resType = 'other';
        self::$reqType = 'curl';
        $data = [];
		$data['merchno'] = $payConf['business_num'];    //商户号
		$data['amount'] = $amount;                      //交易金额
		$data['traceno'] = $order;                      //商户流水号
		$data['payType'] = $bank;                       //支付方式
		$data['notifyUrl'] = $ServerUrl;
		$signArr = array();
		foreach ($data as $key => $value) {
			$signArr[] = $key;
		}
		sort($signArr);
        $signStr = '';
        foreach ($signArr as $i => $q) {
            if ( $i!=0 ) {
                $signStr  = $signStr.'&'.$q.'='.$data[$q];
            } else {
                $signStr = $q.'='.$data[$q];
            }
        }
        $data['signature'] = strtoupper(self::getMd5Sign("{$signStr}&", $payConf['md5_private_key']));
        unset($reqData);
        return $data;
    }

    /**
     * @param $respoon
     * @return mixed
     */
    public static function getQrCode($respoon)
    {
        $jsonStr = iconv('gbk', 'utf-8', $respoon);
        $result = json_decode($jsonStr, true);
        if( $result['respCode'] == '00' ) {
            $result['barCode'] = $result["barCode"];
        }
        return $result;
    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     *
     */
    public static function SignOther($type, $data, $payConf)
    {
        ksort($data);
        $signStr = '';
        foreach ($data as $key => $value) {
            if ($key !== 'signature' && $value != '') {
                $signStr .= "{$key}={$value}&";
            }
        }
        $sign = strtoupper(md5($signStr . $payConf['md5_private_key'])); #生成签名
        if ($sign == $data['signature'] && $data['status'] == "1") {
            return true;
        } else {
            return false;
        }
    }
}