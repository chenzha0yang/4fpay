<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;
class Yufuvpay extends ApiModel
{

    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = ''; //curl file_get_contents 返回的数据类型json/xml/str

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
        self::$unit  = 2;

        $data               = array();
        $data['app_id']     = $payConf['business_num'];      //商户编号
        $data['pay_type']   =  $bank;       //支付类型
        $data['amount']     = $amount*100;  //金额分
        $data['order_id']   = $order;       //订单号
        $data['notify_url'] = $ServerUrl;   //商户后台通知地址
        $data['return_url'] = $returnUrl;   //同步停止地址

        $signStr = self::getSignStr($data, true, true); //true1 为空不参与签名，true2排序
        $data['sign'] = self::getMd5Sign($signStr, $payConf['md5_private_key']);
        unset($reqData);
        $data['extend'] = 'test';
        return $data;
    }

    //回调金额化分为元
    public static function getVerifyResult(Request $request, $mod)
    {
        $res = $request->all();
        $res['amount'] = $res['amount']/100;
        return $res;
    }

    /**
     * @param $type
     * @param $json
     * @param $payConf
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $sign      = $data['sign']; //取SIGN
        $md5str = "amount={$data['amount']}&app_id={$data['app_id']}&order_id={$data['order_id']}&state={$data['state']}&trade_no={$data['trade_no']}";
        $signStr = strtolower(md5($md5str.$payConf['md5_private_key']));
        if (strtolower($sign) == $signStr && $data['state'] == '1') {
            return true;
        } else {
            return false;
        }
    }


}
