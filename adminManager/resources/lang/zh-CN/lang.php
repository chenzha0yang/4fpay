<?php

use App\Extensions\Code;

return [
    'administrator'  => "超级管理员",
    'POST'           => "添加",
    'GET'            => "获取",
    'PUT'            => "修改",
    'DELETE'         => "删除",

    //入款订单下发
    'sendOrderError' => [
        "回调域名或IP未设置",
        "请勿篡改订单信息",
        "无此订单信息",
    ],
    //支付状态
    'orderSure'      => [
        '未支付',
        '支付成功',
        '支付失败',
    ],
    'succeeded'      => '成功',
    'failed'         => '失败',
    //下发状态
    'issued'         => [
        '未下发',
        '下发成功',
        '下发失败',
    ],
    'adminClient'    => [
        'administrator',
        '---',
    ],
    //是否显示维护状态 1显示 2不显示
    'status'         => [
        1,
        2,
    ],
    'redisType'      => [
        Code::REDIS_TYPE_STR  => 'String',
        Code::REDIS_TYPE_SET  => 'Set',
        Code::REDIS_TYPE_LIST => 'List',
        Code::REDIS_TYPE_HASH => 'Hash',
    ],

    'operation' => [
        'bankRunApi'     => '银行列表查询',
        'orderRunApi'    => '订单列表查询',
        'merchantRunApi' => '商户列表查询',
        'typeRunApi'     => '支付方式列表查询',
        'configRunApi'   => '三方列表查询',
        'businessAdd'    => '三方配置新增',
        'businessUpdate' => '三方配置修改',
    ],

    'requestLog' => [
        'api/v1/business/up'      => '商户新增/修改',
        'api/v1/order/list'       => '获取入款订单',
        'api/v1/outBusiness/up'   => '出款商户新增/修改',
        'api/v1/merchant/list'    => '入款商户获取',
        'api/v1/outMerchant/list' => '出款商户获取',
        'api/v1/config/list'      => '三方配置获取',
        'api/v1/bank/list'        => '入款银行获取',
        'api/v1/type/list'        => '支付方式获取',
        'api/v1/paymentBank'      => '支付宝转账新增/修改',
    ],
];
