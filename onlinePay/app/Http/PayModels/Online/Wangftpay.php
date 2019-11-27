<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Wangftpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

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

        $data['MerId']       = $payConf['business_num'];  //商户号
        $data['OrdId']       = $order;          //定单号
        $data['OrdAmt']      = sprintf('%.2f',$amount);           //金额
        $data['PayType']     = 'DT';             //支付类型
        $data['CurCode']     = 'CNY';             //支付币种
        $data['BankCode']    = $bank;            //银行代码
        $data['ProductInfo'] = 'dsn';
        $data['Remark']      = 'dsn';
        $data['ReturnURL']   = $returnUrl;    //同步地址
        $data['NotifyURL']   = $ServerUrl;      //异步地址
        $data['SignType']    = 'MD5';
        $signPars            = "";
        foreach ($data as $k => $v) {
            $signPars .= $k . "=" . $v . "&";
        }
        $data['SignInfo']         = md5($signPars . 'MerKey=' . $payConf['md5_private_key']);

        unset($reqData);
        return $data;
    }

    public static function SignOther($type, $data, $payConf)
    {
        $sign = $data['SignInfo'];
        unset($data['_input_charset']);
        unset($data['SignInfo']);
        $signPars = "";
        foreach ($data as $k => $v) {
            if ($k != 'SignInfo') {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars = rtrim($signPars, '&');
        $signTrue = md5(md5($signPars) . $payConf['md5_private_key']);

        if (strtoupper($sign) == strtoupper($signTrue)  && $data['ResultCode'] == 'success002') {
            return true;
        }
        return false;
    }


}