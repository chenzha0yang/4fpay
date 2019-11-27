<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Mashanpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

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
        
        self::$reqType = 'curl';
        self::$unit   = '2';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;

        $data['amount']               = (string)($amount*100);
        $data['channelCode']          = $bank;
        $data['goodsName']            = "chongzhi"; #商品名称
        $data['orderNum']             = $order; #商户订单号
        $data['organizationCode']     = $payConf['business_num']; #商户号
        $data['payResultCallBackUrl'] = $ServerUrl; #通知地址
        $data['payViewUrl']           = $returnUrl;
        $data['remark']               = (string)($amount)*100;

        $jsonStr         = self::getJsonStr($data, true, true);
        $baseInfo        = self::getRsaPublicSign($jsonStr, $payConf['public_key']);
        $post['sign']    = strtoupper(md5($jsonStr.$payConf['md5_private_key'])); #生成签名
        $post['data']    = $baseInfo;
        $post['orderNo'] = $order;
        $post['merNo']   = $payConf['business_num'];
        $post['amount']   = $data['amount'];
        unset($reqData);
        return $post;
    }

    function Mashanpay_callback(){
        $data = $_REQUEST;
        echo '0';
        $payConf = $this->get_payconf($data['orderNo']);
        $uid = $payConf['uid'];
        $dataStr = $data['data'];
        $prKey = openssl_pkey_get_private($payConf['pay_key']);
        $dataS = base64_decode($dataStr);
        $crypto = '';
        //分段解密   
        foreach (str_split($dataS, 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $prKey);
            $crypto .= $decryptData;
        }
        $array  = json_decode($crypto,true);
        $key    = explode("@", $payConf['pay_id'])[1];
        $md5Key = strtoupper(md5(json_encode($array) . $key));
        if($md5Key == $data['sign'] && $array['payStateCode'] == '10'){
           $this->Online_api_model->update_order($uid,$data['orderNo'],$array['orderAmount']/100); 
        }else {
            $this->Online_api_model->add_callback($data['orderNo'],$data,'');
        }
    }

    //回调处理
    public static function SignOther($type, $data, $payConf)
    {
        $decode = $data['data'];
        $signStr = $data['sign'];
        $prKey = openssl_pkey_get_private($payConf['rsa_private_key']);
        $datas = base64_decode($decode);
        $crypto = '';
        //分段解密
        foreach (str_split($datas, 128) as $chunk) {
            openssl_private_decrypt($chunk, $decryptData, $prKey);
            $crypto .= $decryptData;
        }
        $array = json_decode($crypto, true);
        $md5Key = strtoupper(md5(json_encode($array) . $payConf['md5_private_key']));
        if ( $md5Key == $signStr && $array['payStateCode'] == '10') {
            return true;
        }else{
            return false;
        }
    }
}