<?php
namespace App\Http\PayModels\Online;

use App\ApiModel;
use Illuminate\Http\Request;

class Xiandaipay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str   other

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
        $order     = $reqData['order']; //订单号
        $amount    = $reqData['amount'];
        $bank      = $reqData['bank'];
        $ServerUrl = $reqData['ServerUrl'];// 异步通知地址
        //TODO: do something
        self::$unit = 2;        //单位：分
        $data = [];
        //处理码
        if($payConf['pay_way'] == '3'){                 //支付宝扫码
            $type = '900036';
        }elseif($payConf['pay_way'] == '1'){            //网银
            $type = '900034';
        }elseif($payConf['pay_way'] == '4'){            //QQ钱包
            $type = '900028';
        }elseif($payConf['pay_way'] == '6'){            //银联扫码
            $type = '900029';
        }elseif($payConf['pay_way'] == '9'){            //银联快捷
            $type = '900023';
        }elseif($payConf['pay_way'] == '7'){            //京东钱包
            $type = '900031';
        }
        $pt               = explode("@", $payConf['business_num']);
        $data['txcode']   = 'F60002';#固定
        $data['txdate']   = date('Ymd');
        $data['txtime']   = date('His');
        $data['version']  = '2.0.0';
        $data['field003'] = $type;
        $data['field004'] = (string)($amount*100);
        $data['field031'] = $bank;                                      //支付类别
        $data['field041'] =  $pt[1];                      //客户号
        $data['field042'] =  $pt[0];                  //商户号
        $data['field048'] = $order;                                     //商户订单号
        $data['field057'] = 'honor';                                    //商品名
        $data['field060'] = $ServerUrl;                                 //通知地址
        $data['field125'] = self::randStr1($payConf['business_num']);   //
        $string = '';
        foreach($data as $k => $v){
            $string .= $v;
        }
        $sub = strtoupper(md5($string . $payConf['md5_private_key']));
        $data['field128'] = substr($sub,0,16);

        if ($payConf['pay_way'] != 1) {
            self::$reqType = 'curl';
            self::$payWay = $payConf['pay_way'];
            self::$method = 'header';
            if($payConf['is_app'] == '1' && $payConf['pay_way'] == '3'){   //手机H5支付宝
                $data['field011'] = '000001';                                   //设备类型
                $data['field003'] = '900022';
            }
            if ($payConf['is_app'] == '1' && $payConf['pay_way'] == '2') {
                $data['field011'] = '000001';                                   //设备类型
                $data['field003'] = '900030';
            }
            if ($payConf['is_app'] == '1' && $payConf['pay_way'] == '7') {
                $data['field011'] = '000001';                                   //设备类型
                $data['field003'] = '900035';
            }
            $postDdta = json_encode($data);
            $post = [];
            $post['data'] = $postDdta;                                      //..提交的参数
            $post['httpHeaders'] = [
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($postDdta)
            ];
            $post['field048'] = $data['field048'];
            $post['field004'] = $data['field004'];
            unset($reqData);
            return $post;
        }
        unset($reqData);
        return $data;
    }

    //回调金额化分为元
    public static function getVerifyResult(Request $request, $mod)
    {
        $arr = $request->all();
        $arr['field004'] = $arr['field004']/100;
        return $arr;
    }

    //手机号
    public static function getmobile(){
        $arr = array(
            130,131,132,133,134,135,136,137,138,139,
            144,147,
            150,151,152,153,155,156,157,158,159,
            176,177,178,
            180,181,182,183,184,185,186,187,188,189,
        );

        return $arr[array_rand($arr)].mt_rand(1000,9999).mt_rand(1000,9999);
    }
    //随机数
    public static function randStr1($password){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        for ( $i = 0; $i < 12; $i++ ){
            $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $password;
    }

}