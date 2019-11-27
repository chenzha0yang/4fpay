<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Xinmapay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /*  回调处理  */
    public static $reqData = [];
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

        $data = array();
        if($payConf['pay_way'] == '1'){
            $data['messageid'] = '200002';
            $payType = '30';
            $data['bank_flag'] = '0';
            $data['bank_code'] = $bank;
            $data['front_notify_url'] = $returnUrl;
        } else {
            $data['messageid'] = '200001';
            $payType = $bank;
        }
        if(isset($reqData['isApp']) && $reqData['isApp'] == 1){
            $data['front_notify_url'] = $returnUrl;
            $data['messageid'] = '200004';
            self::$isAPP = true;
        }
        $data['back_notify_url']  = $ServerUrl;
        $data['branch_id']        = $payConf['business_num'];
        $data['nonce_str']        = time();
        $data['out_trade_no']     = $order;
        $data['prod_desc']        = 'prod_desc';
        $data['prod_name']        = 'prod_name';
        $data['pay_type']         = $payType;
        $data['total_fee']        = $amount*100;
        $signStr = self::getSignStr($data, true, true);
        $str = urldecode($signStr);
        $data['sign'] = strtoupper(self::getMd5Sign("{$str}&key=", $payConf['md5_private_key']));
        if ($payConf['pay_way'] != '1') {
            foreach($data as $k => $v) {
                $array[$k] = urlencode($v);
            }
            $dataString =  urldecode(json_encode($array));
            $data['httpHeaders'] = array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($dataString)
	                                );
            $data['data']        = $dataString;//提交数据
            self::$payWay  = $payConf['pay_way'];
            self::$reqType = "curl";
            self::$unit    = 2;
            self::$method  = 'header';
        }
        unset($reqData);
        return $data;
	}

    /**
     * @param $request
     * @param $mod
     * @return mixed
     */
	public static function getVerifyResult($request,$mod)
    {
        $backField = trans("backField.{$mod}");
        $result = json_decode($request->all(), true);
        self::$reqData = $result;
        $res['order']  = $result[$backField['order']];
        $res['amount'] = $result[$backField['amount']]/100;
        return $res;
    }

    /**
     * @param $type    string 模型名
     * @param $data    array  回调数据
     * @param $payConf array  商户信息
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $backField = trans("backField.{$type}");
        $data = self::$reqData;
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr = self::getSignStr($data, true, true);
        $str = urldecode($signStr);
        $signValue = strtoupper(self::getMd5Sign("{$str}&key=", $payConf['md5_private_key']));
        if ( strtoupper($signValue) == strtoupper($sign) ) {
            if (isset($data[$backField['orderStatus'][0]])) {
                if ( (string)$backField['orderStatus'][1] === (string) $data[$backField['orderStatus'][0]]) {
                    return true;
                }
            }
        }
        return false;
    }
}