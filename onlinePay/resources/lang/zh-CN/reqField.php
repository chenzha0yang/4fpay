<?php
/**
 * 提交参数整理
 */

return [
    //秒通支付
    'Miaotongpay'      => [
        'order'     => 'corp_flow_no',  //固定参数，订单号
        'amount'    => 'totalAmount',  //固定参数，金额
        'qrPath'    => 'Msg',  //response二维码key
        'appPath'   => 'Msg',  //response二维码key
        'errorCode' => 'Code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'Result',  //获取二维码失败的错误描述字段
    ],
    //小天鹅支付
    'Xiaotianepay'     => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'code_url',  //response二维码key
        'appPath'   => 'code_url',  //response二维码key
        'errorCode' => 'return_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'return_msg',  //获取二维码失败的错误描述字段
    ],
    //九龙支付
    'Jiulongpay'       => [
        'order'     => 'merchOrder',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response二维码key
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    //A付
    'Duoliyipay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'redirect_url',  //response二维码key
        'appPath'   => 'redirect_url',  //response二维码key
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',  //获取二维码失败的错误描述字段
    ],
    //A付
    'Afupay'           => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'orderPrice',  //固定参数，金额
        'qrPath'    => 'payMessage',  //response二维码key
        'errorCode' => 'resultCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',  //获取二维码失败的错误描述字段
    ],
    //轻易付
    'Qingyifupay'      => [
        'order'     => 'orderNum',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcodeUrl',  //response二维码key
        'appPath'   => 'qrcodeUrl',//response app/H5 跳转
        'errorCode' => 'stateCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],

    //A付
    'Akapay'           => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'paymentInfo',  //response二维码key
        'errorCode' => 'errorCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'errorMsg',  //获取二维码失败的错误描述字段
    ],

    //信付宝
    'Xinfpay'          => [
        'order'     => 'prdOrdNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'errorCode' => 'retCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',  //获取二维码失败的错误描述字段
    ],
    //泽圣
    'Zeshengpay'       => [
        'order'     => 'outOrderId',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrPath',//response二维码key
        'errorCode' => 'errorCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'errorMsg',  //获取二维码失败的错误描述字段
    ],

    //Wo付
    'Wofupay'          => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'qrcode',//response二维码key
        'errorCode' => 'resp_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_desc',  //获取二维码失败的错误描述字段
    ],

    // 智付RSA
    'Dinpayrsa'        => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'qrcode',//response二维码key
        'appPath'   => 'qrcode',//response app/H5 跳转
        'errorCode' => 'resp_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_desc',  //获取二维码失败的错误描述字段
    ],

    // 速汇宝
    'Suhuibaopay'      => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'qrcode',//response二维码key
        'appPath'   => 'payURL',//response app/H5 跳转
        'errorCode' => 'resp_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_desc',  //获取二维码失败的错误描述字段
    ],
    // 艾米森
    'AImispay'         => [
        'order'     => 'out_trade_no',   //固定参数，订单号
        'amount'    => 'total_fee',      //固定参数，金额
        'qrPath'    => 'pay_params',     //response二维码key
        'appPath'   => 'pay_params',    //response app/H5 跳转
        'errorCode' => 'respcd',      //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',      //获取二维码失败的错误描述字段
    ],

    //神付
    'Shenfupay'        => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => 'url',//response二维码key
        'errorCode' => 'status',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    //3v
    'Vvvpay'           => [
        'order'     => 'channelOrderId',  //固定参数，订单号
        'amount'    => 'totalFee',  //固定参数，金额
        'qrPath'    => '',//response二维码key
        'appPath'   => 'pay_info',//response app/H5 跳转
        'errorCode' => 'return_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'return_msg',  //获取二维码失败的错误描述字段
    ],
    //元宝
    'Yuanbaopay'       => [
        'order'     => 'ordernumber',  //固定参数，订单号
        'amount'    => 'paymoney',  //固定参数，金额
        'qrPath'    => 'qrurl',//response二维码key
        'appPath'   => '',//response app/H5 跳转
        'errorCode' => 'status',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',  //获取二维码失败的错误描述字段
    ],
    //汇合
    'Huihepay'         => [
        'order'     => 'OutTradeNo',  //固定参数，订单号
        'amount'    => 'TotalAmount',  //固定参数，金额
        'qrPath'    => 'QrCode',//response二维码key
        'errorCode' => 'Code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'Message',  //获取二维码失败的错误描述字段
    ],
    //新码付
    'Xinmapay'         => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'payUrl',//response二维码key
        'appPath'   => 'payUrl',//response app/H5 跳转
        'errorCode' => 'resCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'resDesc',  //获取二维码失败的错误描述字段
    ],
    //路德付
    'Ludepay'          => [
        'order'     => 'tradeNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',//response二维码key
        'appPath'   => '',//response app/H5 跳转
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'desc',  //获取二维码失败的错误描述字段
    ],
    //星付
    'Heshengpay'       => [
        'order'     => 'tradeNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'codeUrl',//response二维码key
        'errorCode' => 'error_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'error_msg',  //获取二维码失败的错误描述字段
    ],
    //长城
    'Changchengpay'    => [
        'order'     => 'traceno',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'barCode',//response二维码key
        'appPath'   => 'barCode',//response app/H5 跳转
        'errorCode' => 'respCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',  //获取二维码失败的错误描述字段
    ],
    //瞬付
    'Shunfupay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcodeInfo',//response二维码key
        'appPath'   => 'barCode',//response app/H5 跳转
        'errorCode' => 'resultCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'resultMsg',  //获取二维码失败的错误描述字段
    ],
    //在线宝
    'Zaixianbaopay'    => [
        'order'     => 'traceno',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'barCode',//response二维码key
        'appPath'   => '',//response app/H5 跳转
        'errorCode' => 'respCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',  //获取二维码失败的错误描述字段
    ],
    //威富通
    'Wfutongpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'code_url',//response二维码key
        'errorCode' => 'err_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'err_msg',  //获取二维码失败的错误描述字段
    ],
    //赢通宝
    'Yingtongbaopay'   => [
        'order'     => 'traceno',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'barCode',//response二维码key
        'errorCode' => 'respCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',  //获取二维码失败的错误描述字段
    ],
    //智汇付
    'Zhihuipay'        => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'qrcode',//response二维码key
        'appPath'   => 'payURL',//response app/H5 跳转
        'errorCode' => 'resp_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_desc',  //获取二维码失败的错误描述字段
    ],
    //多得宝
    'Duodebaopay'      => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'qrcode',//response二维码key
        'appPath'   => 'payURL',//response app/H5 跳转
        'errorCode' => 'resp_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_desc',  //获取二维码失败的错误描述字段
    ],

    //商银信
    'Scorepay'         => [
        'order'     => 'outOrderId',  //固定参数，订单号
        'amount'    => 'transAmt',  //固定参数，金额
        'qrPath'    => 'payCode',//response二维码key
        'appPath'   => '',//response app/H5 跳转
        'errorCode' => 'reCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',  //获取二维码失败的错误描述字段
    ],

    //芒果
    'Mangguopay'       => [
        'order'     => 'order_num',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'code_url',//response二维码key
        'appPath'   => 'code_url',//response app/H5 跳转
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],

    //汇潮
    'Huichaopay'       => [
        'order'     => 'BillNo',  //固定参数，订单号
        'amount'    => 'Amount',  //固定参数，金额
        'qrPath'    => 'qrCode',//response二维码key
        'appPath'   => '',//response app/H5 跳转
        'errorCode' => 'respCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',  //获取二维码失败的错误描述字段
    ],
    //得宝
    'Debaopay'         => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'qrcode',//response二维码key
        'appPath'   => 'payURL',//response app/H5 跳转
        'errorCode' => 'resp_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_desc',  //获取二维码失败的错误描述字段
    ],
    //鼎峰
    'Dingfengpay'      => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'qrCode',//response二维码key
        'appPath'   => '',//response app/H5 跳转
        'errorCode' => 'respCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',  //获取二维码失败的错误描述字段
    ],
    //金阳
    'Jinyangpay'       => [
        'order'     => 'p4_orderno',  //固定参数，订单号
        'amount'    => 'p3_paymoney',  //固定参数，金额
        'qrPath'    => 'r6_qrcode',//response二维码key
        'appPath'   => 'r6_qrcode',//response app/H5 跳转
        'errorCode' => 'rspCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'rspMsg',  //获取二维码失败的错误描述字段
    ],
    //YZ聚合
    'Yzjhpay'          => [
        'order'     => 'merchantOutOrderNo',  //固定参数，订单号
        'amount'    => 'orderMoney',  //固定参数，金额
        'qrPath'    => 'url',//response二维码key
        'appPath'   => 'url',//response app/H5 跳转
        'errorCode' => 'rspCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'rspMsg',  //获取二维码失败的错误描述字段
    ],
    //全球支付
    'Quanqiuzfpay'     => [
        'order'     => 'mer_order_no',  //固定参数，订单号
        'amount'    => 'trade_amount',  //固定参数，金额
        'qrPath'    => 'trade_return_msg',//response二维码key
        'appPath'   => 'url',//response app/H5 跳转
        'errorCode' => 'trade_result',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'error_msg',  //获取二维码失败的错误描述字段
    ],

    //易云汇
    'Yiyunhpay'        => [
        'order'     => 'merchantNo',   //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'codeUrl',      //response二维码key
        'appPath'   => 'url',         //response app/H5 跳转
        'errorCode' => 'dealCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'dealMsg',    //获取二维码失败的错误描述字段
    ],

    //农付
    'Nongpay'          => [
        'order'     => 'merOrderId',   //固定参数，订单号
        'amount'    => 'txnAmt',       //固定参数，金额
        'qrPath'    => 'payLink',      //response二维码key
        'appPath'   => 'prepayUrl',   //response app/H5 跳转
        'errorCode' => 'msg',       //获取二维码失败的状态吗字段
        'errorMsg'  => 'success',    //获取二维码失败的错误描述字段
    ],

    // 新百付通
    'Baiftpay'         => [
        'order'     => 'outOrderId',   //固定参数，订单号
        'amount'    => 'transAmt',       //固定参数，金额
        'qrPath'    => 'payCode',      //response二维码key
        'appPath'   => 'url',   //response app/H5 跳转
        'errorCode' => 'respCode',       //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],

    // 琦付
    'Qifupay'          => [
        'order'     => 'out_trade_no',   //固定参数，订单号
        'amount'    => 'total_fee',      //固定参数，金额
        'qrPath'    => 'code_url',       //response二维码key
        'appPath'   => 'url',           //response app/H5 跳转
        'errorCode' => 'status',      //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',      //获取二维码失败的错误描述字段
    ],

    // 通扫
    'Tongsaopay'       => [
        'order'     => 'traceno',       //固定参数，订单号
        'amount'    => 'amount',        //固定参数，金额
        'qrPath'    => 'barCode',       //response二维码key
        'errorCode' => 'respCode',   //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],

    //溢
    'Yjpay'            => [
        'order'     => 'ordId',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'codeurl',//response二维码key
        'appPath'   => 'qrCodeUrl',//response app/H5 跳转
        'errorCode' => 'return_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'return_msg',  //获取二维码失败的错误描述字段
    ],

    // 掌托
    'Zhangtuopay'      => [
        'order'     => 'ordernumber',   //固定参数，订单号
        'amount'    => 'paymoney',       //固定参数，金额
        'qrPath'    => 'qrurl',      //response二维码key
        'errorCode' => 'status',       //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],

    //新e付
    'Xinefpay'         => [
        'order'     => 'orderId',      //固定参数，订单号
        'amount'    => 'totalAmt',       //固定参数，金额
        'appPath'   => 'CODE_URL',     //response app/H5 跳转
        'qrPath'    => 'qrCode',      //response二维码key
        'errorCode' => 'respCode', //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],

    //赢鱼
    'Yingyupay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',     //固定参数，金额
        'appPath'   => 'CODE_URL',     //response app/H5 跳转
        'qrPath'    => 'payUrl',        //response二维码key
        'errorCode' => 'resCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resDesc',     //获取二维码失败的错误描述字段
    ],

    //天奕
    'Tianyipay'        => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'order_amt',     //固定参数，金额
        'appPath'   => 'pay_url',     //response app/H5 跳转
        'qrPath'    => 'pay_url',        //response二维码key
        'errorCode' => 'status_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'status_msg',     //获取二维码失败的错误描述字段
    ],

    //粤宝云付
    'Yuebyunpay'       => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'money',     //固定参数，金额
        'appPath'   => 'pay_url',     //response app/H5 跳转
        'qrPath'    => 'img',        //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],

    //天吉
    'Tianjipay'        => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'trans_amt',     //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'pay_link',        //response二维码key
        'errorCode' => 'ret_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'ret_msg',     //获取二维码失败的错误描述字段
    ],

    //高付通
    'Gaoftpay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',     //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',        //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],

    //易付
    'Yiyifupay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',     //固定参数，金额
        'appPath'   => 'code_url',     //response app/H5 跳转
        'qrPath'    => 'code_url',        //response二维码key
        'errorCode' => 'respcd',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',     //获取二维码失败的错误描述字段
    ],
    //随意付
    'Suiyipay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //33付
    'Sansanpay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'realprice',  //固定参数，金额
        'appPath'   => 'url',     //response app/H5 跳转
        'qrPath'    => 'qrcode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //天天付
    'Tiantianpay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //黄金支付
    'Huangjinpay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //红旗支付
    'Hongqipay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //驰鸟支付
    'Chiniaopay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //8king支付
    'Eightkingpay'     => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //虎付
    'Tigerpay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //麒麟付
    'Qilinpay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //益云
    'Yiyunpay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //双汇支付
    'Shuanghuipay'     => [
        'order'     => 'orderNo',  //固定参数，商户订单号
        'amount'    => 'orderAmount',  //固定参数，充值金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //真好付
    'Zhenhaopay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //金财汇
    'Jincaihuipay'     => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //融信付
    'Rongxinfupay'     => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //首捷
    'Shoujiepay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //尚信
    'Shangxinpay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //众信云
    'Zhongxinyunpay'   => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //众信云
    'Jingzhunpay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //聚创
    'Juchuangpay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //易付顺
    'Yifushunpay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //随便付
    'Suibianpay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //金木
    'Jinmupay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //佰钱付
    'Baiqianpay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //易乐享
    'Yilexiangpay'     => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //易付联
    'Yifulianpay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //本质
    'Benzhipay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //迅游通
    'Xunyoutongpay'    => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //微宝付
    'Weibaopay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //土豆支付
    'Tudoupay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //聚米付
    'Jumipay'          => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //金桔
    'Jinjupay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //尚付云
    'Shangfuyunpay'    => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //尚付云
    'Huifutongpay'     => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //K宝支付
    'Kbaopay'          => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'pay_link',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //信誉付
    'Xinyupay'         => [
        'order'     => 'ordNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'Payurl',      //response二维码key
        'errorCode' => 'resCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resMsg',     //获取二维码失败的错误描述字段
    ],
    //佰富
    'Baifupay'         => [
        'order'     => 'orderNum',  //固定参数，订单号
        'amount'    => 'payAmount',  //固定参数，金额
        'qrPath'    => 'CodeUrl',      //response二维码key
        'appPath'   => 'CodeUrl',     //response app/H5 跳转
        'errorCode' => 'resultCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resultMsg',     //获取二维码失败的错误描述字段
    ],
    //汇银支付
    'Huiyinpay'        => [
        'order'     => 'orderNum',  //固定参数，订单号
        'amount'    => 'payAmount',  //固定参数，金额
        'qrPath'    => 'CodeUrl',      //response二维码key
        'errorCode' => 'resultCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resultMsg',     //获取二维码失败的错误描述字段
    ],
    //云贝支付
    'Yunbeipay'        => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'fee',  //固定参数，金额
        'appPath'   => 'param',     //response app/H5 跳转
        'qrPath'    => 'qrData',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //邦银支付
    'Bangyinpay'       => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'fee',  //固定参数，金额
        'appPath'   => 'param',     //response app/H5 跳转
        'qrPath'    => 'qrData',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //恒付宝支付
    'Hengfubaopay'     => [
        'order'     => 'memorderId',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'appPath'   => 'codeUrl',     //response app/H5 跳转
        'qrPath'    => 'qrData',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //G支付
    'Gzhifupay'        => [
        'order'     => 'merOrderNo',  //固定参数，订单号
        'amount'    => 'realAmt',  //固定参数，金额
        'appPath'   => 'jumpUrl',     //response app/H5 跳转
        'errorCode' => 'respCode ',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg ',     //获取二维码失败的错误描述字段
    ],
    //百盛
    'Baishengpay'      => [
        'order'     => 'transactionId',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'payUrl',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg ',     //获取二维码失败的错误描述字段
    ],
    //顺安付
    'Shunanfupay'      => [
        'order'     => 'goodsno',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'info',     //获取二维码失败的错误描述字段
    ],
    //小蚂蚁
    'Xiaomayipay'      => [
        'order'     => 'traceno',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //新陆
    'Xinlupay'         => [
        'order'     => 'orderNo',   //固定参数，订单号
        'amount'    => 'payAmt',    //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    //曼巴
    'Manbapay'         => [
        'order'     => 'orderId',   //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //盛盈付
    'Shengyfpay'       => [
        'order'     => 'tradeNo',   //固定参数，订单号
        'amount'    => 'tradeAmount',    //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respInfo',     //获取二维码失败的错误描述字段
    ],
    //明捷付
    'Mingjpay'         => [
        'order'     => 'orderNum',   //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrcodeUrl',      //response二维码key
        'errorCode' => 'stateCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //汇聚
    'Huijupay'         => [
        'order'     => 'p2_OrderNo',   //固定参数，订单号
        'amount'    => 'p3_Amount',    //固定参数，金额
        'qrPath'    => 'rc_Result',      //response二维码key
        'errorCode' => 'ra_Code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'rb_CodeMsg',     //获取二维码失败的错误描述字段
    ],
    //支付通
    'Zhifutongpay'     => [
        'order'     => 'orderNo',   //固定参数，订单号
        'amount'    => 'transAmount',    //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respDesc',     //获取二维码失败的错误描述字段
    ],
    //入金宝
    'Rujinbaopay'      => [
        'order'     => 'out_trade_no',   //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'total_fee',      //response二维码key
        'errorCode' => 'trade_status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'body',     //获取二维码失败的错误描述字段
    ],
    //兴联
    'Xinglianpay'      => [
        'order'     => 'payOrderId',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'appPath'   => 'payUrl',     //response app/H5 跳转
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errCodeDes',     //获取二维码失败的错误描述字段
    ],
    //pk支付
    'Pkpay'            => [
        'order'     => 'payNo',   //固定参数，订单号
        'amount'    => 'realAmt',    //固定参数，金额
        'appPath'   => 'jumpUrl',     //response app/H5 跳转
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',     //获取二维码失败的错误描述字段
    ],
    //嘉联
    'Jialianpay'       => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'codeUrl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //源支付
    'Yuanpay'          => [
        'order'     => 'business_order',  //固定参数，订单号
        'amount'    => 'flow_balance',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'errorCode' => 'error',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //海贝
    'Haibeipay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //柒柒
    'Qiqipay'          => [
        'order'     => 'OrderNo',  //固定参数，订单号
        'amount'    => 'Amount',  //固定参数，金额
        'qrPath'    => 'Qrcode',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //汇通时代
    'Huitongshidaipay' => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'fee',  //固定参数，金额
        'qrPath'    => 'url_img',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'cpparam',     //获取二维码失败的错误描述字段
    ],
    //萌宝
    'Mengbaopay'       => [
        'order'     => '',  //固定参数，订单号
        'amount'    => '',  //固定参数，金额
        'qrPath'    => '',      //response二维码key
        'appPath'   => 'qrcodeurl',//response app/H5 跳转
        'errorCode' => '',    //获取二维码失败的状态吗字段
        'errorMsg'  => '',     //获取二维码失败的错误描述字段
    ],
    //融富
    'Rongfupay'        => [
        'order'     => 'orderNum',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //荔滔博
    'Litaobopay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respDesc',     //获取二维码失败的错误描述字段
    ],
    //苹果
    'Applepay'         => [
        'order'     => '',  //固定参数，订单号
        'amount'    => '',  //固定参数，金额
        'qrPath'    => '',      //response二维码key
        'appPath'   => 'qrcodeurl',//response app/H5 跳转
        'errorCode' => '',    //获取二维码失败的状态吗字段
        'errorMsg'  => '',     //获取二维码失败的错误描述字段
    ],
    // 仟付
    'Qianfupay'        => [
        'order'     => 'businessOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'success',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respDesc',     //获取二维码失败的错误描述字段
    ],
    //新天付
    'Xintianpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'pay_params',      //response二维码key
        'appPath'   => '',      //response二维码key
        'errorCode' => 'respcd',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',     //获取二维码失败的错误描述字段
    ],
    //AI付
    'AIpay'            => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'code_url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'result_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'result_msg',     //获取二维码失败的错误描述字段
    ],
    //普京
    'Pujingpay'        => [
        'order'     => 'orderNumber',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'data',      //response二维码key
        'appPath'   => '',      //response二维码key
        'errorCode' => 'success',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //聚付
    'Jufupay'          => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => '',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //唐支付
    'Tangpay'          => [
        'order'     => 'Merorderno',  //固定参数，订单号
        'amount'    => 'Toamount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'Code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'description',     //获取二维码失败的错误描述字段
    ],
    //云彩
    'Yuncaipay'        => [
        'order'     => 'merchantOrderno',  //固定参数，订单号
        'amount'    => 'requestAmount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //微支联
    'Weizlpay'         => [
        'order'     => 'tradeSn',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'code_url',      //response二维码key
        'appPath'   => 'qrcodeurl',      //response二维码key
        'errorCode' => 'resultCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //聚合浦发
    'Jhpfpay'          => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'pay_info',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //汇付时代
    'Htxxpay'          => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'payAmt',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    //三盛
    'Sanshengpay'      => [
        'order'     => '',  //固定参数，订单号
        'amount'    => '',  //固定参数，金额
        'qrPath'    => '',      //response二维码key
        'appPath'   => 'pay_params',      //response二维码key
        'errorCode' => '',    //获取二维码失败的状态吗字段
        'errorMsg'  => '',     //获取二维码失败的错误描述字段
    ],
    //豆付
    'Doupay'           => [
        'order'     => 'outOrderId',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'pay_params',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //众汇通
    'Zhonghtpay'       => [
        'order'     => 'corp_flow_no',  //固定参数，订单号
        'amount'    => 'totalAmount',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'qrcode',      //response二维码key
        'errorCode' => 'Code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'Msg',     //获取二维码失败的错误描述字段
    ],
    //易迅捷
    'Yixunjpay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'payAmt',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    //钱多多
    'Qianduoduopay'    => [
        'order'     => 'pay_OrderNo',  //固定参数，订单号
        'amount'    => 'pay_Amount',  //固定参数，金额
        'qrPath'    => 'pay_Code',      //response二维码key
        'appPath'   => 'pay_Code',      //response二维码key
        'errorCode' => 'pay_Status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'pay_CodeMsg',     //获取二维码失败的错误描述字段
    ],
    //诚优
    'Chengyupay'       => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => 'pay_url',      //response二维码key
        'errorCode' => 'data',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //银利宝
    'Yinlibaopay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'pay_url',      //response二维码key
        'errorCode' => 'host_id',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //码闪付
    'Mashanpay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'data',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //春荷支付
    'Chunhepay'        => [
        'order'     => 'order_number',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'data',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //银号
    'Yinhaopay'        => [
        'order'     => 'assPayOrderNo',  //固定参数，订单号
        'amount'    => 'assPayMoney',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //现代
    'Xiandaipay'       => [
        'order'     => 'field048',  //固定参数，订单号
        'amount'    => 'field004',  //固定参数，金额
        'qrPath'    => 'field055',      //response二维码key
        'appPath'   => 'field055',      //response二维码key
        'errorCode' => 'field039',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'field124',     //获取二维码失败的错误描述字段
    ],
    //稳稳付
    'Wenwenfupay'      => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payCode',      //response二维码key
        'appPath'   => 'payCode',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errCodeDes',     //获取二维码失败的错误描述字段
    ],
    //新顺畅
    'Xinshunchangpay'  => [
        'order'     => 'down_trade_no',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'qrcode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Shengyaopay'      => [
        'order'     => 'BusinessOrders',  //固定参数，订单号
        'amount'    => 'Amount',  //固定参数，金额
        'qrPath'    => 'data',      //response二维码key
        'appPath'   => 'data',      //response二维码key
        'errorCode' => 'Code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'Msg',     //获取二维码失败的错误描述字段
    ],
    //鑫发支付
    'XinFaZhiFuPay'    => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcodeUrl',      //response二维码key
        'appPath'   => 'qrcodeUrl',      //response二维码key
        'errorCode' => 'stateCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //隆发付
    'Longfafupay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcodeUrl',      //response二维码key
        'appPath'   => 'qrcodeUrl',      //response二维码key
        'errorCode' => 'stateCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //聚前支付
    'Juqianpay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 's_code_url',      //response二维码key
        'appPath'   => 'code_url',      //response手机端h5链接key
        'errorCode' => 'return_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'return_msg',     //获取二维码失败的错误描述字段
    ],
    //利信
    'Lixinpay'         => [
        'order'     => 'tradeNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payCode',      //response二维码key
        'appPath'   => 'payCode',      //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //微信原生
    'Wxyuanspay'       => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnamt',  //固定参数，金额
        'qrPath'    => 'formaction',      //response二维码key
        'appPath'   => 'formaction',      //response手机端h5链接key
        'errorCode' => 'respcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',     //获取二维码失败的错误描述字段
    ],
    //686支付
    'Liubaliupay'      => [
        'order'     => 'outOrderNo',  //固定参数，订单号
        'amount'    => 'tradeAmount',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //聚合支付
    'Yihuipay'         => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_amount',  //固定参数，金额
        'qrPath'    => 'qr_code',      //response二维码key
        'appPath'   => 'qr_code',      //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //黑豹支付
    'Heibaopay'        => [
        'order'     => 'orderNumber',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'data',      //response二维码key
        //'appPath'   => 'data',      //response手机端h5链接key
        'errorCode' => 'success',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //佰钱付
    'BaiQianFuPay'     => [
        'order'     => 'X2_BillNo',  //固定参数，订单号
        'amount'    => 'X1_Amount',  //固定参数，金额
        'qrPath'    => 'imgUrl',      //response二维码key
        //'appPath'   => 'data',      //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //闲鱼支付
    'Xianyuzfpay'      => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payParams',      //response二维码key
        //'appPath'   => 'data',      //response手机端h5链接key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errDes',     //获取二维码失败的错误描述字段
    ],
    //    //前海支付
    'Qianhaizfpay'     => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'totalMoney',  //固定参数，金额
        'qrPath'    => 'wxPayWay',      //response二维码key
        //'appPath'   => 'data',      //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //    //前海支付
    'Xinyizfpay'       => [
        'order'     => 'assPayOrderNo',  //固定参数，订单号
        'amount'    => 'assPayMoney',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        //'appPath'   => 'data',      //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //    //66支付
    'Sixzfpay'         => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        //'appPath'   => 'data',      //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],

    // 当当付
    'Dangdfupay'       => [
        'order'     => 'traceno',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'barCode',      //response二维码key
        //'appPath'   => 'data',      //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //起点支付
    'Qidianpay'        => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'payInfo',      //response二维码key
        //'appPath'   => 'data',      //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //益达支付
    'Yidapay'          => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'qrPath',      //response二维码key
        'appPath'   => 'qrPath',      //response手机端h5链接key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //全银支付
    'Quanyinpay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderPrice',  //固定参数，金额
        'qrPath'    => 'code_url',      //response二维码key
        //'appPath'   => 'code_url',      //response手机端h5链接key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //瑞信
    'Ruixinpay'        => [
        'order'     => 'assPayOrderNo',  //固定参数，订单号
        'amount'    => 'assPayMoney',    //固定参数，金额
        'qrPath'    => 'payUrl',         //response二维码key
        'appPath'   => 'payUrl',         //response手机端h5链接key
        'errorCode' => 'code',           //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',        //获取二维码失败的错误描述字段
    ],
    //轻支付
    'Qingzhifupay'     => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        //'appPath'   => 'code_url',      //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',     //获取二维码失败的错误描述字段
    ],
    //捷付通
    'Jiefutongpay'     => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        //'appPath'   => 'code_url',      //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',     //获取二维码失败的错误描述字段
    ],
    //亿易付
    'Yiyifupay2'       => [
        'order'     => 'tradeNo',  //固定参数，订单号
        'amount'    => 'totalAmount',  //固定参数，金额
        'qrPath'    => 'qr_code',      //response二维码key
        //'appPath'   => 'code_url',      //response手机端h5链接key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //网银付
    'Wangyinpay'       => [
        'order'     => 'assPayOrderNo',  //固定参数，订单号
        'amount'    => 'assPayMoney',    //固定参数，金额
        'qrPath'    => 'payUrl',         //response二维码key
        'appPath'   => 'payUrl',         //response手机端h5链接key
        'errorCode' => 'code',           //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',        //获取二维码失败的错误描述字段
    ],
    //万汇宝
    'Wanhuibaopay'     => [
        'order'     => 'mer_order_no',  //固定参数，订单号
        'amount'    => 'trade_amount',    //固定参数，金额
        'qrPath'    => 'trade_return_msg',         //response二维码key
        'appPath'   => 'trade_return_msg',         //response手机端h5链接key
        'errorCode' => 'auth_result',           //获取二维码失败的状态吗字段
        'errorMsg'  => 'error_msg',        //获取二维码失败的错误描述字段
    ],

    //科诺
    'Kenuopay'         => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'result',         //response二维码key
        'appPath'   => 'result',         //response手机端h5链接key
        'errorCode' => 'status',           //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',        //获取二维码失败的错误描述字段
    ],
    //易充
    'Yichongpay'       => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'price',    //固定参数，金额
        'qrPath'    => 'url',         //response二维码key
        'appPath'   => 'url',         //response手机端h5链接key
        'errorCode' => 'code',           //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',        //获取二维码失败的错误描述字段
    ],
    //科星
    'Kexingpay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'transAmt',    //固定参数，金额
        'qrPath'    => 'payQRCodeUrl',         //response二维码key
        //'appPath'   => 'payQRCodeUrl',         //response手机端h5链接key
        'errorCode' => 'respCode',           //获取二维码失败的状态吗字段
        'errorMsg'  => 'respDesc',        //获取二维码失败的错误描述字段
    ],
    //LZ支付
    'LZzhifupay'       => [
        'order'     => 'prdOrdNo',  //固定参数，订单号
        'amount'    => 'prdAmt',    //固定参数，金额
        'qrPath'    => 'qrcode',         //response二维码key
        'appPath'   => 'qrcode',         //response手机端h5链接key
        'errorCode' => 'retCode',           //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',        //获取二维码失败的错误描述字段
    ],
    //中诺支付
    'Zhongnuopay'      => [
        'order'     => 'pay_order_id',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'url',         //response二维码key
        'appPath'   => 'url',         //response手机端h5链接key
        'errorCode' => 'code',           //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',        //获取二维码失败的错误描述字段
    ],
    //丰捷支付
    'Fengjiepay'       => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'data',         //response二维码key
        'appPath'   => 'data',         //response手机端h5链接key
        'errorCode' => 'resp_code',           //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_msg',        //获取二维码失败的错误描述字段
    ],
    //顺易
    'Shunyipay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'payAmt',    //固定参数，金额
        'qrPath'    => 'payUrl',         //response二维码key
        'appPath'   => 'payUrl',         //response手机端h5链接key
        'errorCode' => 'retCode',           //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',        //获取二维码失败的错误描述字段
    ],
    //天瑞
    'Tianruipay'       => [
        'order'     => 'request_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'bank_url',  //response二维码key
        'appPath'   => 'bank_url',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //明书支付
    'Mingshupay'       => [
        'order'     => 'transactionId',  //固定参数，订单号
        'amount'    => 'orderAmount',    //固定参数，金额
        'qrPath'    => 'codeHtml',  //response二维码key
        'appPath'   => 'codeHtml',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //天河支付
    'Tianhezfpay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //聚合付
    'Juhefupay'        => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnamt',    //固定参数，金额
        'qrPath'    => 'formaction',  //response二维码key
        'appPath'   => 'formaction',  //response手机端h5链接key
        'errorCode' => 'respcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',    //获取二维码失败的错误描述字段
    ],
    //协鹭
    'Xielupay'         => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'code_url',  //response二维码key
        'appPath'   => 'code_url',  //response手机端h5链接key
        'errorCode' => 'return_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'return_msg',    //获取二维码失败的错误描述字段
    ],
    //顺付通支付
    'Shunfutongpay'    => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'returnCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'returnMsg',    //获取二维码失败的错误描述字段
    ],
    //马尼拉
    'Manilapay'        => [
        'order'     => 'order_number',  //固定参数，订单号
        'amount'    => 'qr_amount',    //固定参数，金额
        'qrPath'    => 'qr_code',  //response二维码key
        'appPath'   => 'qr_code',  //response手机端h5链接key
        'errorCode' => 'ret',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],

    //银盛通
    'Yinshengtongpay'  => [
        'order'     => 'order_number',  //固定参数，订单号
        'amount'    => 'qr_amount',    //固定参数，金额
        'qrPath'    => 'qr_code',  //response二维码key
        'appPath'   => 'qr_code',  //response手机端h5链接key
        'errorCode' => 'ret',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //E卡通
    'Ekatongpay'       => [
        'order'     => 'out_order_no',  //固定参数，订单号
        'amount'    => 'total_money',    //固定参数，金额
        'qrPath'    => 'qr_code',  //response二维码key
        'appPath'   => 'qr_code',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //旺旺支付
    'Wangwangpay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmt',    //固定参数，金额
        'qrPath'    => 'jumpUrl',  //response二维码key
        'appPath'   => 'jumpUrl',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],
    //绿洲通道
    'Luzhoutdpay'      => [
        'order'     => 'prdOrdNo',  //固定参数，订单号
        'amount'    => 'orderAmount',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        //'appPath'   => 'htmlText',  //response手机端h5链接key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',    //获取二维码失败的错误描述字段
    ],
    //集创支付
    'Jichuangzfpay'    => [
        'order'     => 'tradeno',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'retcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //钱多多IWC
    'Qianddiwcpay'     => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',    //获取二维码失败的错误描述字段
    ],
    //悠悠支付v1.0
    'Youyouvpay'       => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',    //获取二维码失败的错误描述字段
    ],
    //e支付
    'Epay'             => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'err',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //蜂巢
    'Fengchaopay'      => [
        'order'     => 'order_user',  //固定参数，订单号
        'amount'    => 'money_order',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',    //获取二维码失败的错误描述字段
    ],
    //博富通
    'Bofutongpay'      => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'bankUrl',  //response二维码key
        'appPath'   => 'bankUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //360支付
    'Sanliulingpay'    => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmt',    //固定参数，金额
        'qrPath'    => 'jumpUrl',  //response二维码key
        'appPath'   => 'jumpUrl',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],
    //超凡闪付
    'Chaofanshanfupay' => [
        'order'     => 'mchntOrderId',  //固定参数，订单号
        'amount'    => 'txnAmt',    //固定参数，金额
        'qrPath'    => 'imgUrl',  //response二维码key
        'appPath'   => 'imgUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //神州支付
    'Shenzhoupay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'code_url',  //response二维码key
        'appPath'   => 'code_url',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //仟淼支付
    'Qianmiaopay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'code_url',  //response二维码key
        'appPath'   => 'code_url',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //快付通支付
    'Kuaifutongpay'    => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'transAmt',    //固定参数，金额
        'qrPath'    => 'ret_msg',  //response二维码key
        'appPath'   => 'ret_msg',  //response手机端h5链接key
        'errorCode' => 'ret_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'ret_msg',    //获取二维码失败的错误描述字段
    ],
    //ABC支付
    'Abczfpay'         => [
        'order'     => 'tenantOrderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'status',    //获取二维码失败的错误描述字段
    ],
    //云宝
    'Yunbaozfpay'      => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'price',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //金商贵
    'Jinshanguipay'    => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qcodeUrl',  //response二维码key
        'appPath'   => 'qcodeUrl',  //response手机端h5链接key
        'errorCode' => 'rescode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resmsg',    //获取二维码失败的错误描述字段
    ],
    //飞龙金服
    'Feilongpay'       => [
        'order'     => 'userOrderId',  //固定参数，订单号
        'amount'    => 'fee',    //固定参数，金额
        'qrPath'    => 'param',  //response二维码key
        'appPath'   => 'param',  //response手机端h5链接key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //金顺支付
    'Jinshunzfpay'     => [
        'order'     => 'merchantOrderId',  //固定参数，订单号
        'amount'    => 'merchantOrderAmt',    //固定参数，金额
        'qrPath'    => 'codeUrl',  //response二维码key
        'appPath'   => 'codeUrl',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respDesc',    //获取二维码失败的错误描述字段
    ],
    //万家支付
    'Wanjiapay'        => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'price',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //八度支付
    'Baduzfpay'        => [
        'order'     => 'sdorderno',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'qrcodeurl',  //response二维码key
        'appPath'   => 'qrcodeurl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //嘉信
    'Jiaxinpay'        => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'mark',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //战蓝支付
    'Zhanlanzfpay'     => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'data',  //response二维码key
        'appPath'   => 'data',  //response手机端h5链接key
        'errorCode' => 'resp_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_msg',    //获取二维码失败的错误描述字段
    ],
    //1000支付
    'Yiqianzfpay'      => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',    //获取二维码失败的错误描述字段
    ],
    //蜘蛛付v1
    'Zhizhufuv1pay'    => [
        'order'     => 'thirdOrderID',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrcodeUrl',  //response二维码key
        'appPath'   => 'qrcodeContent',  //response手机端h5链接key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'result',    //获取二维码失败的错误描述字段
    ],
    //环球支付
    'Huanqiupay'       => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnamt',    //固定参数，金额
        'qrPath'    => 'formaction',  //response二维码key
        'appPath'   => 'formaction',  //response手机端h5链接key
        'errorCode' => 'respcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',    //获取二维码失败的错误描述字段
    ],
    //boss付
    'Bosspay'          => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //钱融支付
    'Qianrongpay'      => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'price',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'returncode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'returnmsg',    //获取二维码失败的错误描述字段
    ],
    //得到支付
    'Dedaopay'         => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'result_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'result_msg',    //获取二维码失败的错误描述字段
    ],
    //亿收支付
    'Yishoupay'        => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'txnAmt',    //固定参数，金额
        'qrPath'    => 'payInfo',  //response二维码key
        'appPath'   => 'payInfo',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],
    //小马支付
    'Xiaomapay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //现金宝
    'Xianjinbaopay'    => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'code_url',  //response二维码key
        'appPath'   => 'prepay_url',  //response手机端h5链接key
        'errorCode' => 'return_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'return_msg',    //获取二维码失败的错误描述字段
    ],
    //宏达支付
    'Hongdazfpay'      => [
        'order'     => 'out_order_id',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'error',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //金流
    'Jinliupay'        => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnamt',    //固定参数，金额
        'qrPath'    => 'formaction',  //response二维码key
        'appPath'   => 'formaction',  //response手机端h5链接key
        'errorCode' => 'respcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',    //获取二维码失败的错误描述字段
    ],
    //快宝支付
    'Kuaibaopay'       => [
        'order'     => 'tradeNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'img',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //易联通
    'Yiliantongpay'    => [
        'order'     => 'platOrderId',  //固定参数，订单号
        'amount'    => 'amt',    //固定参数，金额
        'qrPath'    => 'codeUrl',  //response二维码key
        'appPath'   => 'shortUrl',  //response手机端h5链接key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',    //获取二维码失败的错误描述字段
    ],
    //M米汇
    'Mihuipay'         => [
        'order'     => 'sdorderno',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //晨星
    'Chenxingpay'      => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'qrcode_url',  //response二维码key
        'appPath'   => 'qrcode_url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //华夏支付
    'Huaxiazfpay'      => [
        'order'     => 'record',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'image',  //response二维码key
        'appPath'   => 'image',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //易点支付
    'Yidianpay'        => [
        'order'     => 'edddh',  //固定参数，订单号
        'amount'    => 'edfee',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',    //获取二维码失败的错误描述字段
    ],
    //AP支付
    'APzfpay'          => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'error_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error_msg',    //获取二维码失败的错误描述字段
    ],
    //益龙支付
    'Yilongpay'        => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'return_payurl',  //response二维码key
        'appPath'   => 'return_payurl',  //response手机端h5链接key
        'errorCode' => 'string',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',           //获取二维码失败的错误描述字段
    ],
    //新顺畅V1.0
    'Xinshunchangvpay' => [
        'order'     => 'organizationOrderCode',  //固定参数，订单号
        'amount'    => 'payment',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',           //获取二维码失败的错误描述字段
    ],
    //广信支付
    'Guangxinpay'      => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'value',  //response二维码key
        'appPath'   => 'value',  //response手机端h5链接key
        'errorCode' => 'errorCode',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',           //获取二维码失败的错误描述字段
    ],
    //通兴支付
    'Tongxingzfpay'    => [
        'order'     => 'sdorderno',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'qrcodeurl',  //response二维码key
        'appPath'   => 'qrcodeurl',  //response手机端h5链接key
        'errorCode' => 'code',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',           //获取二维码失败的错误描述字段
    ],
    //牛购商付
    'Niugouspay'       => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'image',  //response二维码key
        'appPath'   => 'image',  //response手机端h5链接key
        'errorCode' => 'status',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',           //获取二维码失败的错误描述字段
    ],
    //帝王支付
    'Diwangpay'        => [
        'order'     => 'TxSN',  //固定参数，订单号
        'amount'    => 'Amount',    //固定参数，金额
        'qrPath'    => 'ImgUrl',  //response二维码key
        'appPath'   => 'ImgUrl',  //response手机端h5链接key
        'errorCode' => 'RspCod',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'RspMsg',           //获取二维码失败的错误描述字段
    ],
    //亿汇宝
    'Yihuibaopay'      => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'qr_code_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'status',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',           //获取二维码失败的错误描述字段
    ],
    //快捷付
    'Kuaijiepay'       => [
        'order'     => 'tradeNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payCode',  //response二维码key
        'appPath'   => 'payCode',  //response手机端h5链接key
        'errorCode' => 'status',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',           //获取二维码失败的错误描述字段
    ],
    //888支付
    'Bababapay'        => [
        'order'     => 'thirdOrderID',  //固定参数，订单号
        'amount'    => 'price',    //固定参数，金额
        'qrPath'    => 'qrcodeContent',  //response二维码key
        'appPath'   => 'qrcodeContent',  //response手机端h5链接key
        'errorCode' => 'result',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'result',           //获取二维码失败的错误描述字段
    ],
    //众付支付
    'Zhongfupay'       => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'total_amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'status',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',           //获取二维码失败的错误描述字段
    ],
    //比特支付
    'Bitezfpay'        => [
        'order'     => 'oid',  //固定参数，订单号
        'amount'    => 'amt',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'error_code',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'error_msg',           //获取二维码失败的错误描述字段
    ],
    //微富支付
    'Weifupay'         => [
        'order'     => 'OrderId',  //固定参数，订单号
        'amount'    => 'Amount',    //固定参数，金额
        'qrPath'    => 'redirectURL',  //response二维码key
        'appPath'   => 'redirectURL',  //response手机端h5链接key
        'errorCode' => 'err_code',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'err_msg',           //获取二维码失败的错误描述字段
    ],
    //亿吉当面
    'Yijidmpay'        => [
        'order'     => 'ordernumber',  //固定参数，订单号
        'amount'    => 'paymoney',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'status',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',           //获取二维码失败的错误描述字段
    ],
    //快易付
    'Kuaiyipay'        => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payJumpUrl',  //response二维码key
        'appPath'   => 'payJumpUrl',  //response手机端h5链接key
        'errorCode' => 'retCode',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',           //获取二维码失败的错误描述字段
    ],
    //小星星
    'Xiaoxingxpay'     => [
        'order'     => 'data',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'state',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'text',           //获取二维码失败的错误描述字段
    ],
    //个码通
    'Gemapay'          => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'code',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',           //获取二维码失败的错误描述字段
    ],
    //汇付支付
    'Huifupay'         => [
        'order'     => 'tradeId',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'qrUrl',  //response二维码key
        'appPath'   => 'qrUrl',  //response手机端h5链接key
        'errorCode' => 'respSts',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',           //获取二维码失败的错误描述字段
    ],
    //讯科支付
    'Xunkepay'         => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'value',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'code',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',           //获取二维码失败的错误描述字段
    ],
    //美联
    'Meilianpay'       => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnAmt',    //固定参数，金额
        'qrPath'    => 'codeUrl',  //response二维码key
        'appPath'   => 'codeUrl',  //response手机端h5链接key
        'errorCode' => 'resultCode',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'resultMsg',           //获取二维码失败的错误描述字段
    ],
    //云支付perCo
    'Yunpay'           => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'totalFee',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'status',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',           //获取二维码失败的错误描述字段
    ],
    //速客
    'Shukepay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'code',        //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',           //获取二维码失败的错误描述字段
    ],
    //摇钱树
    'Yaoqianshupay'    => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'code_url',  //response二维码key
        'appPath'   => 'code_url',  //response手机端h5链接key
        'errorCode' => 'res_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'res_msg',    //获取二维码失败的错误描述字段
    ],
    //世联付
    'Shilianpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'result_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'result_msg',    //获取二维码失败的错误描述字段
    ],
    //恒润支付
    'Hengrunpay'       => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'totalAmount',    //固定参数，金额
        'qrPath'    => 'payURL',  //response二维码key
        'appPath'   => 'payURL',  //response手机端h5链接key
        'errorCode' => 'stateCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'stateInfo',    //获取二维码失败的错误描述字段
    ],
    //东方支付
    'Dongfangpay'      => [
        'order'     => 'record',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'image',  //response二维码key
        'appPath'   => 'image',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //极付支付
    'Jifupay'          => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderPrice',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //万联支付
    'Wanlianzfpay'     => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'data',  //response二维码key
        'appPath'   => 'data',  //response手机端h5链接key
        'errorCode' => 'resp_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_msg',    //获取二维码失败的错误描述字段
    ],
    //冠宝支付
    'Guanbaozfpay'     => [
        'order'     => 'orderIdCp',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'data',  //response二维码key
        'appPath'   => 'data',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //快富平
    'Kuaifupingpay'    => [
        'order'     => 'Merorderno',  //固定参数，订单号
        'amount'    => 'Toamount',    //固定参数，金额
        'qrPath'    => 'result',  //response二维码key
        'appPath'   => 'result',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'description',    //获取二维码失败的错误描述字段
    ],
    //快支付3.1
    'Kuaitreepay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderPrice',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //青果畅付
    'Qingguocpay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total',    //固定参数，金额
        'qrPath'    => 'code_url',  //response二维码key
        'appPath'   => 'code_url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //亿鑫支付
    'Yixinpay'         => [
        'order'     => 'orderIdCp',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'data',  //response二维码key
        'appPath'   => 'data',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //金通
    'Jintongpay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'payAmt',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',    //获取二维码失败的错误描述字段
    ],
    //新汇通支付
    'Huitongpay'       => [
        'order'     => 'merchantOrderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //新版亿付宝
    'Yifubaopay'       => [
        'order'     => 'mchntOrderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',    //固定参数，金额
        'qrPath'    => 'codeUrl',  //response二维码key
        'appPath'   => 'codeUrl',  //response手机端h5链接key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',    //获取二维码失败的错误描述字段
    ],
    //金猫
    // 'Jinmaopay'     => [
    //     'order'     => 'merchant_order_no',  //固定参数，订单号
    //     'amount'    => 'trade_amount',    //固定参数，金额
    //     'qrPath'    => 'pay_url',  //response二维码key
    //     'appPath'   => 'pay_url',  //response手机端h5链接key
    //     'errorCode' => 'status',    //获取二维码失败的状态吗字段
    //     'errorMsg'  => 'info',    //获取二维码失败的错误描述字段
    // ],
    //口碑支付
    'Koubeipay'        => [
        'order'     => 'traceno',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'barCode',  //response二维码key
        'appPath'   => 'barCode',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //鼎融支付
    'Dingrongpay'      => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],

    //玖付
    'Jiupay'           => [
        'order'     => 'AGTORDID',  //固定参数，订单号
        'amount'    => 'PARVALUE',    //固定参数，金额
        'qrPath'    => 'PAYURL',  //response二维码key
        'appPath'   => 'PAYURL',  //response手机端h5链接key
        'errorCode' => 'STATUS',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'TRDERRCODE',    //获取二维码失败的错误描述字段
    ],

    //乐付无限
    'Lefuwxpay'        => [
        'order'     => 'orderIdCp',  //固定参数，订单号
        'amount'    => 'fee',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],

    //玖付
    'Yiqufupay'        => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'totalFee',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    // 久通
    'Jiutong'          => [
        'order'     => 'orderNum',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrcodeUrl',  //response二维码key
        'appPath'   => 'qrcodeUrl',  //response手机端h5链接key
        'errorCode' => 'stateCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //云聚支付
    'Yunjupay'         => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'code_url',  //response二维码key
        'appPath'   => 'code_url',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //天马支付
    'Tianmapay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_amount',    //固定参数，金额
        'qrPath'    => 'qrcode_url',  //response二维码key
        'appPath'   => 'qrcode_url',  //response手机端h5链接key
        'errorCode' => 'resultCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',    //获取二维码失败的错误描述字段
    ],
    //支付狗
    'Zhifugoupay'      => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //龙腾支付
    'Longtengpay'      => [
        'order'     => 'order_down',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'pay_qrcode',  //response二维码key
        'appPath'   => 'pay_qrcode',  //response手机端h5链接key
        'errorCode' => 'rst_status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'rst_msg',    //获取二维码失败的错误描述字段
    ],
    //支付狗
    'Hetongpay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'qrcode_url',  //response二维码key
        'appPath'   => 'qrcode_url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //金云支付
    'Jinyunpay'        => [
        'order'     => 'assPayOrderNo',  //固定参数，订单号
        'amount'    => 'assPayMoney',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //喜来付支付
    'Xilaipay'         => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'pay_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'pay_msg',    //获取二维码失败的错误描述字段
    ],
    //新众付
    'Xinzfupay'        => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'total_amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //E呗支付
    'Ebeizfpay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //恒润付
    'Hengrunfupay'     => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'totalAmount',    //固定参数，金额
        'qrPath'    => 'payURL',  //response二维码key
        'appPath'   => 'payURL',  //response手机端h5链接key
        'errorCode' => 'stateCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'stateInfo',    //获取二维码失败的错误描述字段
    ],
    //一付通
    'Yifutongzfpay'    => [
        'order'     => 'outOrderId',  //固定参数，订单号
        'amount'    => 'transAmt',    //固定参数，金额
        'qrPath'    => 'payCode',  //response二维码key
        'appPath'   => 'payCode',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],
    //顺优付
    'Shunyoufupay'     => [
        'order'     => 'mer_order_no',  //固定参数，订单号
        'amount'    => 'trade_amount',    //固定参数，金额
        'qrPath'    => 'payInfo',  //response二维码key
        'appPath'   => 'payInfo',  //response手机端h5链接key
        'errorCode' => 'errorCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errorMsg',    //获取二维码失败的错误描述字段
    ],
    //诚智付
    'Chengzhifupay'    => [
        'order'     => 'mchntOrderId',  //固定参数，订单号
        'amount'    => 'txnAmt',    //固定参数，金额
        'qrPath'    => 'imgUrl',  //response二维码key
        'appPath'   => 'imgUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //永利
    'Yonglipay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'pay_params',  //response二维码key
        'appPath'   => 'pay_params',  //response手机端h5链接key
        'errorCode' => 'respcd',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',    //获取二维码失败的错误描述字段
    ],
    //捷付支付
    'Jiefuzfpay'       => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //捷付支付
    'Weifubaopay'      => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',    //获取二维码失败的错误描述字段
    ],
    //一加支付
    'Yijiapay'         => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //畅通支付
    'Changtongzfpay'   => [
        'order'     => 'organizationOrderCode',  //固定参数，订单号
        'amount'    => 'orderPrice',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //88支付
    'Babazfpay'        => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'pcQrcode',  //response二维码key
        'appPath'   => 'h5Qrcode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //蚂蚁数据
    'Mayisjpay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'totalAmount',    //固定参数，金额
        'qrPath'    => 'htmlUrl',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //信宏支付
    'Xinhongpay'       => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'pay_info',  //response二维码key
        'appPath'   => 'pay_info',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //蓉银
    'Rongyingpay'      => [
        'order'     => 'outOrderNo',  //固定参数，订单号
        'amount'    => 'tradeAmount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'payState',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    //盈利
    'Yinglipay'        => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'txcode',  //response二维码key
        'appPath'   => 'txcode',  //response手机端h5链接key
        'errorCode' => 'returncode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'txmsg',    //获取二维码失败的错误描述字段
    ],
    //速通
    'Sutongpay'        => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',    //固定参数，金额
        'qrPath'    => 'qrurl',  //response二维码key
        'appPath'   => 'qrurl',  //response手机端h5链接key
        'errorCode' => 'state',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    //997支付
    'Jjqpay'           => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'retHtml',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    //喜多多支付
    'Xiduoduopay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'payAmount',  //固定参数，金额
        'qrPath'    => '',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //龙信
    'Longxinpay'       => [
        'order'     => 'item',  //固定参数，订单号
        'amount'    => 'fee',  //固定参数，金额
        'qrPath'    => '',      //response二维码key
        'appPath'   => 'param',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //888支付v1
    'Babbapay'         => [
        'order'     => 'item',  //固定参数，订单号
        'amount'    => 'fee',  //固定参数，金额
        'qrPath'    => '',      //response二维码key
        'appPath'   => 'param',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //星航支付
    'Xinghpay'         => [
        'order'     => 'ordernumber',  //固定参数，订单号
        'amount'    => 'paymoney',  //固定参数，金额
        'qrPath'    => 'qrurl',      //response二维码key
        'appPath'   => 'qrurl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //快联支付
    'Kuailianpay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //金尊支付
    'Jinzunpay'        => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    //千万家
    'Qianwanjiapay'    => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'payAmt',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    //钉钉支付
    'Dingdingpay'      => [
        'order'     => 'requestNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //lol支付
    'Lolpay'           => [
        'order'     => 'orderNO',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => '',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //易博
    'Yibopay'          => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => '',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //启通宝
    'Qitongbaopay'     => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMessage',     //获取二维码失败的错误描述字段
    ],
    //百盛
    'Baishenpay'       => [
        'order'     => 'OutPaymentNo',  //固定参数，订单号
        'amount'    => 'PaymentAmount',  //固定参数，金额
        'qrPath'    => 'QrCodeUrl',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'Code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'Message',     //获取二维码失败的错误描述字段
    ],
    //新鼎盛
    'Newdingshengpay'  => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmt',  //固定参数，金额
        'qrPath'    => 'jumpUrl',      //response二维码key
        'appPath'   => 'jumpUrl',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',     //获取二维码失败的错误描述字段
    ],
    //亿鑫支付V1.0
    'Yixinvpay'        => [
        'order'     => 'mchntOrderId',  //固定参数，订单号
        'amount'    => 'txnAmt',  //固定参数，金额
        'qrPath'    => 'imgUrl',      //response二维码key
        'appPath'   => 'imgUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //沙雕支付
    'Sbzfpay'          => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmt',  //固定参数，金额
        'qrPath'    => 'jumpUrl',      //response二维码key
        'appPath'   => 'jumpUrl',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',     //获取二维码失败的错误描述字段
    ],
    //聚合精英
    'Juhejypay'        => [
        'order'     => 'MerOrderNo',  //固定参数，订单号
        'amount'    => 'Amount',  //固定参数，金额
        'qrPath'    => 'PayUrl',      //response二维码key
        'appPath'   => 'PayUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    //壹加付
    'Yzfpay'           => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'returncode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'returnmsg',     //获取二维码失败的错误描述字段
    ],
    // 精英支付
    'ingyingzfpay'     => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    // 亿鑫V2.0
    'Yxvpay'           => [
        'order'     => 'mchntOrderId',  //固定参数，订单号
        'amount'    => 'txnAmt',  //固定参数，金额
        'qrPath'    => 'imgUrl',      //response二维码key
        'appPath'   => 'imgUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    // OK付V1.0
    'Okbpay'           => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'state',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    // 汇付
    'Huipay'           => [
        'order'     => 'orderCode',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'qrcode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    // 蜜蜂支付V1.0
    'Mifengvpay'       => [
        'order'     => 'merchant_order_sn',  //固定参数，订单号
        'amount'    => 'total',  //固定参数，金额
        'qrPath'    => 'qr_code',      //response二维码key
        'appPath'   => 'qr_code',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    // 开元聚合
    'Kaiyuanjuhepay'   => [
        'order'     => 'businessnumber',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'trade_qrcode',      //response二维码key
        'appPath'   => 'trade_qrcode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    // 新守信支付
    'Xinshouxinpay'    => [
        'order'     => 'userOrderId',  //固定参数，订单号
        'amount'    => 'fee',  //固定参数，金额
        'qrPath'    => 'param',      //response二维码key
        'appPath'   => 'param',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    // 新北极熊
    'Newnorthbearpay'  => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'value',      //response二维码key
        'appPath'   => 'value',      //response二维码key
        'errorCode' => 'errorCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    // 汇捷通
    'Huijietongpay'    => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',     //获取二维码失败的错误描述字段
    ],
    // 随付
    'Weisutpay'        => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'payURL',      //response二维码key
        'appPath'   => 'payURL',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Junanpay'         => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => 'pay_url',      //response二维码key
        'appPath'   => 'pay_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Feimaipay'        => [
        'order'     => 'transactionId',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'orderUrl',      //response二维码key
        'appPath'   => 'orderUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Jichuangpay'      => [
        'order'     => 'tradeno',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'retcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Wanzhifupay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'pay_url',      //response二维码key
        'appPath'   => 'qrcode',      //response二维码key
        'errorCode' => 'result_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'result_msg',     //获取二维码失败的错误描述字段
    ],
    'Hetpay'           => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'qrcode_url',      //response二维码key
        'appPath'   => 'qrcode_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Chenyzpay'        => [
        'order'     => 'p4_orderno',  //固定参数，订单号
        'amount'    => 'p3_paymoney',  //固定参数，金额
        'qrPath'    => 'r6_qrcode',      //response二维码key
        'appPath'   => 'r6_qrcode',      //response二维码key
        'errorCode' => 'rspCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'rspMsg',     //获取二维码失败的错误描述字段
    ],
    'Tongbaozpay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_amount',  //固定参数，金额
        'qrPath'    => 'qr_code',      //response二维码key
        'appPath'   => 'qr_code',      //response二维码key
        'errorCode' => 'resultCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    'Tongyinfpay'      => [
        'order'     => 'requestId',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'key',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Gaoshengpay'      => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'totalAmount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'resultCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resultMsg',     //获取二维码失败的错误描述字段
    ],
    'Jinfafupay'       => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'qrcode_url',      //response二维码key
        'appPath'   => 'qrcode_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Caifbvepay'       => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Qtyfpay'          => [
        'order'     => 'p2_Order',  //固定参数，订单号
        'amount'    => 'p3_Amt',  //固定参数，金额
        'qrPath'    => 'payImg',      //response二维码key
        'appPath'   => 'payImg',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Quanqiutfpay'     => [
        'order'     => 'businessOrderId',  //固定参数，订单号
        'amount'    => 'tradeMoney',  //固定参数，金额
        'qrPath'    => 'codeurl',      //response二维码key
        'appPath'   => 'codeurl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Goldbpay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'code_url',      //response二维码key
        'appPath'   => 'code_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Expay'            => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'orderPrice',  //固定参数，金额
        'qrPath'    => 'payMessage',      //response二维码key
        'appPath'   => 'payMessage',      //response二维码key
        'errorCode' => 'resultCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    'Huobanzfpay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'code_url',      //response二维码key
        'appPath'   => 'prepay_url',      //response二维码key
        'errorCode' => 'result_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'err_code_des',     //获取二维码失败的错误描述字段
    ],
    'Firefoxpay'       => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => 'pay_url',      //response二维码key
        'appPath'   => 'pay_url',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Yitongdapay'      => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'payMoney',  //固定参数，金额
        'qrPath'    => 'prepayUrl',      //response二维码key
        'appPath'   => 'qrCodeUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    'Yutongpay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'transAmt',  //固定参数，金额
        'qrPath'    => 'payQRCode',      //response二维码key
        'appPath'   => 'payInfo',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respDesc',     //获取二维码失败的错误描述字段
    ],
    'Qianmaipay'       => [
        'order'     => 'assPayOrderNo',  //固定参数，订单号
        'amount'    => 'assPayMoney',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Cgpay'            => [
        'order'     => 'MerchantOrderId',  //固定参数，订单号
        'amount'    => 'Amount',  //固定参数，金额
        'qrPath'    => 'Qrcode',      //response二维码key
        'appPath'   => 'Qrcode',      //response二维码key
        'errorCode' => 'ReturnCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'RetrunMessage',     //获取二维码失败的错误描述字段
    ],
    'Aazfpay'          => [
        'order'     => 'aa_order_no',  //固定参数，订单号
        'amount'    => 'aa_amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Weifuzpay'        => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'qrcode',      //response二维码key
        'errorCode' => 'errorCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Hefutongpay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'success',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Yidazvpay'        => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'codeUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Niunbypay'        => [
        'order'     => 'order_sn',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'http_url',      //response二维码key
        'appPath'   => 'http_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Kayoufupay'       => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payCode',      //response二维码key
        'appPath'   => 'payCode',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errCodeDes',     //获取二维码失败的错误描述字段
    ],
    'Lionzfpay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amt',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Boluomipay'       => [
        'order'     => 'mch_number',  //固定参数，订单号
        'amount'    => 'total',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errors',     //获取二维码失败的错误描述字段
    ],
    'Yunruipay'        => [
        'order'     => 'request_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'bank_url',      //response二维码key
        'appPath'   => 'bank_url',      //response二维码key
        'errorCode' => 'success',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Xinzhifupay'      => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'totalFee',  //固定参数，金额
        'qrPath'    => 'codeUrl',      //response二维码key
        'appPath'   => 'redirectUrl',      //response二维码key
        'errorCode' => 'returnCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errCodeDes',     //获取二维码失败的错误描述字段
    ],
    'Zhonglianzfpay'   => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'pay_url',      //response二维码key
        'errorCode' => 'result_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'result_msg',     //获取二维码失败的错误描述字段
    ],
    'Goldbvpay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'code_url',      //response二维码key
        'appPath'   => 'mweb_url',      //response二维码key
        'errorCode' => 'err_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'err_msg',     //获取二维码失败的错误描述字段
    ],
    'Xiaoniuzpay'      => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'redirect_url',      //response二维码key
        'appPath'   => 'redirect_url',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Xinmangopay'      => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',     //获取二维码失败的错误描述字段
    ],
    'Baolianpay'       => [
        'order'     => 'p4_orderno',  //固定参数，订单号
        'amount'    => 'p3_paymoney',  //固定参数，金额
        'qrPath'    => 'r6_qrcode',      //response二维码key
        'appPath'   => 'r6_qrcode',      //response二维码key
        'errorCode' => 'rspCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'rspMsg',     //获取二维码失败的错误描述字段
    ],
    'Huidzfpay'        => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'pay_url',      //response二维码key
        'appPath'   => 'pay_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Gstpay'           => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'qrCodeUrl',      //response二维码key
        'appPath'   => '',      //response二维码key
        'errorCode' => 'flag',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Sanliuyipay'      => [
        'order'     => 'order_number',  //固定参数，订单号
        'amount'    => 'payment',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => '',          //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Juyinzfpay'       => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => 'smsg',      //response二维码key
        'appPath'   => 'smsg',      //response二维码key
        'errorCode' => 'scode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'smsg',     //获取二维码失败的错误描述字段
    ],
    'Migopay'          => [
        'order'     => 'ubodingdan',  //固定参数，订单号
        'amount'    => 'ubomoney',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'msg',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errcode',     //获取二维码失败的错误描述字段
    ],
    'Shunjiezfpay'     => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'payAmt',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    'Suixinfupay'      => [
        'order'     => 'bizOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Bibaoxnbpay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'Code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'Message',     //获取二维码失败的错误描述字段
    ],
    'Newtjpay'         => [
        'order'     => 'orderno',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'pay_url',      //response二维码key
        'appPath'   => 'pay_url',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Huoshanzpay'      => [
        'order'     => 'outer_order_sn',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'pay_url',      //response二维码key
        'appPath'   => 'pay_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'actionErrors',     //获取二维码失败的错误描述字段
    ],
    'Jiubapay'         => [
        'order'     => 'mch_order_no',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'code_link',      //response二维码key
        'appPath'   => 'code_link',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Hengxinzfpay'     => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'codeUrl',      //response二维码key
        'appPath'   => 'codeUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    'Yidzfpay'         => [
        'order'     => 'orgOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',     //获取二维码失败的错误描述字段
    ],
    'Yflpay'           => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'codeUrl',      //response二维码key
        'appPath'   => 'codeUrl',      //response二维码key
        'errorCode' => 'state',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'codeUrl',     //获取二维码失败的错误描述字段
    ],
    'Yusanpay'         => [
        'order'     => 'sdorderno',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Yunszfpay'        => [
        'order'     => 'requestNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Cntpay'           => [
        'order'     => 'userOrder',  //固定参数，订单号
        'amount'    => 'number',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'resultCode',   //获取二维码失败的状态吗字段
        'errorMsg'  => 'resultMsg',     //获取二维码失败的错误描述字段
    ],
    'Hengszfpay'       => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'Status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Kuaizfpay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderPrice',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Niunpay'          => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Changzhifupay'    => [
        'order'     => 'mch_order',  //固定参数，订单号
        'amount'    => 'relAmount',  //固定参数，金额
        'qrPath'    => 'codeUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Heyingpay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Youfubzpay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'targetUrl',      //response二维码key
        'appPath'   => 'targetUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Huilfpay'         => [
        'order'     => 'traceno',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'codeUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Jiuyunpay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderPrice',  //固定参数，金额
        'qrPath'    => 'codeurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Changyfpay'       => [
        'order'     => 'orderNumber',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'content',      //response二维码key
        'appPath'   => 'content',      //response二维码key
        'errorCode' => 'returnCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Qianyzfpay'       => [
        'order'     => 'innerorderid',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'codeUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Chuangszfpay'     => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'transAmt',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Koudaizpay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Yingzipay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    'Xinjiyuanpay'     => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'code_url',      //response二维码key
        'appPath'   => 'code_url',      //response二维码key
        'errorCode' => 'return_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],

    'Oudoupay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Sanliuwzfpay'     => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'return_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    'Zhimfpay'         => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'order_amt',  //固定参数，金额
        'qrPath'    => 'pay_url',      //response二维码key
        'appPath'   => 'pay_url',      //response二维码key
        'errorCode' => 'rsp_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'rsp_msg',     //获取二维码失败的错误描述字段
    ],
    'Suixfvpay'        => [
        'order'     => 'sdorderno',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Changhepay'       => [
        'order'     => 'orderNum',  //固定参数，订单号
        'amount'    => 'orderMoney',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'qrcode',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Longshengpay'     => [
        'order'     => 'MerOrderNo',  //固定参数，订单号
        'amount'    => 'Amount',  //固定参数，金额
        'qrPath'    => 'codeurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Didifpay'         => [
        'order'     => 'mchntOrderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    'Sihaipay'         => [
        'order'     => 'p2_Order',  //固定参数，订单号
        'amount'    => 'p3_Amt',  //固定参数，金额
        'qrPath'    => 'payImg',      //response二维码key
        'appPath'   => 'payImg',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'Msg',     //获取二维码失败的错误描述字段
    ],
    'Jutongzfpay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'result_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Balzfpay'         => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Qiftpay'          => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    'Sanllpay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmt',  //固定参数，金额
        'qrPath'    => 'jumpUrl',      //response二维码key
        'appPath'   => 'jumpUrl',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Liullpay'         => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'payURL',      //response二维码key
        'appPath'   => 'payURL',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Rongcpay'         => [
        'order'     => 'innerorderid',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Koudaizxpay'      => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Longxinzfvpay'    => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'errorCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errorMessage',     //获取二维码失败的错误描述字段
    ],
    'Baiyqlpay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'result_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'result_msg',     //获取二维码失败的错误描述字段
    ],
    'Anyifupay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'payAmt',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Yinxinpay'        => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Honbaopay'        => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'qrcode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'desc',     //获取二维码失败的错误描述字段
    ],
    'Chenytpay'        => [
        'order'     => 'p4_orderno',  //固定参数，订单号
        'amount'    => 'p3_paymoney',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'rspCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'rspMsg',     //获取二维码失败的错误描述字段
    ],
    'Badatongpay'      => [
        'order'     => 'childOrderno',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Simazfpay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Yongrenzfpay'     => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'merchParam',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'qrcode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Lovefupay'        => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'code_url',      //response二维码key
        'appPath'   => 'code_url',      //response二维码key
        'errorCode' => 'result_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'result_msg',     //获取二维码失败的错误描述字段
    ],
    'Tiantzfvspay'     => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Defupay'          => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'totalFee',  //固定参数，金额
        'qrPath'    => '',      //response二维码key
        'appPath'   => '',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    'Wangwzfpay'       => [
        'order'     => 'innerorderid',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'data',      //response二维码key
        'appPath'   => 'data',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Shunfzfpay'       => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Jiuzhouzfvpay'    => [
        'order'     => 'request_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'success',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Skpay'            => [
        'order'     => 'outOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Yidavyipay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Daydaypay'        => [
        'order'     => 'out_order_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'errcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errmsg',     //获取二维码失败的错误描述字段
    ],
    'Thbpay'           => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'qrcode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Tianxzfpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Yijupay'          => [
        'order'     => 'userOrderId',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Honglianzfpay'    => [
        'order'     => 'osn',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Huitengpay'       => [
        'order'     => 'transactionId',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'appPath'   => 'payUrl',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Canbbpay'         => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'appPath'   => 'payUrl',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Xunjzhtpay'       => [
        'order'     => 'customno',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'appPath'   => 'payUrl',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'resultCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resultMsg',     //获取二维码失败的错误描述字段
    ],
    'Quannpay'         => [
        'order'     => 'pay_orderNo',  //固定参数，订单号
        'amount'    => 'pay_Amount',  //固定参数，金额
        'appPath'   => 'pay_Code',     //response app/H5 跳转
        'qrPath'    => 'pay_Code',      //response二维码key
        'errorCode' => 'pay_Status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'pay_CodeMsg',     //获取二维码失败的错误描述字段
    ],
    'Quzfpay'          => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'appPath'   => 'pay_Code',     //response app/H5 跳转
        'qrPath'    => 'pay_Code',      //response二维码key
        'errorCode' => 'success',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Tengfeizpay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'appPath'   => 'pay_url',     //response app/H5 跳转
        'qrPath'    => 'pay_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Yixpay'           => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'orderPrice',  //固定参数，金额
        'appPath'   => 'qrCode',     //response app/H5 跳转
        'qrPath'    => 'qrCode',      //response二维码key
        'errorCode' => 'resultCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errMsg',     //获取二维码失败的错误描述字段
    ],
    'Didazpay'         => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'appPath'   => 'payUrl',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Jucaipay'         => [
        'order'     => 'shop_order_no',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'appPath'   => 'url',     //response app/H5 跳转
        'qrPath'    => 'url',      //response二维码key
        'errorCode' => 'res_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'res_msg',     //获取二维码失败的错误描述字段
    ],
    'Shouyinbaopay'    => [
        'order'     => 'order_num',  //固定参数，订单号
        'amount'    => 'order_price',  //固定参数，金额
        'appPath'   => 'get_img_url',     //response app/H5 跳转
        'qrPath'    => 'get_img_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Goldmypay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'appPath'   => 'code_url',     //response app/H5 跳转
        'qrPath'    => 'code_url',      //response二维码key
        'errorCode' => 'return_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'return_msg',     //获取二维码失败的错误描述字段
    ],
    'Bobtpay'          => [
        'order'     => 'orderCode',  //固定参数，订单号
        'amount'    => 'orderTotal',  //固定参数，金额
        'appPath'   => 'payUrl',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Waterstarpay'     => [
        'order'     => 'orderNum',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'appPath'   => 'qrCode',     //response app/H5 跳转
        'qrPath'    => 'qrCode',      //response二维码key
        'errorCode' => 'stateCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Juzhengpay'       => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'appPath'   => 'codeUrl',     //response app/H5 跳转
        'qrPath'    => 'codeUrl',      //response二维码key
        'errorCode' => 'err',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Chaofanzfpay'     => [
        'order'     => 'mchntOrderId',  //固定参数，订单号
        'amount'    => 'txnAmt',  //固定参数，金额
        'appPath'   => 'imgUrl',     //response app/H5 跳转
        'qrPath'    => 'imgUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Godmupay'         => [
        'order'     => 'orderno',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'appPath'   => 'code_url',     //response app/H5 跳转
        'qrPath'    => 'code_url',      //response二维码key
        'errorCode' => 'err',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Clouldpay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'appPath'   => 'payurl',     //response app/H5 跳转
        'qrPath'    => 'payurl',      //response二维码key
        'errorCode' => 'err_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'err_msg',     //获取二维码失败的错误描述字段
    ],
    'Monsterpay'       => [
        'order'     => 'order_sn',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'appPath'   => 'qrcode',     //response app/H5 跳转
        'qrPath'    => 'qrcode',      //response二维码key
        'errorCode' => 'errcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Wendangfupay'     => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnamt',  //固定参数，金额
        'appPath'   => 'qrcode',     //response app/H5 跳转
        'qrPath'    => 'qrcode',      //response二维码key
        'errorCode' => 'respcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',     //获取二维码失败的错误描述字段
    ],
    //一加支付
    'Yijiazfpay'       => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Xinxiezfpay'      => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',    //获取二维码失败的错误描述字段
    ],
    'Uchuangpay'       => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnamt',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'respcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',    //获取二维码失败的错误描述字段
    ],
    'Sanliupay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Mifengzvapay'     => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'state',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Wanfapay'         => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'redirect_url',      //response二维码key
        'appPath'   => 'redirect_url',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Sanyaobapay'      => [
        'order'     => 'merchantOrderNo',  //固定参数，订单号
        'amount'    => 'payPrice',    //固定参数，金额
        'qrPath'    => 'payPic',  //response二维码key
        'appPath'   => 'payPic',  //response手机端h5链接key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Xinhuifuvpay'     => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Redbullpay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'price',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Yifpay'           => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',    //获取二维码失败的错误描述字段
    ],
    'Yinshengzpay'     => [
        'order'     => 'order_number',  //固定参数，订单号
        'amount'    => 'qr_amount',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'ret',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',    //获取二维码失败的错误描述字段
    ],
    'Huanshangpay'     => [
        'order'     => 'out_order_number',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Xinyibaopay'      => [
        'order'     => 'orderNum',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'define',    //获取二维码失败的错误描述字段
    ],
    //稳稳付
    'Wenwenpay'        => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payCode',      //response二维码key
        'appPath'   => 'payCode',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errCodeDes',     //获取二维码失败的错误描述字段
    ],
    'Gugzfpay'         => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'totalAmount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 's',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Chaopaopay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Judingzpay'       => [
        'order'     => 'ordercode',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Newpay'           => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => '',      //response二维码key
        'appPath'   => '',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Dongfhvpay'       => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Yizfpay'          => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'state',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Mengjiupay'       => [
        'order'     => 'businessnumber',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Saicpay'          => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Mingyuanpay'      => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payJumpUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    'Yiyunvvpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Tongfuvpay'       => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Ermazfpay'        => [
        'order'     => 'traceno',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Bolezfpay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'payMoney',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Guanshupay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'code_url',      //response二维码key
        'appPath'   => 'code_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Quanfuvypay'      => [
        'order'     => 'orderNum',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'resp_ErrCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_ErrMsg',     //获取二维码失败的错误描述字段
    ],
    'Wantzfvpay'       => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Henanyfpay'       => [
        'order'     => 'shoporderId',  //固定参数，订单号
        'amount'    => 'totalamount',  //固定参数，金额
        'qrPath'    => 'payPic',      //response二维码key
        'appPath'   => 'payPic',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Jingniuzfpay'     => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Yongrzfvpay'      => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'codeUrl',      //response二维码key
        'appPath'   => 'codeUrl',      //response二维码key
        'errorCode' => 'dealCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'dealMsg',     //获取二维码失败的错误描述字段
    ],
    'Wandavpay'        => [
        'order'     => 'CustomerId',  //固定参数，订单号
        'amount'    => 'Money',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'qrcode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Shishipay'        => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'code_url',      //response二维码key
        'appPath'   => 'code_url',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Quanhuibeipay'    => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Ruiyingpay'       => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Quansuzfpay'      => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errmsg',     //获取二维码失败的错误描述字段
    ],
    'Ugbipay'          => [
        'order'     => 'merchOrderSn',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Wangfapay'        => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'redirect_url',      //response二维码key
        'appPath'   => 'redirect_url',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Quanfupay'        => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'qrcode',      //response二维码key
        'errorCode' => 'ret',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Caomaozfpay'      => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnamt',  //固定参数，金额
        'appPath'   => 'qrcode',     //response app/H5 跳转
        'qrPath'    => 'qrcode',      //response二维码key
        'errorCode' => 'respcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',     //获取二维码失败的错误描述字段
    ],
    'Suibianmallpay'   => [
        'order'     => 'linkId',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'appPath'   => 'payUrl',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Dadazfpay'        => [
        'order'     => 'tradeNo',  //固定参数，订单号
        'amount'    => 'totalFee',  //固定参数，金额
        'appPath'   => 'qrcode',     //response app/H5 跳转
        'qrPath'    => 'qrcode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Jizhipay'         => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'appPath'   => 'payUrl',     //response app/H5 跳转
        'qrPath'    => 'payUrl',      //response二维码key
        'errorCode' => 'status_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'status_msg',     //获取二维码失败的错误描述字段
    ],
    'Luojinpay'        => [
        'order'     => 'pay_orderno',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Mmpay'            => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Huizhonpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],
    'Baochengzfpay'    => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Lelifpay'         => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'txnAmt',    //固定参数，金额
        'qrPath'    => 'codeImgUrl',  //response二维码key
        'appPath'   => 'codePageUrl',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],
    'Mihoutaopay'      => [
        'order'     => 'orderNum',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Antspay'          => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Huizhongfpay'     => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],
    'Majipay'          => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'txnAmt',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],
    'Tongltpay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'result_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'result_msg',    //获取二维码失败的错误描述字段
    ],
    'Fangtepay'        => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Ufpay'            => [
        'order'     => 'outOrderId',  //固定参数，订单号
        'amount'    => 'transAmt',    //固定参数，金额
        'qrPath'    => 'payCode',  //response二维码key
        'appPath'   => 'payCode',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],
    'Longmazfpay'      => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'payamount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Hmpay'            => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Mscpay'           => [
        'order'     => 'number',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'data',    //获取二维码失败的错误描述字段
    ],
    'Skbankpay'        => [
        'order'     => 'outOrderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'data',  //response二维码key
        'appPath'   => 'data',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Feifzpay'         => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Kingvepay'        => [
        'order'     => 'merchantOrderNumber',  //固定参数，订单号
        'amount'    => 'requestedAmount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Caiguozfpay'      => [
        'order'     => 'merchantOrderNo',  //固定参数，订单号
        'amount'    => 'payPrice',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Xinyuanzfpay'     => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'price',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'Status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'Message',    //获取二维码失败的错误描述字段
    ],
    'Qingpingguopay'   => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Shunyoupay'       => [
        'order'     => 'mer_order_no',  //固定参数，订单号
        'amount'    => 'trade_amount',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'errorCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errorMsg',    //获取二维码失败的错误描述字段
    ],
    'Weisutongpay'     => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'payURL',      //response二维码key
        'appPath'   => 'payURL',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Rongyizpay'       => [
        'order'     => 'out_trade_id',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'qrcode',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Yinjianpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'code_img_url',      //response二维码key
        'appPath'   => 'prepay_url',      //response二维码key
        'errorCode' => 'return_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'return_msg',     //获取二维码失败的错误描述字段
    ],
    'Bozfpay'          => [
        'order'     => 'attach',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'data',      //response二维码key
        'appPath'   => 'data',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Rongjinfupay'     => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'order_amt',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'status_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'status_msg',     //获取二维码失败的错误描述字段
    ],
    'Rongfuzfpay'      => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => 'codeUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Xinbaozfpay'      => [
        'order'     => 'MerOrderNo',  //固定参数，订单号
        'amount'    => 'Amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Chenggongzfpay'   => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnamt',  //固定参数，金额
        'qrPath'    => 'formaction',      //response二维码key
        'appPath'   => 'formaction',      //response二维码key
        'errorCode' => 'respcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',     //获取二维码失败的错误描述字段
    ],
    'Xinfazhifuvpay'   => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Newbpay'          => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => '',      //response二维码key
        'appPath'   => '',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Renrenzfverpay'   => [
        'order'     => 'merchantorderid',  //固定参数，订单号
        'amount'    => 'applyamount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Zhuoyuezfpay'     => [
        'order'     => 'innerorderid',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Xunjiezfvepay'    => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnamt',    //固定参数，金额
        'qrPath'    => 'formaction',  //response二维码key
        'appPath'   => 'formaction',  //response手机端h5链接key
        'errorCode' => 'respcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',    //获取二维码失败的错误描述字段
    ],
    'Zhizunverpay'     => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Naixuepay'        => [
        'order'     => 'corp_flow_no',  //固定参数，订单号
        'amount'    => 'totalAmount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'Code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'Msg',    //获取二维码失败的错误描述字段
    ],
    'Lovefuvpay'       => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'code_url',      //response二维码key
        'appPath'   => 'code_url',      //response二维码key
        'errorCode' => 'result_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'result_msg',     //获取二维码失败的错误描述字段
    ],
    'Lidpay'           => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'total',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Bibaozfvepay'     => [
        'order'     => 'out_order_id',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Sophiaquickpay'   => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'orderAmt',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Yubaofupay'       => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'returnCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Baifutongvepay'   => [
        'order'     => 'merchant_order_no',  //固定参数，订单号
        'amount'    => 'trade_amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'info',     //获取二维码失败的错误描述字段
    ],
    'Tiandipay'        => [
        'order'     => 'prdOrNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'code_url',      //response二维码key
        'appPath'   => 'code_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Wangwpay'         => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'error',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Wanlianpay'       => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'data',      //response二维码key
        'appPath'   => 'data',      //response二维码key
        'errorCode' => 'resp_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_msg',     //获取二维码失败的错误描述字段
    ],
    'Jinxinyuanpay'    => [
        'order'     => 'clientDealCode',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Chenlezpay'       => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'urls',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'content',     //获取二维码失败的错误描述字段
    ],
    'Jisuvepay'        => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Laojinzfpay'      => [
        'order'     => 'out_bill_num',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'response_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'response_msg',     //获取二维码失败的错误描述字段
    ],
    'Wanlizfvspay'     => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'data',      //response二维码key
        'appPath'   => 'data',      //response二维码key
        'errorCode' => 'resp_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_msg',     //获取二维码失败的错误描述字段
    ],
    'Guanbaopay'       => [
        'order'     => 'orderIdCp',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'data',      //response二维码key
        'appPath'   => 'data',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Bfourpay'         => [
        'order'     => 'tradeNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Miaoshoupay'      => [
        'order'     => 'trade_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'errcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errmsg',     //获取二维码失败的错误描述字段
    ],
    'Yubaozfpay'       => [
        'order'     => 'OrderNo',  //固定参数，订单号
        'amount'    => 'OrderAmount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'ErrCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'ErrMsg',     //获取二维码失败的错误描述字段
    ],
    'Quanqfvpay'       => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Xinscpay'         => [
        'order'     => 'down_trade_no',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Huijypay'         => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    //免签支付
    'Mianqianpay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response二维码key
        'errorCode' => 'status',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',  //获取二维码失败的错误描述字段
    ],
    //Yyyy支付
    'Yyyypay'          => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'order_amt',  //固定参数，金额
        'qrPath'    => 'pay_data',  //response二维码key
        'appPath'   => 'pay_data',  //response二维码key
        'errorCode' => 'status_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'status_msg',  //获取二维码失败的错误描述字段
    ],
    'Ypay'             => [
        'order'     => 'out_order_id',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response二维码key
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    'Zuanspay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'returnUrl',  //response二维码key
        'appPath'   => 'returnUrl',  //response二维码key
        'errorCode' => 'ordercode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'dealmsg',  //获取二维码失败的错误描述字段
    ],
    'Sanshipay'        => [
        'order'     => 'merchantOrderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response二维码key
        'errorCode' => 'status',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    'Leishepay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response二维码key
        'errorCode' => 'error',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    'Huazfpay'         => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'total',  //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response二维码key
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    'Wuerpay'          => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response二维码key
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    'Qidzfpay'         => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',  //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response二维码key
        'errorCode' => 'status',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',  //获取二维码失败的错误描述字段
    ],
    'Bulspay'          => [
        'order'     => 'platformOrderNum',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response二维码key
        'errorCode' => 'status',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    'Wangyipay'        => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'cashierURL',  //response二维码key
        'appPath'   => 'cashierURL',  //response二维码key
        'errorCode' => 'errcode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'errmsg',  //获取二维码失败的错误描述字段
    ],
    'Shenfuzpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'pay_info',  //response二维码key
        'appPath'   => 'pay_info',  //response二维码key
        'errorCode' => 'return_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'return_msg',  //获取二维码失败的错误描述字段
    ],
    'Facaipay'         => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnamt',  //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response二维码key
        'errorCode' => 'respcode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',  //获取二维码失败的错误描述字段
    ],
    'Minypay'          => [
        'order'     => 'userOrder',  //固定参数，订单号
        'amount'    => 'number',  //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response二维码key
        'errorCode' => 'resultCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'resultMsg',  //获取二维码失败的错误描述字段
    ],
    'Nhtexpay'         => [
        'order'     => 'game_order_no',  //固定参数，订单号
        'amount'    => 'charge_amount',  //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response二维码key
        'errorCode' => 'status',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',  //获取二维码失败的错误描述字段
    ],
    'Zhenhfpay'        => [
        'order'     => 'outOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response二维码key
        'errorCode' => 'status',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    'Qingtingpay'      => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response二维码key
        'errorCode' => 'retCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'res_msg',  //获取二维码失败的错误描述字段
    ],
    'Dongfzfpay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response二维码key
        'errorCode' => 'retCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'res_msg',  //获取二维码失败的错误描述字段
    ],
    'Yicaitongpay'     => [
        'order'     => 'payment_id',  //固定参数，订单号
        'amount'    => 'total_amount',  //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response二维码key
        'errorCode' => 'status',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    'Hanyunpay'        => [
        'order'     => 'orderno',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response二维码key
        'errorCode' => 'statue',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    'Fuyouvpay'        => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnamt',  //固定参数，金额
        'qrPath'    => 'formaction',  //response二维码key
        'appPath'   => 'formaction',  //response二维码key
        'errorCode' => 'respcode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'respmsg',  //获取二维码失败的错误描述字段
    ],
    'Liubapay'         => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response二维码key
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    'Caimengpay'       => [
        'order'     => 'merOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response二维码key
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',  //获取二维码失败的错误描述字段
    ],
    'Guangmingpay'     => [
        'order'     => 'businessnumber',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response二维码key
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    'Zhongqiyidongpay' => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',  //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response二维码key
        'errorCode' => 'status',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',  //获取二维码失败的错误描述字段
    ],
    'Zhifuxingpay'     => [
        'order'     => 'cpOrderId',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response二维码key
        'errorCode' => 'status',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'statusDes',  //获取二维码失败的错误描述字段
    ],
    'Fengyunpay'       => [
        'order'     => 'businessOrdid',  //固定参数，订单号
        'amount'    => 'tradeMoney',  //固定参数，金额
        'qrPath'    => 'code_img_url',  //response二维码key
        'appPath'   => 'code_img_url',  //response二维码key
        'errorCode' => 'respCode',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'respDesc',  //获取二维码失败的错误描述字段
    ],
    'Shuangqzpay'      => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'payresult',  //response二维码key
        'appPath'   => 'payresult',  //response二维码key
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'code',  //获取二维码失败的错误描述字段
    ],
    'Fengwozpay'       => [
        'order'     => 'merchant_order_sn',  //固定参数，订单号
        'amount'    => 'total',  //固定参数，金额
        'qrPath'    => 'qr_code',      //response二维码key
        'appPath'   => 'wap_pay_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Sanlizfpay'       => [
        'order'     => 'osn',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
    ],
    'Fuguizpay'     => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'goodsname',  //固定参数，金额
        'qrPath'    => 'qrcode',      //response二维码key
        'appPath'   => 'pay_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Tianbaozpay'     => [
        'order'     => 'oid',  //固定参数，订单号
        'amount'    => 'amt',  //固定参数，金额
        'qrPath'    => 'data',      //response二维码key
        'appPath'   => 'data',      //response二维码key
        'errorCode' => 'errorcode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Qifeizpay'     => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderPrice',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',     //获取二维码失败的错误描述字段
    ],
    'Shunfengzfpay'     => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',     //获取二维码失败的错误描述字段
    ],
    'Futzfpay'     => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_no',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'html',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
	'Applepaipay'     => [
		'order'     => 'mch_order_no',  //固定参数，订单号
		'amount'    => 'trade_amount',  //固定参数，金额
		'qrPath'    => 'qrcode',      //response二维码key
		'appPath'   => 'qrcode',      //response二维码key
		'errorCode' => 'errcode',    //获取二维码失败的状态吗字段
		'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
	],
    //秒通支付
    'Jisufupay'      => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response二维码key
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
	'Hongxingpay'     => [
		'order'     => 'apporderid',  //固定参数，订单号
		'amount'    => 'amount',  //固定参数，金额
		'qrPath'    => 'qrcode',      //response二维码key
		'appPath'   => 'payUrl',      //response二维码key
		'errorCode' => 'code',    //获取二维码失败的状态吗字段
		'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
	],
	'Caifubaovpay'     => [
		'order'     => 'order',  //固定参数，订单号
		'amount'    => 'amount',  //固定参数，金额
		'qrPath'    => 'qrCode',      //response二维码key
		'appPath'   => 'qrCode',      //response二维码key
		'errorCode' => 'status',    //获取二维码失败的状态吗字段
		'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
	],
    'Changchengzfpay'       => [
        'order'     => 'trade_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response二维码key
        'errorCode' => 'status',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',  //获取二维码失败的错误描述字段
    ],
    'Binlipay'     => [
        'order'     => 'ordernumber',  //固定参数，订单号
        'amount'    => 'paymoney',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'html',      //response二维码key
        'errorCode' => 'responsedesc',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'responsedesc',     //获取二维码失败的错误描述字段
    ],
    //万里宝
    'Wanlibaopay'   =>[
        'order'       => 'orderid',    //固定参数，订单号
        'amount'      => 'amount',      //固定参数，金额
        'md5Sign'     => 'sign',            //固定参数，签名
        'orderStatus' => [            //固定参数，订单状态
            'returncode',           //键名
            '00'                      //对应的成功的值
        ],
        'actual_amount',
        'datetime',
        'returncode',
        'transaction_id',

    ],
    'Yubaofuvpay'       => [
        'order'     => 'OrderNo',  //固定参数，订单号
        'amount'    => 'OrderAmount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'ErrCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'ErrMsg',     //获取二维码失败的错误描述字段
    ],
    'Lutongpay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Hongfengpay'   => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
        ],
    'Jufuzpay'       => [
        'order'     => 'mchntOrderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    'Kaiyuanpay'   => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'price',    //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'error',     //获取二维码失败的状态吗字段
        'errorMsg'  => 'error_des',     //获取二维码失败的错误描述字段
    ],
    'Longmaozfpay'   => [
        'order'     => 'shopOutTradeId',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'resultCode',     //获取二维码失败的状态吗字段
        'errorMsg'  => 'desc',     //获取二维码失败的错误描述字段
    ],
    'Bowangzfpay'      => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',    //获取二维码失败的错误描述字段
    ],
    'Mantangfupay'   => [
        'order'     => 'ordernumber',  //固定参数，订单号
        'amount'    => 'paymoney',    //固定参数，金额
        'qrPath'    => 'url',       //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'success',     //获取二维码失败的状态吗字段
        'errorMsg'  => 'responsedesc',     //获取二维码失败的错误描述字段
        ],
    'Kuangbaopay'   => [
        'order'     => 'merchant_order_sn',  //固定参数，订单号
        'amount'    => 'merchant_order_money',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Hongchengpay'       => [
        'order'     => 'out_bill_num',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'response_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'response_msg',     //获取二维码失败的错误描述字段
    ],
    'Gaosupay'       => [
        'order'     => 'trade_no',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'response_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'response_msg',     //获取二维码失败的错误描述字段
    ],
    'Jubaopenpay'   => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => 'data',      //response二维码key
        'appPath'   => 'data',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Xinhefupay'   => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    'Dingshengtwopay' => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Xxmpay' => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'transAmt',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Zhonglianzhifpay'   => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Qingtianpay'   => [
        'order'     => 'sdorderno',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'h5_url',      //response二维码key
        'appPath'   => 'h5_url',      //response二维码key
        'errorCode' => 'status',      //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg_info',     //获取二维码失败的错误描述字段
    ],
    'Maopay'   => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Rutongpay'   => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Tiemapay'   => [
        'order'     => 'trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Bbkpay'   => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',     //获取二维码失败的错误描述字段
    ],
    'Bailongmapay'   => [
        'order'     => 'external',  //固定参数，订单号
        'amount'    => 'money',  //固定参数，金额
        'qrPath'    => 'img',      //response二维码key
        'appPath'   => 'img',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Renrenvpay'   => [
        'order'     => 'outOrderId',  //固定参数，订单号
        'amount'    => 'transAmt',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',     //获取二维码失败的错误描述字段
    ],
    'Gaodepay'   => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'totalAmount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',     //获取二维码失败的错误描述字段
    ],
    'Qianhaipay'   => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'totalMoney',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Shoupay'   => [
        'order'     => 'transNo',  //固定参数，订单号
        'amount'    => 'transAmount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Beijezfpay'   => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'errCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],

    'Youqianfupay'   => [
        'order'     => 'morder_code',  //固定参数，订单号
        'amount'    => 'order_fee',  //固定参数，金额
        'qrPath'    => 'pay_url',      //response二维码key
        'appPath'   => 'pay_url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Aimisenpay'   => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'respcd',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Ruijietongpay'   => [
        'order'     => 'orderNum',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'stateCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'stateMsg',     //获取二维码失败的错误描述字段
    ],
    'Xingfpay'   => [
        'order'     => 'linkId',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'resultCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resultMsg',     //获取二维码失败的错误描述字段
    ],
    'Youmifupay'   => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amt',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',     //获取二维码失败的错误描述字段
    ],
    'Liuliufpay'   => [
        'order'     => 'userOrderId',  //固定参数，订单号
        'amount'    => 'fee',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Jfbvpay'   => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Renzhipay'   => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Shengtongpay'   => [
        'order'     => 'field048',  //固定参数，订单号
        'amount'    => 'field004',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'field039',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'field039',     //获取二维码失败的错误描述字段
    ],
    'Xinqidianpay'   => [
        'order'     => 'traceno',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',     //获取二维码失败的错误描述字段
    ],
    'Hxinpay'   => [
        'order'     => 'mer_order_no',  //固定参数，订单号
        'amount'    => 'trade_amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'tradeResult',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'tradeResult',     //获取二维码失败的错误描述字段
    ],
    'Shengyuzfpay'       => [
        'order'     => 'userOrderId',  //固定参数，订单号
        'amount'    => 'fee',    //固定参数，金额
        'qrPath'    => 'param',  //response二维码key
        'appPath'   => 'param',  //response手机端h5链接key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Longshengzfpay'       => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'resp_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_msg',    //获取二维码失败的错误描述字段
    ],
    'Xinhongypay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'error',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Sanspay'       => [
        'order'     => 'fxddh',  //固定参数，订单号
        'amount'    => 'fxfee',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',    //获取二维码失败的错误描述字段
    ],
    'Momozfpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Jiaszfpay'       => [
        'order'     => 'mch_trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'success',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'success',    //获取二维码失败的错误描述字段
    ],
    'Chengfubaovpay'       => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'code',    //获取二维码失败的错误描述字段
    ],
    'Jinzhuanpay'       => [
        'order'     => 'mch_trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'success',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'success',    //获取二维码失败的错误描述字段
    ],
    'Haofuzfpay'       => [
        'order'     => 'trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'is_success',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'is_success',    //获取二维码失败的错误描述字段
        ],
    'Suixingfupay'       => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'error',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Pandapay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'value_cny',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'error',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Huijuzfpay'       => [
        'order'     => 'pay_orderno',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'msg',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Leitingpay'       => [
        'order'     => 'childOrderno',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Zhongrzfpay'       => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'mvpStatus',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'mvpStatus',    //获取二维码失败的错误描述字段
    ],
    'Baolongpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'state',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'state',    //获取二维码失败的错误描述字段
    ],
    'Xinscvpay'       => [
        'order'     => 'organizationOrderCode',  //固定参数，订单号
        'amount'    => 'orderPrice',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'code',    //获取二维码失败的错误描述字段
    ],
    'Zhongcaipay'       => [
        'order'     => 'outer_order_sn',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'code',    //获取二维码失败的错误描述字段
    ],
    'Xingzfpay'       => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'code',    //获取二维码失败的错误描述字段
    ],
    'Weizfpay'       => [
        'order'     => 'oid',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'url4',  //response二维码key
        'appPath'   => 'url4',  //response手机端h5链接key
        'errorCode' => 'err',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Zhonlianvezpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_amount',    //固定参数，金额
        'qrPath'    => 'pay_info',  //response二维码key
        'appPath'   => 'pay_info',  //response手机端h5链接key
        'errorCode' => 'result_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'err_msg',    //获取二维码失败的错误描述字段
        ],
    'Heimipay'       => [
        'order'     => 'trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'pcodeurl',  //response二维码key
        'appPath'   => 'pcodeurl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
        ],
    'Guibingpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'order_amount',    //固定参数，金额
        'qrPath'    => 'pay_url',   //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Sanduopay'       => [
        'order'     => 'Trade_No',  //固定参数，订单号
        'amount'    => 'Price',    //固定参数，金额
        'qrPath'    => 'Url',  //response二维码key
        'appPath'   => 'Url',  //response手机端h5链接key
        'errorCode' => 'Status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'Mesg',    //获取二维码失败的错误描述字段
    ],
    'Yuntianpay'       => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'state',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Xhbpay'       => [
        'order'     => 'traceno',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],
    'Wofuvvpay'          => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'order_amount',  //固定参数，金额
        'qrPath'    => 'qrcode',//response二维码key
        'errorCode' => 'resp_code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_desc',  //获取二维码失败的错误描述字段
        ],
    'Apipay'          => [
        'order'     => 'out_order_id',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'url', //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'code',  //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',  //获取二维码失败的错误描述字段
    ],
    'Wanmeizfpay'       => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Bafangpay'       => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'msg',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'err',    //获取二维码失败的错误描述字段
    ],
    'Weipay'       => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'totalFee',    //固定参数，金额
        'qrPath'    => 'payInfo',  //response二维码key
        'appPath'   => 'payInfo',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Daxiangpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Tengfeitwopay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'QrCodeUrl',  //response二维码key
        'appPath'   => 'QrCodeUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
        ],
    'Jinzuanpay'       => [
        'order'     => 'jOrderId',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Chengnuozfpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Tuozhongpay'       => [
        'order'     => 'merchantOrderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Zuanshipay'       => [
        'order'     => 'outOrderId',  //固定参数，订单号
        'amount'    => 'orderFund',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Lubanqihaopay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Qibapay'       => [
        'order'     => 'out_trade_no_mch',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Motttopay'       => [
        'order'     => 'orderSn',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'gopayUrl',  //response二维码key
        'appPath'   => 'gopayUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Maotaipay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'payAmt',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',    //获取二维码失败的错误描述字段
    ],
    'Tfupay'       => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'currencyType',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
    ],
    'Newzfpay'           => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payCode',      //response二维码key
        'appPath'   => 'payCode',      //response二维码key
        'errorCode' => 'returnCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'errCodeDes',     //获取二维码失败的错误描述字段
    ],
    'Daxiapay'           => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_amount',  //固定参数，金额
        'qrPath'    => 'pay_info',      //response二维码key
        'appPath'   => 'pay_info',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Jinyinhuapay'           => [
        'order'     => 'merchantOrderNo',  //固定参数，订单号
        'amount'    => 'payPrice',  //固定参数，金额
        'qrPath'    => 'payPic',      //response二维码key
        'appPath'   => 'payPic',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Dafatwopay'           => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'error',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Haihaipay'           => [
        'order'     => 'merchantOrderNo',  //固定参数，订单号
        'amount'    => 'payPrice',  //固定参数，金额
        'qrPath'    => 'payPic',      //response二维码key
        'appPath'   => 'payPic',      //response二维码key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Yichongzf'           => [
        'order'     => 'submit_order_id',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Kkpayzf'           => [
        'order'     => 'mercOrderId',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Guomeipay'           => [
        'order'     => 'requestId',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCodeUrl',      //response二维码key
        'appPath'   => 'qrCodeUrl',      //response二维码key
        'errorCode' => 'returnCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Lianhepay'           => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'price',  //固定参数，金额
        'qrPath'    => 'payurl',      //response二维码key
        'appPath'   => 'payurl',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Gtpay'           => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'qrCode',      //response二维码key
        'appPath'   => 'qrCode',      //response二维码key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',     //获取二维码失败的错误描述字段
    ],
    'Kedoupay'           => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'payUrl',      //response二维码key
        'appPath'   => 'payUrl',      //response二维码key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',     //获取二维码失败的错误描述字段
    ],
    'Baihuitpay'           => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Bujiadipay'           => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'amount',  //固定参数，金额
        'qrPath'    => 'url',      //response二维码key
        'appPath'   => 'url',      //response二维码key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',     //获取二维码失败的错误描述字段
    ],
    'Wangzhezfpay'        => [
        'order'     => 'merchantOrderNumber',  //固定参数，订单号
        'amount'    => 'requestedAmount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Goldengpay'        => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrCode',  //response二维码key
        'appPath'   => 'qrCode',  //response手机端h5链接key
        'errorCode' => 'resp_Code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_ErrMsg',    //获取二维码失败的错误描述字段
    ],
    'Wangzzfpay'        => [
        'order'     => 'traceno',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'barUrl',  //response二维码key
        'appPath'   => 'barUrl',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Shenzzfupay'        => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Meimeipay'        => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'orderAmt',    //固定参数，金额
        'qrPath'    => 'jumpUrl',  //response二维码key
        'appPath'   => 'jumpUrl',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'respMsg',    //获取二维码失败的错误描述字段
        ],
    'Shangwenzfpay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Xinkuaipay'        => [
        'order'     => 'outerOrderId',  //固定参数，订单号
        'amount'    => 'submitAmount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Gegepay'        => [
        'order'     => 'payOrderId',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Qufubaopay'        => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'price',    //固定参数，金额
        'qrPath'    => 'Data',  //response二维码key
        'appPath'   => 'Data',  //response手机端h5链接key
        'errorCode' => 'Result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'Msg',    //获取二维码失败的错误描述字段
    ],
    'Kuaijianpay'        => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'trade_amount',    //固定参数，金额
        'qrPath'    => 'data',  //response二维码key
        'appPath'   => 'data',  //response手机端h5链接key
        'errorCode' => 'resp_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resp_msg',    //获取二维码失败的错误描述字段
    ],
    'Jinzzfpay'        => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Xunjiezfpay'       => [
       'order'     => 'businessnumber',  //固定参数，订单号
       'amount'    => 'amount',    //固定参数，金额
       'qrPath'    => 'trade_qrcode',  //response二维码key
       'appPath'   => 'trade_qrcode',  //response手机端h5链接key
       'errorCode' => 'code',    //获取二维码失败的状态吗字段
       'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
   ],
    'Yimiyangguang'       => [
        'order'     => 'request_id',  //固定参数，订单号
        'amount'    => 'ord_amount',    //固定参数，金额
        'qrPath'    => 'data',  //response二维码key
        'appPath'   => 'data',  //response手机端h5链接key
        'errorCode' => 'rsp_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'rsp_msg',    //获取二维码失败的错误描述字段
    ],
    'Shandiantianpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'mweb_url',  //response二维码key
        'appPath'   => 'mweb_url',  //response手机端h5链接key
        'errorCode' => 'err_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'err_msg',    //获取二维码失败的错误描述字段
    ],
    'Zhongyizpay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'returnCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Xiongmaozfpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'total_fee',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'error',    //获取二维码失败的错误描述字段
    ],
    'Sanyuanpay'       => [
        'order'     => 'ref_number',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Shunfengtwopay'       => [
        'order'     => 'transNumber',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Sanliuwuzfpay'       => [
        'order'     => 'merchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'OKzfpay'       => [
        'order'     => 'orderNumber',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'content',  //response二维码key
        'appPath'   => 'content',  //response手机端h5链接key
        'errorCode' => 'returnCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Qianduopay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'image',  //response二维码key
        'appPath'   => 'image',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Xiaochoupay'       => [
        'order'     => 'mer_order',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Ruiyzfpay'       => [
        'order'     => 'commercialOrderNo',  //固定参数，订单号
        'amount'    => 'payAmount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'result',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Huiftpay'       => [
        'order'     => 'Out_Trade_No',  //固定参数，订单号
        'amount'    => 'Total_Amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'ErrCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'ErrMsg',    //获取二维码失败的错误描述字段
    ],
    'Ugpay'       => [
        'order'     => 'merchOrderSn',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Yuanczfpay'       => [
        'order'     => 'order_no',  //固定参数，订单号
        'amount'    => 'pay_money',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'return_code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Newpandapay'   => [
        'order'     => 'orderId',  //固定参数，订单号
        'amount'    => 'orderAmt',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Chaofupay'       => [
        'order'     => 'order_id',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Chaojmlpay'       => [
        'order'     => 'outOrderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'returnUrl',  //response二维码key
        'appPath'   => 'returnUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Lianbangpay'       => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'retCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'retMsg',    //获取二维码失败的错误描述字段
    ],
    'Snapay'        => [
        'order'     => 'traceno',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'barCode',  //response二维码key
        'appPath'   => 'barCode',  //response手机端h5链接key
        'errorCode' => 'respCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Lanhaipay'       => [
        'order'     => 'ordernumber',  //固定参数，订单号
        'amount'    => 'paymoney',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'success',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'responsedesc',    //获取二维码失败的错误描述字段
    ],
    'Yinengpay'       => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'state',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Niaocaopay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'data',  //response二维码key
        'appPath'   => 'data',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'OKzfthreepay'       => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'state',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'mayivszfpay'       => [
        'order'     => 'orderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Wuyouvpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'url',  //response二维码key
        'appPath'   => 'url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Dawangpay'       => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'payment_url',  //response二维码key
        'appPath'   => 'payment_url',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Shunfapay'       => [
        'order'     => 'outTradeNo',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Quancftwopay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'qrcode',  //response二维码key
        'appPath'   => 'qrcode',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Hengjiupay'       => [
        'order'     => 'orderid',  //固定参数，订单号
        'amount'    => 'txnAmt',    //固定参数，金额
        'qrPath'    => 'codeUrl',  //response二维码key
        'appPath'   => 'codeUrl',  //response手机端h5链接key
        'errorCode' => 'resultCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'resultMsg',    //获取二维码失败的错误描述字段
    ],
    'Hengyipay'       => [
        'order'     => 'pay_orderid',  //固定参数，订单号
        'amount'    => 'pay_amount',    //固定参数，金额
        'qrPath'    => 'pay_url',  //response二维码key
        'appPath'   => 'pay_url',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Hongzspay'       => [
        'order'     => 'outOrderId',  //固定参数，订单号
        'amount'    => 'orderFund',    //固定参数，金额
        'qrPath'    => 'pcUrl',  //response二维码key
        'appPath'   => 'pcUrl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
    'Jiudingpay'       => [
        'order'     => 'order',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'status',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'message',    //获取二维码失败的错误描述字段
    ],
    'Yilianzfpay'       => [
        'order'     => 'mchOrderNo',  //固定参数，订单号
        'amount'    => 'amount',    //固定参数，金额
        'qrPath'    => 'payUrl',  //response二维码key
        'appPath'   => 'payUrl',  //response手机端h5链接key
        'errorCode' => 'returnCode',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'returnMsg',    //获取二维码失败的错误描述字段
    ],
    'Xunbaovzfpay'       => [
        'order'     => 'out_trade_no',  //固定参数，订单号
        'amount'    => 'money',    //固定参数，金额
        'qrPath'    => 'payurl',  //response二维码key
        'appPath'   => 'payurl',  //response手机端h5链接key
        'errorCode' => 'code',    //获取二维码失败的状态吗字段
        'errorMsg'  => 'msg',    //获取二维码失败的错误描述字段
    ],
];
