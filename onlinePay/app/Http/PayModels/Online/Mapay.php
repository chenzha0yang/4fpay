<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;

class Mapay extends ApiModel
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
        $data = array(
            'id'            => $payConf['business_num'],  //商户号
            'type'          => $bank,                     //支付方式
            'param'         => $order,                    //即时到账
            'price'         => $amount,                   //金额
            'pay_id'        => $order,                    //订单号
            'notify_url'    => $ServerUrl,                //异步通知地址
            'return_url'    => $returnUrl,
        );

        ksort($data);
        $sign = ''; //初始化需要签名的字符为空
        $urls = ''; //初始化URL参数为空

        foreach ($data AS $key => $val) { //遍历需要传递的参数
            if ($val == ''||$key == 'sign') continue; //跳过这些不参数签名
            if ($sign != '') { //后面追加&拼接URL
                $sign .= "&";
                $urls .= "&";
            }
            $sign .= "$key=$val"; //拼接为url参数形式

        }
        $data['sign'] = md5($sign .$payConf['md5_private_key']); //创建订单所需的参数

        unset($reqData);
        return $data;
    }

    public static function SignOther($model, $data,$payConf)
    {
        ksort($data); //排序post参数
        reset($data); //内部指针指向数组中的第一个元素
        $sign = $data['sign'];
        $signStr = '';//初始化
        foreach ($data AS $key => $val) { //遍历POST参数
            if ($val == '' || $key == 'sign') continue; //跳过这些不签名
            if ($signStr) $signStr .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
            $signStr .= "$key=$val"; //拼接为url参数形式
        }
        $signTrue = md5($signStr . $payConf['md5_private_key']);
        if (strtoupper($sign) == strtoupper($signTrue) && $data['pay_no']) {
            return true;
        }
        return false;
    }
}