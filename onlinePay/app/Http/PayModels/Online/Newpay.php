<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Newpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    private static $UserName = '';
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
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        self::$isAPP = true;
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        self::$resType = 'other';
        self::$method = 'header';

        if(!openssl_pkey_get_public($payConf['public_key'])){
            echo '公钥格式错误!请检查!';exit();
        }

        $data['amount'] = $amount;//订单金额
        $data['notify_url'] = $ServerUrl;
        $data['out_trade_no'] = (string)$order;//订单号
        $data['partner_id'] = $payConf['business_num'];//商户号
        $data['payType'] = $bank;//银行编码
        $data['sendTime'] = date('Y-m-d H:i:s',time());
        //$signStr =  $this->getSignStr($data);
        $jsonData = json_encode($data,JSON_UNESCAPED_SLASHES);
        $md5msg = md5( $jsonData . $payConf['md5_private_key']);
        $crypto = '';
        foreach (str_split($jsonData, 117) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $payConf['public_key'],OPENSSL_PKCS1_PADDING);
            $crypto .= $encryptData;
        }

        $rsamsg =  base64_encode($crypto);
        $reData = [
            'partner_id' => $data['partner_id'],
            'rsamsg'     => urlencode($rsamsg),
            'md5msg'     => $md5msg,
            'username'   => self::$UserName,
            'ip'         => self::getIp(),
        ];

        $result['data'] = json_encode($reData);
        $result['httpHeaders'] = array(
                 'Content-Type: application/json; charset=utf-8',
             );
        $result['amount'] = $data['amount'];
        $result['out_trade_no'] = $data['out_trade_no'];

        unset($reqData);
        return $result;
    }

    /**
     * 二维码及语言包处理
     * @param $res
     * @return mixed
     */
    public static function getQrCode($res)
    {
        $responseData = json_decode($res,true);
        if ($responseData['status'] == '0'){
            echo urldecode($responseData['payUrl']);die;
        }else{
            $responseData['status'] = $responseData['status'];
            $responseData['msg'] = $responseData['msg'];
        }
        return $responseData;
    }

    public static function getVerifyResult(Request $request, $mod)
    {
        $res                = $request->getContent();
        $data = json_decode($res, true);
        return $data;
    }

    public static function SignOther($type, $datas, $payConf)
    {
        $post    = file_get_contents("php://input");
        $data = json_decode($post,true);
        $sign = $data['sign'];
        unset($data['sign']);
        $signStr =  $data['amount'].'&'.$data['partner_id'].'&'.$data['out_trade_no'].'&'.$data['ProfitOrderNo'].'&'.$data['payType'];
        $signTrue = md5($signStr . "&" . $payConf['md5_private_key']);
        if (strtoupper($signTrue) == strtoupper($sign) && $data['status'] == '0') {
            return true;
        } else {
            return false;
        }
    }

    public static function formatKey($key, $type = 'public'){
        $key = str_replace("-----BEGIN PRIVATE KEY-----", "", $key);
        $key = str_replace("-----END PRIVATE KEY-----", "", $key);
        $key = str_replace("-----BEGIN PUBLIC KEY-----", "", $key);
        $key = str_replace("-----END PUBLIC KEY-----", "", $key);
        $key = self::_trimAll($key);

        if ($type == 'public') {
            $begin = "-----BEGIN PUBLIC KEY-----\n";
            $end = "-----END PUBLIC KEY-----";
        } else {
            $begin = "-----BEGIN PRIVATE KEY-----\n";
            $end = "-----END PRIVATE KEY-----";
        }

        $key = chunk_split($key, 64, "\n");

        return $begin . $key . $end;
    }

    public  static function _trimAll($str)
    {
        $qian = array(" ", " ", "\t", "\n", "\r");
        $hou = array("", "", "", "", "");
        return str_replace($qian, $hou, $str);
    }

}