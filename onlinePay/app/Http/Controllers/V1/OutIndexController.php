<?php

namespace App\Http\Controllers\V1;

use App\Extensions\RedisConPool;
use App\Http\Controllers\APIController;
use App\Http\Models\OutBank;
use App\Http\Models\OutMerchant;
use App\Http\Models\OutOrder;
use App\Http\Models\PayConfig;

class OutIndexController extends APIController
{

    // 提交地址
    private $queryUrl = '';

    private $OutKey = 'OutMoneyListKey';


    /**
     * 出款入口
     *
     * @return array|bool|\Illuminate\Contracts\Translation\Translator|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|null|object|string
     */
    public function recharge()
    {
        // 验证订单号是否存在
        if (OutOrder::findOrder(self::$PKData['order'])) {
            return $this->responseJson(trans('error.orderError'));
        }

        // 获取商户配置
        $payMerchant = OutMerchant::getMerchant(self::$PKData);

        if (!$payMerchant) {
            return $this->responseJson(trans('error.PayMerchantError'));
        }
        $maintain = MaintainController::getPayIdMaintain($payMerchant->pay_id);
        if ($maintain) {
            return $maintain;
        }

        // 获取三方配置
        $payConfig = PayConfig::getPayTypeCode($payMerchant->pay_id, false);
        if ($payConfig === false) {
            return $this->responseJson(trans('error.payConfigOutError'));
        }

        // 验证代理合法性
        if ($payMerchant->business_num !== self::$PKData['businessNum']) {
            return $this->responseJson(trans('error.agentIdError'));
        }

        // 验证出款银行 是否支持
        $outBankCode = OutBank::bankCodeCheck(self::$PKData['bankName'], $payMerchant->pay_id);
        if (!$outBankCode) {
            return $this->responseJson(trans('error.bankCodeError'));
        }
        //银行编码从数据库中获取
        self::$PKData['bankCode'] = $outBankCode['bank_code'];

        // 写入订单
        $create = OutOrder::createOrder($payMerchant, self::$PKData);

        if (!$create->getKey()) {
            return $this->responseJson(trans('error.orderDataError'));
        }

        //根据三方id与支付方式 获取支付网关
        $this->queryUrl = PayConfig::getOutPayUrl($payMerchant->pay_id);
        if (!$this->queryUrl) {
            return $this->responseJson(trans('error.queryOutUrlError'));
        }

        // 防止三方model里边需要使用支付网关
        self::$PKData['OutUrl'] = $this->queryUrl;
        //对应模型名称
        self::$PKData['Model'] = $payConfig;
        //此三方出款是否需要查询结果
        self::$PKData['needQuery']       = PayConfig::getOutConfigNeedQuery($payMerchant->pay_id);
        self::$PKData['outMerchantInfo'] = $payMerchant;

        //商户 异步通知地址
        self::$PKData['ServerUrl'] = "{$payMerchant->callback_url}/api/v1/{$payConfig}/out_callback";

        //验证完成、加入队列执行
        $redis = RedisConPool::rPush($this->OutKey, json_encode(array_filter(self::$PKData)));
        if ($redis) {
            self::$response['status'] = true;
            self::$response['code']   = 200;
            return $this->responseJson(trans('success.success'));
        } else {
            return $this->responseJson(trans('error.error'));
        }
    }
}