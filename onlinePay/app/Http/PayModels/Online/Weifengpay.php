<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Weifengpay extends ApiModel
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
         *   参数赋值，方法间传递数组
         */
        $order     = $reqData['order'];
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        $returnUrl = $reqData['returnUrl'];
        //TODO: do something

        $data = [];
        $data['pay_memberid'] = $payConf['business_num'];                        //商户 ID

        switch ($payConf['pay_way']) {
            case '2':
                $data['pay_bankcode'] = 'WX';
                break;
            case '3':
                $data['pay_bankcode'] = 'ZFB';
                break;
            default:
                $data['pay_bankcode'] = 'yh';
                break;
        }//银行类型
        $data['pay_amount'] = sprintf('%.2f',$amount);                                        //金额
        $data['pay_applydate'] = date('Y-m-d H:i:s');
        $data['pay_orderid'] = $order;                                      //商户订单号
        $data['pay_notifyurl'] = $ServerUrl;//下行异步通知地址
        $data['pay_callbackurl'] = $returnUrl;
        $signSource = "pay_amount=>".$data['pay_amount']."&pay_applydate=>".$data['pay_applydate']."&pay_bankcode=>".$data['pay_bankcode']."&pay_callbackurl=>".$data['pay_callbackurl']."&pay_memberid=>".$data['pay_memberid']."&pay_notifyurl=>".$data['pay_notifyurl']."&pay_orderid=>".$data['pay_orderid'];
        $sign = strtoupper(md5($signSource.'&key='. $payConf['md5_private_key']));
        $data['tongdao'] = $bank;

        if ($payConf['pay_way'] == '1') {
            $data['tongdao'] = 'WY';
        }

        $data['pay_md5sign'] = $sign;//MD5 签名
        unset($reqData);
        return $data;
    }
}