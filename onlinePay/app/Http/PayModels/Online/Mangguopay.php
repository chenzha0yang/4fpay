<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Mangguopay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];
        //TODO: do something

        self::$reqType = 'curl';
        self::$httpBuildQuery = true;
        self::$payWay = $payConf['pay_way'];
        $data = array(
            "down_sn" => $order,                //商户订单号
            "subject" => 'zhif',                //主题
            "amount" => $amount,                //交易金额
            "app_id" => '11',                   //应用id
            "notify_url" => $ServerUrl,         //异步通知地址
            "return_url" => $returnUrl,         //支付返回地址
        );

        if($payConf['pay_way'] == '1') {
            self::$isAPP = true;
            $data['type_code'] = 'gateway';     //支付类型
            $data['card_type'] = "1";           //银行卡类型
            $data['bank_segment'] = $bank;      //银行代号
            $data['user_type'] = "1";           //用户类型
            $data['agent_type'] = "1";          //渠道
        }else{
            $data['type_code'] = $bank;         //支付类型
        }
        $signStr = self::getSignStr($data, false,true);
        $data['sign'] = strtolower(self::getMd5Sign("{$signStr}&key=", $payConf['md5_private_key']));
        //组合数据
        $cipherData = self::encrypt($data, $payConf['public_key']);
        $post = array(
            'member_code' => $payConf['business_num'],
            'cipher_data' => $cipherData,
        );
        $post['money'] = $amount;
        $post['order_num'] = $order;
        unset($reqData);
        return $post;
    }

    /**
     * @param $params
     * @param $path
     * @return string
     */
    public static function encrypt($params, $path)
    {
        $originalData = json_encode($params);
        $cryPTo = '';
        $encryptData = '';
        foreach (str_split($originalData, 117) as $chunk) {
            openssl_public_encrypt($chunk, $encryptData, $path);
            $cryPTo .= $encryptData;
        }
        return base64_encode($cryPTo);
    }
}