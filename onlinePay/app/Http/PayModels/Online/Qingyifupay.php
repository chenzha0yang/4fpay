<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use App\Http\PayModels\PayLib\Qingyifupay\Qingyifu;

class Qingyifupay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    /*  回调数据  */
    public static $reqData = [];

    /**
     * @param array $reqData       接口传递的参数
     * @param array $payConf
     * @return array
     * @internal param null|string $user
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

        if (isset($reqData['isApp']) && $reqData['isApp'] == 1) {
            self::$isAPP = true;
        }

        self::$resType = 'json';
        self::$reqType = 'curl';
        self::$payWay = $payConf['pay_way'];
        $data = $post = array();
        $data['version'] = 'V2.0.0.0'; # 版本号
        $data['merNo'] = $payConf['business_num']; #商户号
        $data['netway'] = $bank;  #WX 或者 ZFB
        $data['random'] = (string) rand(1000,9999);  #4位随机数    必须是文本型
        $data['orderNum'] = $order;  #商户订单号
        $data['amount'] = (string)($amount * 100);  #默认分为单位 转换成元需要 * 100   必须是文本型
        $data['goodsName'] = 'abcd';  #商品名称
        $data['charset'] = 'utf-8';  # 系统编码
        $data['callBackUrl'] = $ServerUrl;  #通知地址 可以写成固定
        $data['callBackViewUrl'] = ""; #暂时没用
        ksort($data); #排列数组 将数组已a-z排序
        $sign = md5(Qingyifu::json_encode($data) . $payConf['md5_private_key']); #生成签名
        $data['sign'] = strtoupper($sign); #设置签名

        $post['data'] = Qingyifu::json_encode($data);
        $post['orderNum'] = $order;
        $post['amount'] = $amount;
        unset($reqData);
        return $post;
    }

    /**
     * @param $requset
     * @return mixed
     */
    public static function getVerifyResult($requset)
    {
        $data = json_decode($requset->all(), true);
        self::$reqData = $data;
        $res['order']  = $data['orderNum'];
        $res['amount'] = $data['amount']/100;
        return $res;
    }


    /**
     * @param $mod
     * @param $sign
     * @param $payConf
     * @return bool
     */
    public static function SignOther($mod, $sign, $payConf)
    {
        $data = self::$reqData;
        $arr = array();
        foreach ($data as $key => $v){
            if ($key !== 'sign'){ #删除签名
                $arr[$key] = $v;
            }
        }
        ksort($arr);
        $sign = strtoupper(md5(json_encode($arr) .$payConf['md5_private_key'])); #生成签名
        if (strtoupper($sign) == strtoupper($data['sign'])) {
            if($data['payResult'] == '00'){
                return true;
            }
        }
        return false;
    }


}