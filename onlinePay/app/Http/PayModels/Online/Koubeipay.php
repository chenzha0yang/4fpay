<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\Extensions\Curl;

class Koubeipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

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

        self::$resType = 'other';
        self::$payWay = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        //判断是否需要跳转链接 is_app=1开启 2-关闭
        if ($payConf['is_app'] == 1) {
            self::$isAPP = true;
        }
        $data = array(
            'merchno'       => $payConf['business_num'],   //商户号
            'amount'        => sprintf('%.2f',$amount),
            'traceno'       => $order,
            'payType'       => $bank,
            'goodsName'     => 'goodsName',
            'notifyUrl'     => $ServerUrl,
            'settleType'    => '1',
        );
        //对数组进行排序
        ksort($data);
        $signStr = '';
        //遍历数组进行字符串的拼接
        foreach ($data as $k => $v){
            if ($v != null){
                $signStr = $signStr.$k."=".iconv('UTF-8','GBK//IGNORE',$v)."&";
            }
        }
        $data['signature'] = md5($signStr.$payConf['md5_private_key']); //MD5签名
        $data['url'] = $reqData['formUrl'];

        unset($reqData);
        return $data;
    }

    /**
     * 请求、二维码、链接处理
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response){
        $data = $response['data'];
        //请求网关
        Curl::$url = $data['url'];
        $signature = $data['signature'];
        unset($data['url']);
        unset($data['signature']);
        $dataStr = self::getSignStr($data,true,true);
        //GBK格式编码
        $dataStr = iconv('UTF-8','GBK',$dataStr.'&signature='.$signature);
        Curl::$request = $dataStr;
        //请求
        $result = Curl::Request();
        //UTF8格式处理乱码
        $result = iconv('utf-8','utf-8//IGNORE',$result);
        $responseData = json_decode($result,true);
        if($responseData['respCode'] == '00'){
            $resultData['barCode'] = $responseData['barCode'];
        }else{
            $resultData['respCode'] = $responseData['respCode'];
            $resultData['message']  = $responseData['message'];
        }

        return $responseData;
    }
}