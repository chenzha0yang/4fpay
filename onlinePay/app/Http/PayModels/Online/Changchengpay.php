<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Changchengpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $httpBuildQuery = false; //默认 false  true为curl提交参数 需要http_build_query

    public static $postType = false; //数据提交类型 默认false 一维数组   or  json ／str ／多维数组

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $icon = 'GB2312'; //返回信息字符编码

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

        $post = array(
            'merchno'      => $payConf['business_num'],
            'amount'       => $amount,
            'traceno' 	   => $order,
            'payType'      => $bank,
            'notifyUrl'    => $ServerUrl,
            'goodsName'    => 'chongzhi',
            'settleType'   => '1',
            'remark'       => "pk"
        );
        $signStr = self::getSignStr($post, false, true);
        $post['signature'] = strtoupper(self::getMd5Sign("{$signStr}&", $payConf['md5_private_key']));
        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$httpBuildQuery = true;
        if($reqData['isApp'] == 1){
            self::$isAPP = true;
        }
        unset($reqData);
        return $post;
    }

    public static function SignOther($mod,$sign,$payConf)
    {
        //签名操作
        ksort($sign);
        $a = '';
        foreach ($sign as $x => $x_value) {
            if ($x_value && $x != 'signature') {
                $a = $a . $x . "=" . $x_value . "&";
            }
        }
        $b = md5($a . $payConf['md5_private_key']);
        $d = strtoupper($b);
        $c = $sign['signature'];
        if ($d == $c && $sign['status'] == '1') {
            return true;
        } else {
            return false;
        }
    }
}