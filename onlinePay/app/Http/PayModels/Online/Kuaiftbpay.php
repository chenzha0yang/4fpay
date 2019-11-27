<?php

namespace App\Http\PayModels\Online;

use App\ApiModel;

class Kuaiftbpay extends ApiModel
{
    public static $method = 'post'; //提交方式 必加属性 post or get

    public static $reqType = 'form'; //提交类型 必加属性 form or curl or fileGet

    public static $payWay = 0; //是否需要生成二维码 必加属性 2 3 4 5

    public static $resType = 'json'; //curl file_get_contents 返回的数据类型json/xml/str

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

        $data               = [];
        $data['p1_MerId'] = $payConf['business_num'];
        $data['p2_Order'] = $order;
        $data['p3_Amt'] = $amount;
        $data['p4_Cur'] = "CNY";
        $data['p5_Pid'] = 'name';
        $data['p6_Pcat'] = 'class';
        $data['p7_Pdesc'] = 'desc';
        $data['p8_Url'] = $ServerUrl;   
        $data['pa_MP'] = '1';
        $data['pd_FrpId'] = $bank;
        $data['pr_NeedResponse'] = "1";
        $data['p0_Cmd'] = "Buy";
        $hmac = self::getReqHmacString($data['p1_MerId'],$data['p2_Order'],$data['p3_Amt'],$data['p4_Cur'],$data['p5_Pid'],$data['p6_Pcat'],$data['p7_Pdesc'],$data['p8_Url'],$data['pa_MP'],$data['pd_FrpId'],$data['pr_NeedResponse'],$payConf['md5_private_key']);        
        $data['hmac'] = $hmac;

        unset($reqData);
        return $data;
    }

    //签名函数生成签名串
    public static function getReqHmacString($p1_MerId,$p2_Order,$p3_Amt,$p4_Cur,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pd_FrpId,$pr_NeedResponse,$merchantKey)
    {
      
      // 业务类型
      // 支付请求，固定值"Buy" .    
      $p0_Cmd = "Buy";
            
      //    送货地址
      $p9_SAF = "0";
            
        //进行签名处理，一定按照文档中标明的签名顺序进行
      $sbOld = "";
      //加入业务类型
      $sbOld = $sbOld.$p0_Cmd;
      //加入商户编号
      $sbOld = $sbOld.$p1_MerId;
      //加入商户订单号
      $sbOld = $sbOld.$p2_Order;     
      //加入支付金额
      $sbOld = $sbOld.$p3_Amt;
      //加入交易币种
      $sbOld = $sbOld.$p4_Cur;
      //加入商品名称
      $sbOld = $sbOld.$p5_Pid;
      //加入商品分类
      $sbOld = $sbOld.$p6_Pcat;
      //加入商品描述
      $sbOld = $sbOld.$p7_Pdesc;
      //加入商户接收支付成功数据的地址
      $sbOld = $sbOld.$p8_Url;
      //加入商户扩展信息
      $sbOld = $sbOld.$pa_MP;
      //加入支付通道编码
      $sbOld = $sbOld.$pd_FrpId;
      //加入是否需要应答机制
      $sbOld = $sbOld.$pr_NeedResponse;
        //logstr($p2_Order,$sbOld,HmacMd5($sbOld,$merchantKey));
      return self::HmacMd5($sbOld,$merchantKey);
      
    } 

    public static function getCallbackHmacString($p1_MerId,$r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType,$merchantKey)
    {
      
        #取得加密前的字符串
        $sbOld = "";
        #加入商家ID
        $sbOld = $sbOld.$p1_MerId;
        #加入消息类型
        $sbOld = $sbOld.$r0_Cmd;
        #加入业务返回码
        $sbOld = $sbOld.$r1_Code;
        #加入交易ID
        $sbOld = $sbOld.$r2_TrxId;
        #加入交易金额
        $sbOld = $sbOld.$r3_Amt;
        #加入货币单位
        $sbOld = $sbOld.$r4_Cur;
        #加入产品Id
        $sbOld = $sbOld.$r5_Pid;
        #加入订单ID
        $sbOld = $sbOld.$r6_Order;
        #加入用户ID
        $sbOld = $sbOld.$r7_Uid;
        #加入商家扩展信息
        $sbOld = $sbOld.$r8_MP;
        #加入交易结果返回类型
        $sbOld = $sbOld.$r9_BType;

        return self::HmacMd5($sbOld,$merchantKey);

    }



    function CheckHmac($p1_MerId,$r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType,$hmac,$merchantKey)
    {
        if($hmac == self::getCallbackHmacString($p1_MerId,$r0_Cmd,$r1_Code,$r2_TrxId,$r3_Amt,$r4_Cur,$r5_Pid,$r6_Order,$r7_Uid,$r8_MP,$r9_BType,$merchantKey))
            return true;
        else
            return false;
    }

    public static function HmacMd5($data,$key)
    {
    // RFC 2104 HMAC implementation for php.
    // Creates an md5 HMAC.
    // Eliminates the need to install mhash to compute a HMAC
    // Hacked by Lance Rushing(NOTE: Hacked means written)

    //需要配置环境支持iconv，否则中文参数不能正常处理
    $key = iconv("GB2312","UTF-8",$key);
    $data = iconv("GB2312","UTF-8",$data);

    $b = 64; // byte length for md5
    if (strlen($key) > $b) {
    $key = pack("H*",md5($key));
    }
    $key = str_pad($key, $b, chr(0x00));
    $ipad = str_pad('', $b, chr(0x36));
    $opad = str_pad('', $b, chr(0x5c));
    $k_ipad = $key ^ $ipad ;
    $k_opad = $key ^ $opad;

    return md5($k_opad . pack("H*",md5($k_ipad . $data)));
    }

    /**
     * @param $type    string 模型名
     * @param $data    array  回调数据
     * @param $payConf array  商户信息
     * @return bool
     */
    public static function SignOther($type, $data, $payConf)
    {
        $merchantKey = $payConf['md5_private_key'];
        //判断返回签名是否正确（True/False）
        $bRet = self::CheckHmac($data['p1_MerId'],$data['r0_Cmd'],$data['r1_Code'],$data['r2_TrxId'],$data['r3_Amt'],$data['r4_Cur'],$data['r5_Pid'],$data['r6_Order'],$data['r7_Uid'],$data['r8_MP'],$data['r9_BType'],$data['hmac'],$merchantKey);
        if($bRet){
            if($data['r1_Code']=="1"){
                return true;
            }
        }else{
            return false;
        }
    }
}