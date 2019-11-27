<?php

namespace App\Http\Controllers\V1;

use App\Extensions\Curl;
use App\Http\Controllers\APIController;
use App\Http\Models\Client;
use App\Http\Models\PayBank;
use App\Http\Models\PaymentBank;
use App\Http\Models\PayMerchant;
use Illuminate\Http\Request;


class QuickPayController extends APIController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showQuickPage()
    {
        /**
         * where  client_id=cw  is_quick=true agent=cw   getList
         *
         * show select (商户1，商户2)
         *
         * show bank list(联动)
         *
         * 提交金额
         */

        $where       = trans("transView.message");
        $payMerchant = PayMerchant::getMerchantRedis($where);
        $payBank     = PayBank::getBankId();
        $banks       = [];
        foreach ($payMerchant as $key => $merchant) {
            $num               = $key + 1;
            $merchant->payName = trans("transView.payName") . "{$num}";
            foreach ($payBank as $bank) {
                if ($bank->pay_id == $merchant->pay_id) {
                    $banks[$bank->pay_id][] = json_decode(json_encode($bank), true);
                }
            }
        }

        $payWay_data = [];
        foreach ($payMerchant as $k => $v) {
            if ($v->pay_way == 1) {
                $payWay_data['bank'][$k] = $v;
            } elseif ($v->pay_way == 2) {
                $payWay_data['wechat'][$k] = $v;
            } elseif ($v->pay_way == 3) {
                $payWay_data['alipay'][$k] = $v;
            } elseif ($v->pay_way == 4) {
                $payWay_data['qqpay'][$k] = $v;
            } elseif ($v->pay_way == 5) {
                $payWay_data['tenpay'][$k] = $v;
            } elseif ($v->pay_way == 6) {
                $payWay_data['visapay'][$k] = $v;
            } elseif ($v->pay_way == 7) {
                $payWay_data['jdpay'][$k] = $v;
            }
        }
        return view('quickPay', [
            'action' => '/quickPage',
            'data'   => $payWay_data,
            'banks'  => json_encode($banks)
        ]);
    }

    public static function getWayName($way)
    {
        $res = '';
        if (!$way) {
            $res = 'nochose';
        }
        if ($way == 1) {//网银
            $res = 'bank';
        } elseif ($way == 2) { //微信
            $res = 'wechat';
        } elseif ($way == 3) {  //支付宝
            $res = 'alipay';
        } elseif ($way == 4) {
            $res = 'qqpay';
        } elseif ($way == 5) {
            $res = 'tenpay';
        } elseif ($way == 6) {
            $res = 'visapay';
        } elseif ($way == 7) {
            $res = 'jdpay';
        } elseif ($way == 8) {
            $res = 'bdpay';
        }
        return $res;
    }

    public function quickPage(Request $request)
    {
        /**
         *
         * 调用getToken方法获取token
         *
         * 参数组装成和现金网一样
         *
         * return -> buy.html
         */

        $amount     = $request->amount;
        $bank       = $request->bank_code;
        $merchantId = $request->merchant_id;
        $payWay     = $request->payWay_id;
        $order      = date("YmdHis", time()) . rand(100000, 999999);
        if ($amount == '' || $amount == "0") {
            $errorMsg = [
                'payWay' => self::getWayName($payWay),
                'order'  => $order,
                'amount' => number_format(0, 2),
                'error'  => trans("transView.errorAmount")
            ];
            return view('errorCode', $errorMsg);
        }

        //未选择支付方式
        if ($payWay == '' || $payWay == "0") {
            $errorMsg = [
                'payWay' => 'nochose',
                'order'  => $order,
                'amount' => number_format(0, 2),
                'error'  => trans("transView.errorPayway")
            ];
            return view('errorCode', $errorMsg);
        }

        if ($merchantId == '' || $merchantId == 0) {
            $errorMsg = [
                'payWay' => self::getWayName($payWay),
                'order'  => $order,
                'amount' => number_format($amount, 2),
                'error'  => trans("transView.errorMerchantId")
            ];
            return view('errorCode', $errorMsg);
        }

        $client      = Client::getClientKey(trans("transView.message")['client_id']);
        $where       = [
            'agentId'    => trans("transView.message")['agent_id'],
            'agentNum'   => trans("transView.message")['agent_num'],
            'client_id'  => trans("transView.message")['client_id'],
            'merchantId' => $merchantId,
            'payWay'     => $payWay,
        ];
        $payMerchant = PayMerchant::getMerchant($where);
        $data        = [];
        // 客户端ID
        $data['clientUserId'] = $client->user_id;
        // 客户端名称
        $data['clientName'] = $client->client_name;
        // 客户端授权证书
        $data['clientSecret'] = $client->secret;
        // 订单号
        $data['order'] = $order;
        // 代理线ID
        $data['agentId'] = trans("transView.message")['agent_id'];
        // 子代理线ID
        $data['agentNum'] = trans("transView.message")['agent_num'];
        // token接口链接
        $tokenUrl = "{$payMerchant->callback_url}/api/v1/token";

        // 获取 token
        Curl::$request = $data;//提交数据
        Curl::$url     = $tokenUrl;//支付网关
        $response      = Curl::Request();
        $res           = json_decode($response, true);
        if ($res['status'] === true) {
            // 获取到 token 赋值
            $data['tokSign'] = $res['token'];
        } else {
            return view('tokenErr');
        }
        // 支付接口链接
        $requestUrl = "{$payMerchant->callback_url}/api/v1/buy";
        // 金额
        $data['amount'] = $amount;
        // 银行编码
        $data['bank'] = $bank;
        // 支付方式 1网银 2 微信 3 支付宝 4 QQ钱包
        $data['payWay'] = $payWay;
        // 商户主键ID
        $data['merchantId'] = $payMerchant->merchant_id;
        // 商户号
        $data['businessNum'] = $payMerchant->business_num;
        // 跳转App标示
        $data['isApp '] = '0';//pc端默认0为扫码，wap端 1为开启跳转App或者H5，
        // 异步通知地址
        $data['notifyUrl'] = "{$payMerchant->callback_url}/api/v1/notifyBack";
        // 签名组装
        $signData['order']        = $data['order'];
        $signData['agentId']      = $data['agentId'];
        $signData['agentNum']     = $data['agentNum'];
        $signData['amount']       = $data['amount'];
        $signData['bank']         = $data['bank'];
        $signData['payWay']       = $data['payWay'];
        $signData['merchantId']   = $data['merchantId'];
        $signData['businessNum']  = $data['businessNum'];
        $signData['clientSecret'] = $data['clientSecret']; // 参与签名，但不能提交
        $signData['notifyUrl']    = $data['notifyUrl'];
        unset($data['clientName']);
        unset($data['clientSecret']);
        ksort($signData);
        $str = '';
        foreach ($signData as $key => $val) {
            // 签名规则
            if (!is_null($val) && $val !== '') {
                $str .= "&&&{$key}=#{$val}#&&&";
            }
        }
        $data['sign'] = md5(md5($str));
        return view('buy', [
            'action' => $requestUrl,
            'method' => 'post',
            'data'   => $data
        ]);
    }

    /**
     *
     */
    public static function callback()
    {
        echo "SUCCESS";
    }

    /**
     *支付宝转账
     * @param Request $params
     */
    public static function csApi(Request $params)
    {
        $data   = $params->all();
        $type   = $data['type'];
        $id     = $data['a'];
        $money  = isset($data['b']) ? $data['b'] : '';
        $remark = isset($data['c']) ? $data['c'] : '';
        if (self::checkParameter($params, 'd', '/^([a-z]){4}$/', true)) {
            $urlMsg = PaymentBank::getzFbpay($id, $data['d']); // 查询长链接
            if (isset($urlMsg['longUrl'])) {
                //$url = 'alipays://platformapi/startapp';
                $url = env('PAYMENT_URL'); // url
                $url = str_replace('[url]', $url, $urlMsg['longUrl']);
                $url = str_replace('[amount]', $money, $url);
                if ($type == '1') { // 支付宝
                    $url = str_replace('[memo]', $remark, $url);
                }
                $url = urldecode($url);
                echo "<script type='text/javascript'>window.location.replace('{$url}');</script>";
                exit();
            } else {
                exit('支付宝转账失败');
            }
        }
    }

}
