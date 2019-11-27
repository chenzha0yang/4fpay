<?php

/**
 * 错误信息
 */
return [
    'orderSure'         => [
        '未支付',
        '支付成功',
        '支付失败',
    ],
    'issued'            => [
        '未下发',
        '下发成功',
        '下发失败',
    ],
    'sendOrderError'    => [
        "回调域名或IP未设置",
        "请勿篡改订单信息",
        "无此订单信息",
        "异常"
    ],
    'failed'            => '失败',
    'error'             => '出款请求失败，请重试！',
    'callUrlError'      => '回调地址错误！',
    'agentIdError'      => '参数错误！',
    'signError'         => '验签失败！',
    'agentNumError'     => '参数错误！',
    'orderError'        => '订单号不合法！',
    'PayMerchantError'  => '商户信息不存在！',
    'amountError'       => '金额参数不合法！',
    'payWayError'       => '支付方式不存在！',
    'merchantIdError'   => '商户ID错误！',
    'businessNumError'  => '商户号错误！',
    'noBusinessMod'     => '无此支付通道！',
    'orderDataError'    => '无此订单信息！',
    'PaySetError'       => '无法选择可使用的三方!请联系在线客服!',
    'cancelTransaction' => '交易被取消，请联系管理员！',
    'checkSignFail'     => '交易失败,错误代码cw0009',
    'backIpMsg'         => '支付异常,信息限制',
    'checkAmountFail'   => '支付额度异常',
    'NumFail'           => '异常',
    'tokenError'        => [
        '1' => '通道验证失败，请重试!【没有订单号或签名code=1】',
        '2' => '通道验证失败，请重试!【签名有误code=2】',
        '3' => '通道验证失败，请重试!【参数验证不通过code=3】',
        '4' => '通道验证失败，请重试!',
    ],
    'Fail_001'          => '交易失败,错误代码cw0001',
    'Fail_002'          => '交易失败,错误代码cw0002',
    'Fail_003'          => '入款失败,错误代码cw0003',
    'Fail_004'          => '入款失败,错误代码cw0004',
    'Fail_005'          => '入款失败,错误代码cw0005',
    'Fail_006'          => '入款失败,错误代码cw0006',
    'Fail_007'          => '入款失败,错误代码cw0007',
    'Fail_008'          => '入款失败,错误代码cw0008',

    'payError1' => '请求失败，请稍后重试！',
    'payError2' => '系统异常，请稍后重试！',


    'outTokenError'     => '通道验证失败',
    'bankCodeError'     => '出款银行不支持',
    'payConfigOutError' => '未开启出款',
    'queryOutUrlError'  => '未配置代付网关, 请联系客服',
    'outQuerying'       => '查询出款结果中...',
    'outQueryError'     => '加入查询队列失败...',
    'outMoneyError'     => '订单出款失败...',
    'PayUrlError'       => 'url配置失败...',
    'outfail'           => '出款请求失败，无返回信息',
    'outQfail'          => '出款查询失败，请检查网关',
];
