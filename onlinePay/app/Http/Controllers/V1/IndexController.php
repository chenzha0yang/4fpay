<?php

namespace App\Http\Controllers\V1;

use App\Extensions\File;
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
    private $PayModel = null;

    // 提交参数
    private $queryData = array();

    // 提交地址
    private $queryUrl = '';

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
//        self::$PKData['order'] = $owOrderNum; // 已平台订单号提交
        if (!$create->getKey()) {
            return $this->responseJson(trans('error.orderDataError'));
        }

        if ($payMerchant->mer_url) {
            $this->queryUrl = $payMerchant->mer_url;//客户自行填写支付网关
        } else {
            //根据三方id与支付方式 获取支付网关
            $this->queryUrl = PayConfig::getPayUrl($payMerchant->pay_id, $payMerchant->pay_way);
        }

        // 防止三方model里边需要使用支付网关
        self::$PKData['formUrl'] = $this->queryUrl;

        //商户 异步通知地址
        self::$PKData['ServerUrl'] = "{$payMerchant->callback_url}/api/v1/{$payConfig}/callback";
        //商户 同步通知地址
        self::$PKData['returnUrl'] = "{$payMerchant->callback_url}/return_url";

        if (!$this->queryUrl) {
            return $this->responseJson(trans('error.payWayError'));
        }

        if ($payMerchant->pay_code) {
            self::$PKData['bank'] = $payMerchant->pay_code;//客户自行填写支付编码
        }

        global $app;
        $this->PayModel = $app->make("App\\Http\\PayModels\\Online\\$payConfig");

        $this->queryData = $this->UnsetEmptyKey(
            ($this->PayModel)::getAllInfo(self::$PKData, (array)$payMerchant)
        );

        //提交参数为空则返回自定义错误信息
        if (empty($this->queryData)) {
            return $this->viewCode(
                ($this->PayModel)::$payWay,
                ($this->PayModel)::$errorData['order'],
                ($this->PayModel)::$errorData['amount'],
                ($this->PayModel)::$errorData['msg'],
                2
            );
        }

        $func = $this->getReqFunc(($this->PayModel)::$reqType);

        $res = $this->{$func}();

        if (($this->PayModel)::$payWay) {
            if (empty($res)) {
                $req = trans("reqField.{$payConfig}");//三方参数信息
                return $this->viewCode(
                    ($this->PayModel)::$payWay,
                    $this->queryData[$req['order']],
                    $this->queryData[$req['amount']],
                    trans('error.payError1'),
                    2
                );
            }

            return $this->makeQRCode($res, $payConfig);
        }

        return $res;
    }

    /**
     * 生成二维码
     *
     * @param $res
     * @param $PayConfig
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function makeQRCode($res, $PayConfig)
    {
        if (isset(($this->PayModel)::$icon) && !empty(($this->PayModel)::$icon)) {
            $res = iconv(($this->PayModel)::$icon, 'UTF-8', $res);
        }

        if ($func = $this->getResFunc(
            ($this->PayModel)::$resType,
            "App\\Http\\PayModels\\Online\\$PayConfig"
        )
        ) {

            $qr = call_user_func($func, $res, true);

        } else {

            $qr = $res;

        }

        $req = trans("reqField.{$PayConfig}");//三方参数信息

        if (!$qr) {
            return $this->viewCode(
                ($this->PayModel)::$payWay,
                $this->queryData[$req['order']],
                $this->queryData[$req['amount']],
                trans('error.payError2'),
                2
            );
        }

        // 跳转APP
        if (isset(($this->PayModel)::$isAPP) && ($this->PayModel)::$isAPP === true) {
            if (isset($qr[$req['appPath']]) && !empty($qr[$req['appPath']])) {
                $this->queryUrl = $qr[$req['appPath']];
                echo "<script type='text/javascript'>window.location.replace('{$this->queryUrl}');</script>";
                exit();
                //                return header("location:{$this->queryUrl}");
            }
        }

        if (isset($qr[$req['qrPath']]) && !empty($qr[$req['qrPath']])) {
            // 成功展示二维码
            if(isset(($this->PayModel)::$imgSrc) && ($this->PayModel)::$imgSrc === true){
                return $this->viewCode(
                    ($this->PayModel)::$payWay,
                    $this->queryData[$req['order']],
                    $this->queryData[$req['amount']],
                    $qr[$req['qrPath']],
                    1,
                    ($this->PayModel)::$imgSrc
                );
            } else {
                return $this->viewCode(
                    ($this->PayModel)::$payWay,
                    $this->queryData[$req['order']],
                    $this->queryData[$req['amount']],
                    $qr[$req['qrPath']]
                );
            }


        } else {
            //失败展示错误信息 errorCode  失败编码 errorMsg   失败信息
            $msg   = $qr[$req['errorMsg']];
            $code  = $qr[$req['errorCode']];
            $error = $msg . ',编码:' . $code;
            return $this->viewCode(
                ($this->PayModel)::$payWay,
                $this->queryData[$req['order']],
                $this->queryData[$req['amount']],
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
        if (($this->PayModel)::$unit == '2') {//单位为分
            $amount = $amount / 100;
        }

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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function buildForm()
    {
        if (isset(($this->PayModel)::$changeUrl)
            && ($this->PayModel)::$changeUrl === true
        ) {
            $this->queryUrl = $this->queryData['queryUrl'];
            $this->queryData = $this->queryData['data'];
        }
        return view('buy', [
            'action' => $this->queryUrl,
            'method' => ($this->PayModel)::$method,
            'data'   => $this->queryData
        ]);
    }

    /**
     * curl请求
     *
     * @return array|bool|object
     */
    private function curlRequest()
    {
        $request = $this->queryData;
        if (isset(($this->PayModel)::$httpBuildQuery)
            && ($this->PayModel)::$httpBuildQuery === true
        ) {

            $request = http_build_query($this->queryData);
        }

        if ($this->checkType(($this->PayModel)::$method, 'HEADER')) {
            if (isset(($this->PayModel)::$headerToArray)) {
                Curl::$headerToArray = ($this->PayModel)::$headerToArray;
            }
            Curl::$header = $this->queryData['httpHeaders'];
            $request      = $this->queryData['data'];//提交数据
        }

        if (isset(($this->PayModel)::$changeUrl)
            && ($this->PayModel)::$changeUrl === true
        ) {
            $this->queryUrl = $this->queryData['queryUrl'];
            $request      = $this->queryData['data'];//提交数据
        }

        if (isset(($this->PayModel)::$postType)
            && ($this->PayModel)::$postType === true
        ) {

            $request = ($this->PayModel)::getRequestByType($this->queryData);
        }
        if (env('EG_AGENT_URL')) {
            $this->queryUrl = env('EG_AGENT_URL') . '?url=' . $this->queryUrl;
        }

        Curl::$request = $request;//提交数据
        Curl::$url     = $this->queryUrl;//支付网关
        Curl::$method  = ($this->PayModel)::$method;//提交方式

        return Curl::Request();
    }

    /**
     * file_get_contents
     *
     * @return bool|string
     */
    private function fileGetRequest()
    {
        $postData = $this->queryData;
        if (isset(($this->PayModel)::$changeUrl)
            && ($this->PayModel)::$changeUrl === true
        ) {
            $this->queryUrl = $this->queryData['queryUrl'];
            $postData = $this->queryData['data'];
        }

        $queryData = http_build_query($postData);
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
            $this->queryUrl = env('EG_AGENT_URL') . '?url=' . $this->queryUrl;
        }
        $context   = stream_context_create($options);
        ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20100101 Firefox/14.0.2');
        return file_get_contents($this->queryUrl, false, $context);
    }

    /**
     * 清除多余的参数
     *
     * @param $queryData
     * @return mixed
     */
    private function UnsetEmptyKey($queryData)
    {
        if (!empty($queryData['formUrl'])) {
            $this->queryUrl = $queryData['formUrl'];
            unset($queryData['formUrl']);
        }

        if (!empty($queryData['PHPSESSID'])) {
            unset($queryData['PHPSESSID']);
        }
        return $queryData;
    }
}