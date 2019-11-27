<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Judingzpay extends ApiModel
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

        $data['ordercode']    = $order;
        $data['amount']       = $amount;
        $data['goodsId']      = $bank;
        $data['merNo']        = $payConf['business_num'];
        $data['callbackurl']  = $ServerUrl;
        $data['callbackMemo'] = 'jd';
        $data['notifyurl']    = $returnUrl;
        $signStr              = "amount={$data['amount']}&callbackMemo={$data['callbackMemo']}&callbackurl={$data['callbackurl']}&goodsId={$data['goodsId']}&key={$payConf['md5_private_key']}&merNo={$data['merNo']}&ordercode={$data['ordercode']}";
        $obj                  = [];
        $obj["ordercode"]     = $data['ordercode'];
        $obj["amount"]        = $data['amount'];
        $obj["callbackurl"]   = $data['callbackurl'];
        $obj["callbackMemo"]  = $data['callbackMemo'];
        $obj["goodsId"]       = $data['goodsId'];
        $obj["sign"]          = md5($signStr);
        $obj["merNo"]         = $data['merNo'];

        $result['data'] = json_encode($obj);
        $result['httpHeaders'] = array(
                 'Content-Type: application/json; charset=utf-8',
             );
        $result['amount'] = $data['amount'];
        $result['ordercode'] = $data['ordercode'];

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
        if ($responseData['result'] == '200'){
            $responseData['payUrl'] = $responseData['codeUrl'];
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
        $signStr = "amount={$data['amount']}&callbackMemo={$data['callbackMemo']}&callbackurl={$data['callbackurl']}&goodsId={$data['goodsId']}&key={$payConf['md5_private_key']}&ordercode={$data['ordercode']}&state={$data['state']}";
        $mysign  = md5($signStr);
        if (strtoupper($mysign) == strtoupper($sign) && $data['state'] == '10Z') {
            return true;
        } else {
            return false;
        }
    }

}