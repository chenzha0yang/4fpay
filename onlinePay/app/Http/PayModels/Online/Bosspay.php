<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
class Bosspay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        self::$unit    = 2;
        self::$reqType = 'header';
        self::$resType = 'other';
        self::$payWay  = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        //TODO: do something
        date_default_timezone_set('Asia/Shanghai');
        $data = array(
            'appid'           => $payConf['business_num'],  //商户号
            'orderNo'         => $order,                    //订单号
            'dateTime'        => date('YmdHis'),    //下单时间
            'payType'         => $bank,                     //支付方式
            'amount'          => $amount*100,               //金额
            'productName'     => 'VIP',
            'asynNotifyUrl'   => $ServerUrl,                //异步通知地址
            'returnUrl'       => $returnUrl,
        );

        krsort($data);
        $param = "";
        foreach($data as $x=>$x_value){
            $param = $param.$x_value;
        };

        $name = iconv('UTF-8','GBK',$param);
        $utf8Str = mb_convert_encoding(strtoupper(md5($name)), "UTF-8");
        $data['sign'] = strtoupper(hash_hmac("sha1", $utf8Str, $payConf['md5_private_key']));
        $opts = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($data)
            )
        );
        $context = stream_context_create($opts);
        $result  = file_get_contents($reqData['formUrl'], false, $context);
        $results = iconv("utf-8", "utf-8//IGNORE",$result);

        //将json数组放入新数组中
        $post = [];
        $post['json']    = $results;
        $post['orderNo'] = $data['orderNo'];
        $post['amount']  = $data['amount'];
        $post['sign']    = $data['sign'];

        unset($reqData);
        return $post;
    }

    /**
     * 二维码处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $responseData = json_decode($response['data']['json'],true);
        $result = json_decode($responseData['data'],true);
        if($responseData['code'] == 'success'){
            $data['payUrl']   = $result['payUrl'];
        }else{
            $data['code']     = $responseData['code'];
            $data['message']  = $responseData['message'];
        }

        return $data;
    }

    public static function getVerifyResult($request, $mod)
    {
        $arr = $request->all();
        if (isset($arr['amount'])) {
            $arr['amount'] = $arr['amount'] / 100;
        }
        return $arr;
    }
    /**
     * 特殊处理
     * @param $model
     * @param $data
     * @param $payConf
     * @return bool
     */
    public static function SignOther($model, $data, $payConf){
        //获取数组中的数据 - 签名
        $callbackData = [
            'appId' => $data['appId'],
            'orderNo'  => $data['orderNo'],
            'payTrxNo'   => $data['payTrxNo'],
            'version' => $data['version'],
            'amount' => $data['amount'],
        ];
        krsort($callbackData);
        $param = "";
        foreach($data as $x=>$x_value){
            $param = $param.$x_value;
        };

        $name = iconv('UTF-8','GBK',$param);
        $utf8Str = mb_convert_encoding(strtoupper(md5($name)), "UTF-8");
        $paySign = strtoupper(hash_hmac("sha1", $utf8Str, $payConf['md5_private_key']));
        if($data['sign'] == $paySign && $data['resultcode'] == '0000'){
            return true;
        }else{
            return false;
        }
    }
}