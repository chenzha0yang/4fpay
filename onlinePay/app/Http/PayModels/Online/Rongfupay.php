<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Rongfupay extends ApiModel
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
        $returnUrl = $reqData['returnUrl'];// 同步通知地址

        //TODO: do something
        $data['merNo'] = $payConf['business_num'];//商户号
        $data['aptNo'] = $payConf['business_num'];//资质编号
        $data['payNetway'] = $bank;//方式
        $data['random'] = (string) rand(1000,9999);  #4位随机数
        $data['orderNum'] = $order;//订单
        $data['ip'] = self::getIp();
        $data['amount'] = strval($amount*100);//商品描述
        $data['stuffName'] = 'vivo';
        $data['callBackUrl'] = $ServerUrl;
        $data['callBackViewUrl'] = $returnUrl;
        ksort($data);
        $json = json_encode($data,JSON_UNESCAPED_SLASHES);
        $sign = strtoupper(self::getMd5Sign($json, $payConf['md5_private_key']));
        $data['sign'] = $sign;//签名
        $datas = json_encode($data,JSON_UNESCAPED_SLASHES);
        $post = array('data'=>$datas);
        self::$reqType = 'curl';
        self::$unit = '2';

        unset($reqData);
        return $post;
    }

    /**
     * @param $type    string 模型名
     * @param $data    array  回调数据
     * @param $payConf array  商户信息
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $str = stripslashes($data['data']);
        $arr  = json_decode($str, TRUE);
        $sign = $arr['sign'];
        unset($arr['sign']);
        ksort($arr);
        $json = json_encode($arr,JSON_UNESCAPED_SLASHES);
        $mysign = strtoupper(self::getMd5Sign($json, $payConf['md5_private_key']));
        if ($mysign == $sign && $arr['result'] == '00') {
            return true;
        } else {
            return false;
        }
    }
}