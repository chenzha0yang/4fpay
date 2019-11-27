<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Zeshengpay extends ApiModel
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

        $data['merchantCode'] = $payConf['business_num'];  //商户号
        $data['outOrderId'] = $order;  //商户订单号

        $data['goodsName'] = 'telp';  //商品名称
        $data['goodsExplain'] = 'telp';  //商品描述
        $data['orderCreateTime'] = date("YmdHis",time()+3600*12);//订单创建时间  北京时间
        $data['lastPayTime'] = date("YmdHis",strtotime("+1 days")+3600*12);  //最晚支付时间
        $data['ext'] = 'remark';  //扩展字段
        $data['noticeUrl'] = $ServerUrl;  //异步通知

        if($payConf['pay_way'] == 1){ //网银
            $data['totalAmount'] = $amount * 100;  //交易金额。 单位分
            $data['bankCode'] = $bank;  //支付银行代码
            $data['bankCardType'] = '01';  //银行类型
            $data['merUrl'] = $returnUrl;  //同步通知

            $map = array(
                "merchantCode" => $data['merchantCode'],
                "outOrderId" => $data['outOrderId'],
                "totalAmount" => $data['totalAmount'],
                "orderCreateTime" => $data['orderCreateTime'],
                "lastPayTime" => $data['lastPayTime']
            );

        } else {
            $data['model'] = 'QR_CODE';
            $data['isSupportCredit'] = '0';
            $data['payChannel'] = $bank;
            $data['deviceNo'] = '';
            $data['ip'] = '127.0.0.1';
            $data['amount'] = $amount * 100;  //交易金额。 单位分
            self::$unit = 2;
            self::$reqType = 'fileGet';
            self::$payWay = $payConf['pay_way'];
            self::$resType = 'other';

            $map = Array(
                "merchantCode" => $data['merchantCode'],
                "outOrderId" => $data['outOrderId'],
                "amount" => $data['amount'],
                "orderCreateTime" => $data['orderCreateTime'],
                "noticeUrl" => $ServerUrl,
                "isSupportCredit" => '0'
            );

        }
        $signStr = self::getSignStr($map,false, true);
        $sign = strtoupper(self::getMd5Sign("{$signStr}&KEY=", $payConf['md5_private_key']));
        $data['sign'] = $sign; //签名
        return $data;
    }

    /**
     * @param $res
     * @return array  二维码处理
     */
    public static function getQrCode($res)
    {
        $returnQrPath = array();
        $result = json_decode($res,true);

        if(isset($result['code']) && $result['code'] == '00' ) {
            $qrPath = $result['data']['url'];
        }

        if(isset($qrPath) && !empty($qrPath)) {
            $returnQrPath['qrPath'] = $qrPath;
        } else {
            $returnQrPath['errorCode'] = $result['code'];
            $returnQrPath['errorMsg']  = $result['msg'];
        }

        return $returnQrPath;
    }

    /**
     * @param $request
     * @param $mod  金额单位处理
     * @return mixed
     */
    public static function getVerifyResult($request,$mod)
    {
        $backField = trans("backField.{$mod}");
        $res['order'] = $request->all()[$backField['order']];
        $res['amount'] = $request->all()[$backField['amount']]/100;
        return $res;
    }
}