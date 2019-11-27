<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Dongfhvpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

    public static $unit = 1; //金额单位  默认1为元  2:单位为分

    public static $postType = false; //数据提交类型 默认false 一维数组 or json/str/多维数组  getRequestByType

    public static $httpBuildQuery = false; //默认false/true为curl提交参数需要http_build_query

    public static $isAPP = false; // 判断是否跳转APP 默认false

    public static $changeUrl = false;

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
        //TODO: do something
        $data['uid'] = $payConf['business_num'];//商户号
        $data['istype'] = $bank;//银行编码
        $data['orderid'] = $order;//订单号
        $data['price'] = sprintf('%.2f',$amount);//订单金额
        $data['orderuid'] = self::$UserName;
        $data['return_url'] = $returnUrl;
        $data['notify_url'] = $ServerUrl;
        $data['token'] = $payConf['md5_private_key'];
        //ksort($data);
        $signStr = $data['istype'].$data['notify_url'].$data['orderid'].$data['orderuid'].$data['price'].$data['return_url'].$payConf['md5_private_key'].$data['uid'];

        $data['key'] = strtolower(md5($signStr));

        self::$isAPP = true;
        self::$resType   = 'other';
        self::$payWay  = $payConf['pay_way'];
        $url = $reqData['formUrl'].'?'.http_build_query($data);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $datat = curl_exec($curl);
        curl_close($curl);

        $post['json']     = $datat;
        $post['orderid']  = $data['orderid'];
        $post['price'] = $data['price'];

        unset($reqData);
        return $post;
    }

    public static function getQrCode($res)
    {
        $post = $res['data']['json'];
        if ($post) {
            $data = json_decode($post,true);
            if(isset($data['code']) && $data['code'] == '200'){
                $data['payUrl'] = $data['body']['payurl'];
            }else{
                $data['code'] = $data['code'];
                $data['msg']  = $data['msg'];
            }
        } else {
            $data['code'] = '500';
            $data['msg']  = '请求失败,请联系管理员';
        }
        return $data;
    }

    public static function SignOther($mod, $data, $payConf)
    {
        $sign = $data['key'];
        unset($data['key']);
        $signStr =  $data['orderid'].$data['orderuid'].$data['platform_trade_no'].$data['price'].$data['realprice'];
        $signTrue = md5($signStr .$payConf['md5_private_key']);
        if (strtoupper($signTrue) == strtoupper($sign) ){
            return true;
        } else {
            return false;
        }
    }

}