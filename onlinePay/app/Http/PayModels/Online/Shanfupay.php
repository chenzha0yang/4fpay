<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Shanfupay extends ApiModel
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

        //TODO: do something

        $data = [];
        $data['MemberID'] = $payConf['business_num'];//商户号
        $data['TransID']= $order;//流水号
        $data['PayID'] = $bank;//银行参数
        $data['TradeDate'] = date('YmdHis');//交易时间
        $data['OrderMoney'] = $amount * 100;//订单金额
        $data['ProductName'] = 'shanfu';//产品名称
        $data['Amount'] = 1;//商品数量
        $data['Username'] = 'pk';//支付用户名
        $data['AdditionalInfo'] = '';//订单附加消息
        $data['PageUrl'] = $ReturnUrl;//通知商户页面端地址
        $data['ReturnUrl'] = $ServerUrl;//服务器底层通知地址
        $data['NoticeType'] = 1;//通知类型
        $Md5key = $payConf['md5_private_key'];//md5密钥（KEY）
        $MARK = "|";
        $Signature=md5($data['MemberID'].$MARK.$data['PayID'].$MARK.$data['TradeDate'].$MARK.$data['TransID'].$MARK.$data['OrderMoney'].$MARK.$data['PageUrl'].$MARK.$data['ReturnUrl'].$MARK.$data['NoticeType'].$MARK.$Md5key);
        $data['TerminalID'] = $payConf['message1'];//终端ID
        $data['InterfaceVersion'] = "4.0";
        $data['Signature'] = $Signature;
        $data['KeyType'] = "1";
        unset($reqData);
        return $data;
    }
}