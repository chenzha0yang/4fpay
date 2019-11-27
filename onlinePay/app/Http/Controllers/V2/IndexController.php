<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\APIController;
use App\Extensions\Curl;
use App\Http\Models\PayCallbackUrl;
use App\Http\Models\PayOrder;
use App\Http\Models\PayConfig;
use App\Http\Models\PayMerchant;
use App\Http\Models\PayType;

class IndexController extends APIController
{
    // 三方对应模型
    private static $PayModel = null;
    // 提交地址
    private static $queryUrl = '';
    //组装提交的数据
    protected static $sendData = [];
    //同步返回结果
    protected static $response = '';

    /**
     * 支付入口
     *
     * @return array|bool|\Illuminate\Contracts\Translation\Translator|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|null|object|string
     */
    public function recharge()
    {
        //验证订单号是否存在
        if (PayOrder::findOrder(self::$PKData['order'])) {
            return $this->responseJson(trans('error.orderError'));
        }
        // 获取商户配置
        $payMerchant = PayMerchant::getMerchant(self::$PKData);

        if (!$payMerchant) {
            return $this->responseJson(trans('error.PayMerchantError'));
        }
        $maintain = MaintainController::getPayIdMaintain($payMerchant->pay_id);

        if ($maintain) {
            return $maintain;
        }
        // 验证代理合法性
        if ($payMerchant->business_num != self::$PKData['businessNum']) {
            return $this->responseJson(trans('error.businessNumError'));
        }
        // 获取三方配置
        $payConfig = PayConfig::getPayTypeCode($payMerchant->pay_id);

        if (!$payConfig) {
            return $this->responseJson(trans('error.noBusinessMod'));
        }
        //获取三方配置对应的支付Code码
        if (!self::$PKData['bank']) {
            self::$PKData['bank'] = PayConfig::getConfigCode($payMerchant->pay_id,$payMerchant->pay_way);
        }
        // 写入订单
        if (empty(self::$PKData['notifyUrl'])) {
            self::$PKData['notifyUrl'] = PayCallbackUrl::getCallbackUrlByAgent([
                'agentId'    => $payMerchant->agent_id,
                'clientId'   => $payMerchant->client_id
            ]);
        }
        $owOrderNum = date('YmdHis', time()) . rand(100000, 999999);
        $create     = PayOrder::createOrder($payMerchant, self::$PKData, $owOrderNum);

        if (!$create->getKey()) {
            return $this->responseJson(trans('error.orderDataError'));
        }

        if ($payMerchant->mer_url) {
            self::$queryUrl = $payMerchant->mer_url;//客户自行填写支付网关
        } else {
            //根据三方id与支付方式 获取支付网关
            self::$queryUrl = PayConfig::getPayUrl($payMerchant->pay_id, $payMerchant->pay_way);
        }

        // 防止三方model里边需要使用支付网关
        self::$PKData['formUrl'] = self::$queryUrl;

        //商户 异步通知地址
        self::$PKData['ServerUrl'] = "{$payMerchant->callback_url}/api/v2/{$payConfig}/callback";
        //商户 同步通知地址
        self::$PKData['returnUrl'] = "{$payMerchant->callback_url}/return_url";

        if (!self::$queryUrl) {
            return $this->responseJson(trans('error.payWayError'));
        }

        if ($payMerchant->pay_code) {
            self::$PKData['bank'] = $payMerchant->pay_code;//客户自行填写支付编码
        }

        global $app;
        self::$PayModel = $app->make("App\\Http\\PayModels\\Online\\$payConfig");

        self::$sendData = (self::$PayModel)::getData(self::$PKData, (array)$payMerchant);

        switch ((self::$PayModel)::$action)
        {
            case 'formGet':
                return self::buildForm('get');
            case 'formPost':
                return self::buildForm('post');
            case 'curlGet':
                self::$response = self::curlRequest('get');
                break;
            case 'curlPost':
                self::$response = self::curlRequest('post');
                break;
            case 'fileGetContent':
                self::$response = self::fileGetContent();
                break;
            case 'other':
                self::$response = (self::$PayModel)::other(self::$sendData);
                break;
            default:
                return self::buildForm('post');
                break;
        }

        if (empty(self::$response)) {
            return $this->viewCode(
                $payMerchant->pay_way,
                (self::$PKData)['order'],
                (self::$PKData)['amount'],
                trans('error.payError1'),
                2
            );
        }

        (self::$PayModel)::getQrCode(self::$response);

        if (empty((self::$PayModel)::$result)) {
            return $this->viewCode(
                $payMerchant->pay_way,
                (self::$PKData)['order'],
                (self::$PKData)['amount'],
                trans('error.payError1'),
                2
            );
        }

        if ((isset(((self::$PayModel)::$result)['appPath']) && !empty(((self::$PayModel)::$result)['appPath']) && $payMerchant->is_app == 1) || (isset(((self::$PayModel)::$result)['appPath']) && !empty(((self::$PayModel)::$result)['appPath']) && isset((self::$PayModel)::$pc) && (self::$PayModel)::$pc === true)) {
            $aa = ((self::$PayModel)::$result)['appPath'];
            echo "<script type='text/javascript'>window.location.replace('{$aa}');</script>";
            exit();
        }

        if (isset(((self::$PayModel)::$result)['qrCode']) && !empty(((self::$PayModel)::$result)['qrCode'])) {
            // 成功展示二维码
            if(isset((self::$PayModel)::$imgSrc) && (self::$PayModel)::$imgSrc === true){
                return $this->viewCode(
                    $payMerchant->pay_way,
                    (self::$PKData)['order'],
                    (self::$PKData)['amount'],
                    ((self::$PayModel)::$result)['qrCode'],
                    1,
                    (self::$PayModel)::$imgSrc
                );
            } else {
                return $this->viewCode(
                    $payMerchant->pay_way,
                    (self::$PKData)['order'],
                    (self::$PKData)['amount'],
                    ((self::$PayModel)::$result)['qrCode']
                );
            }
        } else {
            //失败展示错误信息 errorCode  失败编码 errorMsg   失败信息
            $msg   = ((self::$PayModel)::$result)['msg'];
            $code  = ((self::$PayModel)::$result)['code'];
            $error = $msg . ',编码:' . $code;
            return $this->viewCode(
                $payMerchant->pay_way,
                (self::$PKData)['order'],
                (self::$PKData)['amount'],
                $error,
                2
            );
        }
    }


    /**
     * @param $payWay
     * @param $order
     * @param $amount
     * @param $body
     * @param int $type
     * @param bool $imgSrc
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function viewCode($payWay, $order, $amount, $body, $type = 1, $imgSrc = false)
    {
        $data = [
            'payWay' => PayType::getPayTypeRedis($payWay),
            'order'  => $order,
            'amount' => $amount,
        ];
        if ($type === 1) {
            $data['qrCodeUrl'] = $body;
            if($imgSrc){
                return view('imgSrc', $data);
            } else {
                return view('qrCode', $data);
            }
        } else {
            $data['error'] = $body;
            return view('errorCode', $data);
        }
    }

    /**
     * 表单提交POST
     *
     * @param $method
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private static function buildForm($method)
    {
        if (isset((self::$PayModel)::$changeUrl) && !empty((self::$PayModel)::$changeUrl)) {
            self::$queryUrl = (self::$PayModel)::$changeUrl;
        }
        return view('buy', [
            'action' => self::$queryUrl,
            'method' => $method,
            'data'   => self::$sendData
        ]);
    }

    /**
     * curl请求
     *
     * @param $method
     * @return array|bool|object
     */
    private static function curlRequest($method)
    {

        if (isset((self::$PayModel)::$changeUrl) && !empty((self::$PayModel)::$changeUrl)) {
            self::$queryUrl = (self::$PayModel)::$changeUrl;
        }

        if (isset((self::$PayModel)::$httpBuildQuery) && (self::$PayModel)::$httpBuildQuery === true) {
            self::$sendData = http_build_query(self::$sendData);
        }

        if (isset((self::$PayModel)::$headerToArray) && (self::$PayModel)::$headerToArray === true) {
            Curl::$headerToArray = (self::$PayModel)::$headerToArray;
        }

        if (isset((self::$PayModel)::$header) && !empty((self::$PayModel)::$header)) {
            Curl::$header = (self::$PayModel)::$header;
            $method = 'header';
        }

        Curl::$request = self::$sendData;//提交数据
        Curl::$url     = self::$queryUrl;//支付网关
        Curl::$method  = $method;//提交方式

        return Curl::Request();
    }

    /**
     * file_get_contents
     *
     * @return bool|string
     */
    private static function fileGetContent()
    {
        if (isset((self::$PayModel)::$changeUrl) && !empty((self::$PayModel)::$changeUrl)) {
            self::$queryUrl = (self::$PayModel)::$changeUrl;
        }

        $queryData = http_build_query(self::$sendData);
        $options   = array(
            'http' => array(
                'method'  => 'POST',
                'header'  => 'Content-type:application/x-www-form-urlencoded',
                'content' => $queryData,
                'timeout' => 60,// 超时时间（单位:s）
            ),
            "ssl"  => array(
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ),
        );
        if (env('EG_AGENT_URL')) {
            self::$queryUrl = env('EG_AGENT_URL') . '?url=' . self::$queryUrl;
        }
        $context   = stream_context_create($options);
        ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20100101 Firefox/14.0.2');
        return file_get_contents(self::$queryUrl, false, $context);
    }
}