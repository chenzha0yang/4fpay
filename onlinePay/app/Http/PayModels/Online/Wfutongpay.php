<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Wfutongpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $callbackData = '';


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
		$data['service']      = $bank;//
		$data['version']      = "1.0"; //
		$data['charset']      = "UTF-8";
		$data['sign_type']    = "MD5";
		$data['mch_id']       = $payConf['business_num']; //商户号
		$data['out_trade_no'] = $order;
		$data['device_info']  = "";
		$data['body']         = "ces";
		$data['sub_openid']   = "";
		$data['attach']       = "";
		$data['total_fee']    = $amount * 100; //金额   以分为单位
		$data['notify_url']   = $ServerUrl;
		$data['callback_url'] = $returnUrl;
		$data['time_start']   = "";
		$data['time_expire']  = "";
		$data['goods_tag']    = "";
		$data['mch_create_ip']= "127.0.0.1";
		$data['auth_code']    = "";
		$data['nonce_str']    = time();

		$signStr = self::getSignStr($data, true, true);//true1 为空不参与签名，true2排序
        $data['sign']  = strtoupper(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));

    	$src = "<xml>";
    	ksort($data);
		foreach ($data as $key => $value){
			$key   = iconv("GBK","UTF-8", $key);
			$value = iconv("GBK","UTF-8", $value);
			$src .= "<{$key}>{$value}</{$key}>";
		}
		$src .= "</xml>";
		$httpHeaders = array();
        array_push($httpHeaders, "Content-Type:application/xml;charset=utf-8");
		$postData['data'] = $src;
        $postData['out_trade_no'] = $order;
        $postData['total_fee']    = $amount;
        $postData['httpHeaders']  = $httpHeaders;
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = "other";
        self::$method  = "header";
        unset($reqData);
        return $postData;
    }

    /**
     * @param $response
     * @return array
     */
    public static function getQrCode($response)
    {
    	$encode = '';
    	$result = [];
    	$res=simplexml_load_string($response);
		if($res && $res->children()) {
			foreach ($res->children() as $node){
				//有子节点
				if($node->children()) {
					$k = $node->getName();
					$nodeXml = $node->asXML();
					$v = substr($nodeXml, strlen($k)+2, strlen($nodeXml)-2*strlen($k)-5);

				} else {
					$k = $node->getName();
					$v = (string)$node;
				}

				if($encode!="" && $encode != "UTF-8") {
					$k = iconv("UTF-8", $encode, $k);
					$v = iconv("UTF-8", $encode, $v);
				}

				$result[$k] =  $v;
			}
		}
        if(isset($result['status']) && $result['status'] != "0"){
            $result['err_msg']  = $result['message'];
            $result['err_code'] = $result['status'];
        }
		return $result;

    }

    /**
     * @param $type
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign = self::$callbackData['sign'];
        unset(self::$callbackData['sign']);
        $signStr = self::getSignStr(self::$callbackData, true, true); //true1 为空不参与签名，true2排序
        $signTrue = self::getMd5Sign($signStr . "key=", $payConf['md5_private_key']);
        if (strtoupper($signTrue) == strtoupper($sign)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $request
     * @param $mod
     * @return mixed
     */
    public static function getVerifyResult($request, $mod) {
    	libxml_disable_entity_loader(true);
		$xmlString = simplexml_load_string($request->all(), 'SimpleXMLElement', LIBXML_NOCDATA);
		self::$callbackData = json_decode(json_encode($xmlString),true);
        $res['amount'] = self::$callbackData['total_fee'];  //金额
        $res['order']  = self::$callbackData['out_trade_no']; //订单号
        return $res;
    }

}