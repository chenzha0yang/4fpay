<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Weiweipay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        //TODO: do something
        $data['version']     = 'V6.79';
        $data['merchantid']  = $payConf['business_num'];//商户号
        $data['merordernum'] = $order;//订单
        $data['orderamt']    = number_format($amount, 2, '.', '');
        $data['bankcode']    = $bank;//支付类型
        $data['paytime']     = date("Y-m-d h:i:s");//订单时间
        $data['notifyurl']   = $ServerUrl;
        $data['returnurl']   = $returnUrl;
        ksort($data);   //按照ascii a~z 对数组进行排序
        $md5str = "";
        foreach ($data as $key => $val) {       //组装签名参数
            $md5str = $md5str . $key . "=" . $val . "&";
        }
        $sign = strtoupper(bin2hex(hash('sha256', $md5str . "key=" . $payConf['md5_private_key'], true))); //将组装好的参数以SHA256的方式进行加密，并且转换为大写。

        $data["hmac"] = $sign; //将生成好的签名加入数组中
        $data['attach'] = number_format($amount, 2, '.', '');//原样返回
        $data['goodname'] ='honor';//商品名称

        unset($reqData);
        return $data;
    }

    public static function signOther($model, $data, $payConf)
    {
        $hmac = $data['hmac'];
        unset($data['hmac']);
        ksort($data);
        $signStr = '';
        foreach($data as $k => $v){
            if($v !='' && $k !='attach'){
                $signStr .= $k . "=" . $v . '&';
            }
        }
        $sign = strtoupper(bin2hex(hash('sha256', $signStr . "key=" . $payConf['md5_private_key'], true)));
        if ($hmac == $sign && $data['trade_status'] == '0') {
            return true;
        } else {
            return false;
        }
    }


}
