<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Tianxinzfpay extends ApiModel
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
        $ServerUrl = $reqData['ServerUrl']; // 异步通知地址
        $returnUrl = $reqData['returnUrl']; // 同步通知地址

        self::$reqType = 'curl';
        self::$payWay  = $payConf['pay_way'];
        self::$resType = 'other';
        self::$method  = 'header';
        $data                      = [];
        $data['no']                = (string)$payConf['business_num'];
        $data['merchant_order_no'] = (string)$order;
        $data['price']             = (string)$amount;
        $data['callback_url']      = (string)$ServerUrl;
        $data['redirect_back']     = (string)$returnUrl;
        $data['sign']              = (string)self::generateSign($data, $payConf['md5_private_key']);
        $httpHeaders = [
            'Content-Type: application/json', // 此欄會影響到發送請求時要求的資料格式
            'X-Requested-With: XMLHttpRequest', // 添加此選項以得到正確的返回資訊
            "Authorization: Bearer {$payConf['md5_private_key']}", // API 身分驗證：Bearer + 空格 + API_TOKEN
        ];
        $post['data'] = json_encode($data);
        $post['httpHeaders'] = $httpHeaders;
        $post['merchant_order_no'] = $order;
        $post['price'] = $amount;
        unset($reqData);
        return $post;
    }

    /**
     * @param $response
     * @return mixed
     */
    public static function getQrCode($response)
    {
        echo $response;die;
        return $result;
    }

    public static function generateSign(Array $attributed, $api_token)
    {
        $sign = ""; // 宣告驗證簽名

        ksort($attributed); // 將傳入的陣列以鍵名小到大排序

        // 將陣列裡的資料以 key=value 用 & 串接進簽名
        foreach ($attributed as $key => $value) {
            $sign .= "{$key}={$value}&";
        }

        $sign .= "api_token={$api_token}"; // 將 api_token 串接進明文中

        return strtoupper(md5($sign)); // 返回轉換為大寫的 md5 密文
    }
}
