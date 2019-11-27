<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Ruyipay extends ApiModel
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
        if($payConf['pay_way'] == '1'){
            $PanKid = $bank;//银行编码
            if($payConf['is_app'] == '1'){
                $PchaNnelId = '31';
            }else{
                $PchaNnelId = '1';
            }
        }else{
            $PchaNnelId = $bank;//支付类型
            $PanKid    = '';
        }
        $data                = array();
        $data['P_UserId']      = $payConf['business_num'];//商户编号
        $data['P_OrderId']     = $order;//订单编号
        $data['P_CardId']      = '';
        $data['P_CardPass']    = '';
        $data['P_FaceValue']   = $amount;//订单金额
        $data['P_ChannelId']   = $PchaNnelId;
        $md5Str = '';
        foreach ($data as $k => $v){
            $md5Str .= $v . '|';
        }
        $PostKey  =  self::getMd5Sign("{$md5Str}", $payConf['md5_private_key']);
        $data['P_Subject']     = 'chongzhi';
        $data['P_Price']       = $amount;
        $data['P_Quantity']    = 1;
        $data['P_Description'] = $PanKid;
        $data['P_Notic']       = '';
        $data['P_Result_url']  = $ServerUrl;
        $data['P_Notify_url']  = $returnUrl;
        $data['P_PostKey']     = $PostKey;
        unset($reqData);
        return $data;
    }
}
