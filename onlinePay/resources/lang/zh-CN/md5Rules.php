<?php
/**
 * 验签规则
 */

return [
    'Miaotongpay' => [
        'space'    => '',
        'singRule' => "{@-merchantId-@}{@-corp_flow_no-@}{@-reqMsgId-@}{@-respType-@}{@-PKMd5Key-@}"
    ],
    'Jiulongpay' => [
        'space'    => '&',
        'singRule' => "merchNo={@-merchNo-@}&merchOrder={@-merchOrder-@}&orderAmount={@-orderAmount-@}&status={@-status-@}&timestamp={@-timestamp-@}&key={@-PKMd5Key-@}"
    ],
    'Mianqianpay'  => [
        'space'    => '&',
        'singRule' => "merchant_id={@-merchant_id-@}&fees={@-fees-@}&order_amount={@-order_amount-@}&out_trade_no={@-out_trade_no-@}&pay_amount={@-pay_amount-@}&pay_time={@-pay_time-@}&paytype={@-paytype-@}&status={@-status-@}&trade_no={@-trade_no-@}{@-PKMd5Key-@}"
    ],
    'YifutongBpay' => [
        'space'    => '',
        'singRule' => "{@-orderid-@}{@-orderuid-@}{@-yftNo-@}{@-price-@}{@-istype-@}{@-realprice-@}{@-createAt-@}{@-uid-@}{@-PKMd5Key-@}{@-notifyurl-@}"
    ],
    'Jinbozfpay'  => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&type={@-type-@}&key={@-PKMd5Key-@}"
    ],
    'Wangwpay'  => [
        'space'    => '&',
        'singRule' => "out_trade_no={@-out_trade_no-@}&paysucessdate={@-paysucessdate-@}&total_fee={@-total_fee-@}&trade_no={@-trade_no-@}&tradingfee={@-tradingfee-@}&key={@-PKMd5Key-@}"
    ],
    'Wanhuipay'  => [
    'space'    => '&',
    'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Balingyipay'  => [
        'space'    => '&',
        'singRule' => "out_trade_no={@-out_trade_no-@}&total_amount={@-total_amount-@}&trade_status={@-trade_status-@}{@-PKMd5Key-@}"
    ],
    'Baodepay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Xindongfangpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&attach={@-attach-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Tiandipay'  => [
        'space'    => '&',
        'singRule' => "merId={@-merId-@}&merParam={@-merParam-@}&orderAmount={@-orderAmount-@}&paySerialNo={@-paySerialNo-@}&prdOrNo={@-prdOrNo-@}&payTime={@-payTime-@}&returnCode={@-returnCode-@}&key={@-PKMd5Key-@}"
    ],
    'Abcpay'  => [
        'space'    => '',
        'singRule' => "{@-xxorder-@}{@-order-@}{@-paytype-@}{@-fee-@}{@-paytm-@}{@-PKMd5Key-@}"
    ],
    'Jiabaopay'  => [
        'space'    =>  '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Zeshengpay'  => [
        'space'    => '&',
        'singRule' => "instructCode={@-instructCode-@}&merchantCode={@-merchantCode-@}&outOrderId={@-outOrderId-@}&totalAmount={@-totalAmount-@}&transTime={@-transTime-@}&transType={@-transType-@}&KEY={@-PKMd5Key-@}"
    ],
    'Yunanpay'    => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Ankuaipay'   => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Gaotongpay'  => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Shanfupay'   => [
        'space'    => '~|~',
        'singRule' => "MemberID={@-MemberID-@}~|~TerminalID={@-TerminalID-@}&TransID={@-TransID-@}~|~Result={@-Result-@}~|~ResultDesc={@-ResultDesc-@}~|~FactMoney={@-FactMoney-@}~|~AdditionalInfo={@-AdditionalInfo-@}~|~SuccTime={@-SuccTime-@}~|~Md5Sign={@-PKMd5Key-@}"
    ],
    'Likepay'     => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Luobopay'    => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Mobaopay'    => [
        'space'    => '&',
        'singRule' => "apiName={@-apiName-@}&apiVersion={@-apiVersion-@}&platformID={@-platformID-@}&merchNo={@-merchNo-@}&orderNo={@-orderNo-@}&tradeDate={@-tradeDate-@}&amt={@-amt-@}&merchUrl={@-merchUrl-@}&merchParam={@-merchParam-@}&tradeSummary={@-tradeSummary-@}{@-PKMd5Key-@}"
    ],
    'Dingyipay'   => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Anxinpay'    => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}&sysnumber={@-sysnumber-@}{@-PKMd5Key-@}"
    ],
    'Huibaopay'   => [
        'space'    => '&',
        'singRule' => "balanceCurrName={@-balanceCurrName-@}&merId={@-merId-@}&money={@-money-@}&notifyType={@-notifyType-@}&"
            . "orderId={@-orderId-@}&order_state={@-order_state-@}&payCurrName={@-payCurrName-@}&payOrderId={@-payOrderId-@}&payReturnTime={@-payReturnTime-@}&"
            . "payType={@-payType-@}&key={@-PKMd5Key-@}"
    ],
    'Heyipay'     => [
        'space'    => '&',
        'singRule' => "apiName={@-apiName-@}&apiVersion={@-apiVersion-@}&platformID={@-platformID-@}&merchNo={@-merchNo-@}&orderNo={@-orderNo-@}&tradeDate={@-tradeDate-@}&amt={@-amt-@}&merchUrl={@-merchUrl-@}&merchParam={@-merchParam-@}&tradeSummary={@-tradeSummary-@}{@-PKMd5Key-@}"
    ],
    'Xinbeipay'   => [
        'space'    => '',
        'singRule' => "Version=[{@-Version-@}]MerchantCode=[{@-MerchantCode-@}]OrderId=[{@-OrderId-@}]orderDate=[{@-orderDate-@}]SerialNo=[{@-SerialNo-@}]Amount=[{@-Amount-@}]PayCode=[{@-PayCode-@}]State=[{@-State-@}]FinishTime=[{@-FinishTime-@}]TokenKey=[{@-PKMd5Key-@}]"
    ],
    'Yuebaopay'   => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Vvvpay'      => [
        'space'    => '&',
        'singRule' => "channelOrderId={@-channelOrderId-@}&key={@-PKMd5Key-@}&orderId={@-orderId-@}&timeStamp={@-timeStamp-@}&totalFee={@-totalFee-@}"
    ],
    'Duobaopay'   => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Xinyizhipay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&orderNo={@-orderNo-@}&transactionNo={@-transactionNo-@}#{@-PKMd5Key-@}"
    ],
    'AImispay'    => [
        'space'    => '&',
        'singRule' => "cancel={@-cancel-@}&fee_type={@-fee_type-@}&goods_detail={@-goods_detail-@}&goods_name={@-goods_name-@}&mchid={@-mchid-@}&order_status={@-order_status-@}&order_type={@-order_type-@}&orig_trade_no={@-orig_trade_no-@}&out_mchid={@-out_mchid-@}&pay_time={@-pay_time-@}&src_code={@-src_code-@}&time_expire={@-time_expire-@}&time_start={@-time_start-@}&trade_no={@-trade_no-@}&trade_type={@-trade_type-@}&key={@-PKMd5Key-@}"
    ],

    'Yuanbaopay'    => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Huihepay'      => [
        'space'    => '&',
        'singRule' => "AppId={@-AppId-@}&Code={@-Code-@}&OutTradeNo={@-OutTradeNo-@}&TotalAmount={@-TotalAmount-@}&TradeNo={@-TradeNo-@}{@-PKMd5Key-@}"
    ],
    'Ludepay'       => [
        'space'    => '&',
        'singRule' => "service={@-service-@}&merId={@-merId-@}&tradeNo={@-tradeNo-@}&tradeDate={@-tradeDate-@}&opeNo={@-opeNo-@}"
            . "&opeDate={@-opeDate-@}&amount={@-amount-@}&status={@-status-@}&extra={@-extra-@}&payTime={@-payTime-@}{@-PKMd5Key-@}"
    ],
    'Xunfupay'      => [
        'space'    => '&',
        'singRule' => "amount=>{@-amount-@}&datetime=>{@-datetime-@}&memberid=>{@-memberid-@}&orderid=>{@-orderid-@}&returncode=>{@-returncode-@}&key={@-PKMd5Key-@}"
    ],
    'Heshengpay'    => [
        'space'    => '&',
        'singRule' => "service={@-service-@}&merId={@-merId-@}&tradeNo={@-tradeNo-@}&tradeDate={@-tradeDate-@}&opeNo={@-opeNo-@}&opeDate={@-opeDate-@}&amount={@-amount-@}&status={@-status-@}&extra={@-extra-@}&payTime={@-payTime-@}{@-PKMd5Key-@}"
    ],
    'Zaixianbaopay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&channelOrderno={@-channelOrderno-@}&channelTraceno={@-channelTraceno-@}"
            . "&customerno={@-customerno-@}&merchName={@-merchName-@}&merchno={@-merchno-@}&openId={@-openId-@}"
            . "&orderno={@-orderno-@}&payType={@-payType-@}&remark={@-remark-@}&status={@-status-@}&traceno={@-traceno-@}"
            . "&transDate={@-transDate-@}&transTime={@-transTime-@}&{@-PKMd5Key-@}"
    ],
    'Bohuibaopay'   => [
        'space'    => '&',
        'singRule' => "mch_id={@-mch_id-@}&out_order_no={@-out_order_no-@}&total_fee={@-total_fee-@}&trade_status={@-trade_status-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Xinhuipay'     => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Xunbaopay'     => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}&time={@-systime-@}&sysorderid={@-sysorderid-@}{@-PKMd5Key-@}"
    ],
    'Renxinpay'     => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],

    'Yiwangpay'      => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Jiandanpay'     => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&trade_no={@-trade_no-@}&total_fee={@-total_fee-@}&status={@-status-@}"
            . "&transaction_id={@-transaction_id-@}&remark={@-remark-@}&key={@-PKMd5Key-@}"
    ],
    'Yingtongbaopay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&channelOrderno={@-channelOrderno-@}&channelTraceno={@-channelTraceno-@}&merchName={@-merchName-@}&merchno={@-merchno-@}&openId={@-openId-@}&orderno={@-orderno-@}&payType={@-payType-@}&status={@-status-@}&traceno={@-traceno-@}&transDate={@-transDate-@}&transTime={@-transTime-@}{@-PKMd5Key-@}"
    ],
    'Yunshengpay'    => [
        'space'    => '.',
        'singRule' => "success={@-success-@}&merchantid={@-merchantid-@}&billno={@-billno-@}&Amount={@-Amount-@}&orderdate={@-orderdate-@}{@-PKMd5Key-@}"
    ],
    'Jiupay'         => [
        'space'    => '&',
        'singRule' => "AGTID={@-AGTID-@}&AGTORDID={@-AGTORDID-@}&CMDID={@-CMDID-@}&CRTTIME={@-CRTTIME-@}&NTFURL={@-NTFURL-@}&PARVALUE={@-PARVALUE-@}&PAYTYPE={@-PAYTYPE-@}&USERID={@-USERID-@}{@-PKMd5Key-@}"
    ],
    'Juyuanpay'      => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],

    'Okpay' => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&partner={@-partner-@}&orderid={@-orderid-@}&payamount={@-payamount-@}&opstate={@-opstate-@}&orderno={@-orderno-@}&okfpaytime={@-okfpaytime-@}&message={@-message-@}&paytype={@-paytype-@}&remark={@-remark-@}&key={@-PKMd5Key-@}"
    ],

    'Mangguopay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&code={@-code-@}&down_sn={@-down_sn-@}&fee={@-fee-@}&msg={@-msg-@}&order_sn={@-order_sn-@}&status={@-status-@}&trans_time={@-trans_time-@}&key={@-PKMd5Key-@}"
    ],
    'Huichaopay' => [
        'space'    => '&',
        'singRule' => "MerNo={@-MerNo-@}&BillNo={@-BillNo-@}&OrderNo={@-OrderNo-@}&Amount={@-Amount-@}&Succeed={@-Succeed-@}&{@-PKMd5Key-@}"
    ],
    'Ebaopay'    => [
        'space'    => '',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&key={@-PKMd5Key-@}"
    ],
    'Jinyinpay'  => [
        'space'    => '&',
        'singRule' => "body={@-body-@}&merchant_no={@-merchant_no-@}&notify_time={@-notify_time-@}&obtain_fee={@-obtain_fee-@}&out_trade_no={@-out_trade_no-@}&payment_bank={@-payment_bank-@}&payment_type={@-payment_type-@}&total_fee={@-total_fee-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&version={@-version-@}{@-PKMd5Key-@}"
    ],
    'Ladengpay'  => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],

    'Kuaidapay'       => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Longzhifupay'    => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Ruifupay'        => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Haolianpay'      => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Wanfubaopay'     => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Lezhifupay'      => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Kusutxtpay'      => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Qianlongtongpay' => [
        'space'    => '&',
        'singRule' => "appid={@-appid-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Jinyangpay'      => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Fivefupay'       => [
        'space'    => '.',
        'singRule' => "{@-amt-@}&{@-merData-@}&{@-merchOrderId-@}&{@-orderId-@}&{@-orderStatusMsg-@}&{@-status-@}&{@-tranTime-@}{@-PKMd5Key-@}"
    ],
    'Yzjhpay'         => [
        'space'    => '.',
        'singRule' => "merchantOutOrderNo={@-merchantOutOrderNo-@}&merid={@-merid-@}&noncestr={@-noncestr-@}&orderNo={@-orderNo-@}&payMoney={@-payMoney-@}&payResult={@-payResult-@}{@-PKMd5Key-@}"
    ],
    'Quanqiuzfpay'    => [
        'space'    => '&',
        'singRule' => "currency={@-currency-@}&mer_no={@-mer_no-@}&mer_order_no={@-mer_order_no-@}&mer_return_msg={@-mer_return_msg-@}&order_date={@-order_date-@}&order_no={@-order_no-@}&pay_date={@-pay_date-@}&sign_type={@-sign_type-@}&trade_amount={@-trade_amount-@}&trade_result={@-trade_result-@}{@-PKMd5Key-@}"
    ],
    'Bofubaopay'      => [
        'space'    => '&',
        'singRule' => "mch_id={@-mch_id-@}&out_order_no={@-out_order_no-@}&total_fee={@-total_fee-@}&trade_status={@-trade_status-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Yiyunhpay'       => [
        'space'    => '&',
        'singRule' => "curCode={@-curCode-@}&cxOrderNo={@-cxOrderNo-@}&dealCode={@-dealCode-@}&dealMsg={@-dealMsg-@}&dealTime={@-dealTime-@}&ext1={@-ext1-@}&ext2={@-ext2-@}&fee={@-fee-@}&merchantNo={@-merchantNo-@}&orderAmount={@-orderAmount-@}&orderNo={@-orderNo-@}&orderTime={@-orderTime-@}&payChannelCode={@-payChannelCode-@}&productName={@-productName-@}&signType={@-signType-@}&version={@-version-@}{@-PKMd5Key-@}"
    ],
    'Rongcaipay'      => [
        'space'    => '&',
        'singRule' => "memo={@-memo-@}&merNo={@-merNo-@}&notifyUrl={@-notifyUrl-@}&orderDate={@-orderDate-@}&orderNo={@-orderNo-@}&payId={@-payId-@}&payTime={@-payTime-@}&productId={@-productId-@}&respCode={@-respCode-@}&respDesc={@-respDesc-@}&transAmt={@-transAmt-@}&transId={@-transId-@}{@-PKMd5Key-@}"
    ],
    'Wbypay'          => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Geilipay'        => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&app_id={@-app_id-@}&order_id={@-order_id-@}&state={@-state-@}&trade_no={@-trade_no-@}{@-PKMd5Key-@}"
    ],

    'Ruyipay' => [
        'space'    => '|',
        'singRule' => "{@-P_UserID-@}{@-P_OrderID-@}{@-P_CardID-@}{@-P_CardPass-@}{@-P_FaceValue-@}{@-P_ChannelID-@}{@-P_PayMoney-@}{@-P_ErrCode-@}{@-PKMd5Key-@}"
    ],

    'Sanshiepay' => [
        'space'    => '|',
        'singRule' => "{@-P_UserId-@}{@-P_OrderId-@}{@-P_CardId-@}{@-P_CardPass-@}{@-P_FaceValue-@}{@-P_ChannelId-@}{@-P_PayMoney-@}{@-P_ErrCode-@}{@-PKMd5Key-@}"
    ],

    'Yishengfupay' => [
        'space'    => '&',
        'singRule' => "apiName={@-apiName-@}&notifyTime={@-notifyTime-@}&tradeAmt={@-tradeAmt-@}&merchNo={@-merchNo-@}&merchParam={@-merchParam-@}&orderNo={@-orderNo-@}&tradeDate={@-tradeDate-@}&accNo={@-accNo-@}&accDate={@-accDate-@}&orderStatus={@-orderStatus-@}{@-PKMd5Key-@}"
    ],

    'Yjpay' => [
        'space'    => '&',
        'singRule' => "{@-payId-@}{@-merId-@}{@-ordidState-@}{@-PKMd5Key-@}"
    ],

    'Zhangtuopay' => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],

    'Zhongbaotong' => [
        'space'    => '&',
        'singRule' => "apiName={@-apiName-@}&notifyTime={@-notifyTime-@}&tradeAmt={@-tradeAmt-@}&merchNo={@-merchNo-@}&merchParam={@-merchParam-@}&orderNo={@-orderNo-@}&tradeDate={@-tradeDate-@}&accNo={@-accNo-@}&accDate={@-accDate-@}&orderStatus={@-orderStatus-@}{@-PKMd5Key-@}"
    ],

    'Fenghuangpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&app_id={@-app_id-@}&order_id={@-order_id-@}&state={@-state-@}&trade_no={@-trade_no-@}{@-PKMd5Key-@}"
    ],

    'Skyzepay' => [
        'space'    => '&',
        'singRule' => "{@-p1_MerId-@}{@-r0_Cmd-@}{@-r1_Code-@}{@-r2_TrxId-@}{@-r3_Amt-@}{@-r4_Cur-@}{@-r5_Pid-@}{@-r6_Order-@}{@-r7_Uid-@}{@-r8_MP-@}{@-r9_BType-@}{@-PKMd5Key-@}"
    ],

    'Gaoftpay'         => [
        'space'    => '&',
        'singRule' => "merchantNo={@-merchantNo-@}&orderAmount={@-orderAmount-@}&orderNo={@-orderNo-@}&orderStatus={@-orderStatus-@}&payTime={@-payTime-@}&productDesc={@-productDesc-@}&productName={@-productName-@}&remark={@-remark-@}&wtfOrderNo={@-wtfOrderNo-@}{@-PKMd5Key-@}"
    ],
    'Yiyifupay'        => [
        'space'    => '&',
        'singRule' => "goods_name={@-goods_name-@}&out_trade_no={@-out_trade_no-@}&src_code={@-src_code-@}&time_start={@-time_start-@}&total_fee={@-total_fee-@}&trade_type={@-trade_type-@}{@-PKMd5Key-@}"
    ],
    'Neihanpay'        => [
        'space'    => '&',
        'singRule' => "merchantnumber={@-merchantnumber-@}&three_order={@-three_order-@}&is_pay={@-is_pay-@}&order_money={@-order_money-@}&pay_money={@-pay_money-@}&pay_type={@-pay_type-@}&pay_time={@-pay_time-@}{@-PKMd5Key-@}"
    ],
    'Suiyipay'         => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Sansanpay'        => [
        'space'    => '.',
        'singRule' => "orderid={@-orderid-@}&orderuid={@-orderuid-@}&platform_trade_no={@-platform_trade_no-@}&ordno={@-ordno-@}&price={@-price-@}&realprice={@-realprice-@}{@-PKMd5Key-@}"
    ],
    'Hongqipay'        => [
        'space'    => '.',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}{@-PKMd5Key-@}"
    ],
    'Chiniaopay'       => [
        'space'    => '.',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}{@-PKMd5Key-@}"
    ],
    'Eightkingpay'     => [
        'space'    => '&',
        'singRule' => "bank_type={@-bank_type-@}&cid={@-cid-@}&orderid={@-orderid-@}&out_trade_no={@-out_trade_no-@}&out_transaction_id={@-out_transaction_id-@}&paytype={@-paytype-@}&result_code={@-result_code-@}&status={@-status-@}&total_fee={@-total_fee-@}&transaction_id={@-transaction_id-@}{@-PKMd5Key-@}"
    ],
    'Tigerpay'         => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Qilinpay'         => [
        'space'    => '&',
        'singRule' => "{@-orderid-@}{@-orderuid-@}{@-paysapi_id-@}{@-price-@}{@-realprice-@}{@-PKMd5Key-@}"
    ],
    'Yiyunpay'         => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Shuanghuipay'     => [
        'space'    => '&',
        'singRule' => "accFlag={@-accFlag-@}&accName={@-accName-@}&amount={@-amount-@}&createTime={@-createTime-@}&currentTime={@-currentTime-@}&merchant={@-merchant-@}&orderNo={@-orderNo-@}&payFlag={@-payFlag-@}&payTime={@-payTime-@}&payType={@-payType-@}&remark={@-remark-@}&systemNo={@-systemNo-@}#{@-PKMd5Key-@}"
    ],
    'Zhenhaopay'       => [
        'space'    => '&',
        'singRule' => "status={@-status-@}&msg={@-msg-@}&data={@-data-@}&payUrl={@-payUrl-@}&nonceStr={@-nonceStr-@}&timestamp={@-timestamp-@}{@-PKMd5Key-@}"
    ],
    'Shouqianbaopay'   => [
        'space'    => '&',
        'singRule' => "method={@-method-@}&recode={@-recode-@}&reMsg={@-reMsg-@}&channelType={@-channelType-@}&merchId={@-merchId-@}&orderNo={@-orderNo-@}&tradeNo={@-tradeNo-@}&qcodeUrl={@-qcodeUrl-@}&payMoney={@-payMoney-@}{@-PKMd5Key-@}"
    ],
    'Jincaihuipay'     => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Rongxinfupay'     => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Shoujiepay'       => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&ordernumber={@-ordernumber-@}&paymoney={@-paymoney-@}&paytype={@-paytype-@}{@-PKMd5Key-@}"
    ],
    'Shangxinpay'      => [
        'space'    => '&',
        'singRule' => "{@-orderId-@}{@-merchOrderId-@}{@-amount-@}{@-payTime-@}{@-PKMd5Key-@}"
    ],
    'Zhongxinyunpay'   => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&ordernumber={@-ordernumber-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}{@-PKMd5Key-@}"
    ],
    'Jingzhunpay'      => [
        'space'    => '&',
        'singRule' => "apiName={@-apiName-@}&notifyTime={@-notifyTime-@}&tradeAmt={@-tradeAmt-@}&merchNo={@-merchNo-@}&merchParam={@-merchParam-@}&orderNo={@-orderNo-@}&tradeDate={@-tradeDate-@}&accNo={@-accNo-@}&accDate={@-accDate-@}&orderStatus={@-orderStatus-@}{@-PKMd5Key-@}"
    ],
    'Juchuangpay'      => [
        'space'    => '&',
        'singRule' => "sign={@-sign-@}&outOrderNo={@-outOrderNo-@}&goodsClauses={@-goodsClauses-@}&tradeAmount={@-tradeAmount-@}&code={@-code-@}&payCode={@-payCode-@}{@-PKMd5Key-@}"
    ],
    'Yifushunpay'      => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}{@-PKMd5Key-@}"
    ],
    'Suibianpay'       => [
        'space'    => '&',
        'singRule' => "trade_no={@-trade_no-@}&trade_type={@-trade_type-@}&time_start={@-time_start-@}&pay_time={@-pay_time-@}&total_fee={@-total_fee-@}&goods_name={@-goods_name-@}&src_code={@-src_code-@}&out_trade_no={@-out_trade_no-@}&order_status={@-order_status-@}&pay_params={@-pay_params-@}{@-PKMd5Key-@}"
    ],
    'Jinmupay'         => [
        'space'    => '&',
        'singRule' => "errcode={@-errcode-@}&orderno={@-orderno-@}&total_fee={@-total_fee-@}&attach={@-attach-@}{@-PKMd5Key-@}"
    ],
    'Baiqianpay'       => [
        'space'    => '&',
        'singRule' => "Amount={@-Amount-@}&BillNo={@-BillNo-@}&MerNo={@-MerNo-@}&Succeed={@-Succeed-@}{@-PKMd5Key-@}"
    ],
    'Yilexiangpay'     => [
        'space'    => '&',
        'singRule' => "return_code={@-return_code-@}&return_msg={@-return_msg-@}&money={@-money-@}&pay_status={@-pay_status-@}&pay_time={@-pay_time-@}&trade_no={@-trade_no-@}&attach={@-attach-@}&cust_no={@-cust_no-@}&pay_channel={@-pay_channel-@}&order_id={@-order_id-@}&plat_order_no ={@-plat_order_no -@}&mch_order_no={@-mch_order_no-@}{@-PKMd5Key-@}"
    ],
    'Yifulianpay'      => [
        'space'    => '&',
        'singRule' => "merchantId={@-merchantId-@}&orderNo={@-orderNo-@}&orderAmount={@-orderAmount-@}&paymentDatetime={@-paymentDatetime-@}&dealId={@-dealId-@}&status={@-status-@}{@-PKMd5Key-@}"
    ],
    'Benzhipay'        => [
        'space'    => '&',
        'singRule' => "merchantId={@-merchantId-@}&orderNo={@-orderNo-@}&orderAmount={@-orderAmount-@}&paymentDatetime={@-paymentDatetime-@}&dealId={@-dealId-@}&status={@-status-@}{@-PKMd5Key-@}"
    ],
    'Xunyoutongpay'    => [
        'space'    => '&',
        'singRule' => "payKey={@-payKey-@}&productName={@-productName-@}&productType={@-productType-@}&orderPrice={@-orderPrice-@}&orderTime={@-orderTime-@}&status={@-status-@}&outTradeNo={@-outTradeNo-@}&tradeStatus={@-tradeStatus-@}&trxNo={@-trxNo-@}&successTime={@-successTime-@}&remark={@-remark-@}{@-PKMd5Key-@}"
    ],
    'Weibaopay'        => [
        'space'    => '&',
        'singRule' => "sErrorCode={@-sErrorCode-@}&bType={@-bType-@}&ForUserId={@-ForUserId-@}&LinkID={@-LinkID-@}&Moneys={@-Moneys-@}&AssistStr={@-AssistStr-@}&keyValue={@-PKMd5Key-@}"
    ],
    'Tudoupay'         => [
        'space'    => '&',
        'singRule' => "merchantCode={@-merchantCode-@}&message={@-message-@}&interfaceVersion={@-interfaceVersion-@}&qrcode={@-qrcode-@}&status={@-status-@}{@-PKMd5Key-@}"
    ],
    'Jumipay'          => [
        'space'    => '&',
        'singRule' => "total_fee={@-total_fee-@}&order_no={@-order_no-@}&out_trade_no={@-out_trade_no-@}&mch_id={@-mch_id-@}&datetime={@-datetime-@}&attach={@-attach-@}{@-PKMd5Key-@}"
    ],
    'Jinjupay'         => [
        'space'    => '&',
        'singRule' => "status={@-status-@}&charset={@-charset-@}&transactionid={@-transactionid-@}&outtransactionid={@-outtransactionid-@}&outorderno={@-outorderno-@}&totalfee={@-totalfee-@}&mchid={@-mchid-@}{@-PKMd5Key-@}"
    ],
    'Shangfuyunpay'    => [
        'space'    => '&',
        'singRule' => "message={@-message-@}&detail={@-detail-@}&code={@-code-@}&desc={@-desc-@}&opeNo={@-opeNo-@}&opeDate={@-opeDate-@}&sessionID={@-sessionID-@}{@-PKMd5Key-@}"
    ],
    'Kbaopay'          => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&record={@-record-@}{@-PKMd5Key-@}"
    ],
    'Ywbpay'           => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&customer_id={@-customer_id-@}&money={@-money-@}&order_no={@-order_no-@}&callback_url={@-callback_url-@}&sync_url={@-sync_url-@}{@-PKMd5Key-@}"
    ],
    'Tongbaopay'       => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&customer_id={@-customer_id-@}&total_fee={@-total_fee-@}&sdorderno={@-sdorderno-@}&notifyurl={@-notifyurl-@}&returnurl={@-returnurl-@}{@-PKMd5Key-@}"
    ],
    'Xinyupay'         => [
        'space'    => '|',
        'singRule' => "V={@-V-@}|UserNo={@-UserNo-@}|ordNo={@-ordNo-@}|ordTime={@-ordTime-@}|amount={@-amount-@}|pid={@-pid-@}|notifyUrl={@-notifyUrl-@}|frontUrl={@-frontUrl-@}|remark={@-remark-@}|ip={@-ip-@}{@-PKMd5Key-@}"
    ],
    'Jinbaopay'        => [
        'space'    => '|',
        'singRule' => "merchant_no={@-merchant_no-@}&version={@-version-@}&out_trade_no={@-out_trade_no-@}&payment_type={@-payment_type-@}&payment_bank={@-payment_bank-@}&notify_url={@-notify_url-@}&page_url={@-page_url-@}&total_fee={@-total_fee-@}&trade_time={@-trade_time-@}&user_account={@-user_account-@}&body={@-body-@}&channel={@-channel-@}{@-PKMd5Key-@}"
    ],
    'Kuaichongpay'     => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&app_id={@-app_id-@}&order_id={@-order_id-@}&state={@-state-@}&trade_no={@-trade_no-@}{@-PKMd5Key-@}"
    ],
    'Baozepay'         => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}{@-PKMd5Key-@}"
    ],
    'Xinganxianpay'    => [
        'space'    => '&',
        'singRule' => "merchant_id={@-merchant_id-@}&source_order_id={@-source_order_id-@}&order_amount={@-order_amount-@}&goods_name={@-goods_name-@}&payTime={@-payTime-@}{@-PKMd5Key-@}"
    ],
    'Kuairubaopay'     => [
        'space'    => '&',
        'singRule' => "paysapi_id={@-paysapi_id-@}&orderid={@-orderid-@}&price={@-price-@}&realprice={@-realprice-@}&orderuid={@-orderuid-@}{@-PKMd5Key-@}"
    ],
    'Baifupay'         => [
        'space'    => '&',
        'singRule' => "merchantNo={@-merchantNo-@}&netwayCode={@-netwayCode-@}&orderNum={@-orderNum-@}&payAmount={@-payAmount-@}&goodsName={@-goodsName-@}&resultCode={@-resultCode-@}&payDate={@-payDate-@}{@-PKMd5Key-@}"
    ],
    'Huiyinpay'        => [
        'space'    => '&',
        'singRule' => "merchantcode={@-merchantcode-@}&orderid={@-orderid-@}&status={@-status-@}&amount={@-amount-@}&paytime={@-paytime-@}&key={@-PKMd5Key-@}"
    ],
    'Yunbeipay'        => [
        'space'    => '_',
        'singRule' => "{@-companyId-@}_{@-userOrderId-@}_{@-fee-@}_{@-PKMd5Key-@}"
    ],
    'Bangyinpay'       => [
        'space'    => '&',
        'singRule' => "merId={@-merId-@}&finalOrderId={@-finalOrderId-@}&merOrderId={@-merOrderId-@}&succTime={@-succTime-@}&transAmt={@-transAmt-@}&respCode={@-respCode-@}{@-PKMd5Key-@}"
    ],
    'Hengfubaopay'     => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_orderid={@-pay_orderid-@}&pay_productname={@-pay_productname-@}&pay_tradetype={@-pay_tradetype-@}&pay_turnyurl={@-pay_turnyurl-@}{@-PKMd5Key-@}"
    ],
    'Gzhifupay'        => [
        'space'    => '&',
        'singRule' => "respCode={@-respCode-@}&respMsg={@-respMsg-@}&payNo={@-payNo-@}&merOrderNo={@-merOrderNo-@}&jumpUrl={@-jumpUrl-@}&realAmt={@-realAmt-@}{@-PKMd5Key-@}"
    ],
    'Xinghangpay'      => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Tianyifupay'      => [
        'space'    => '&',
        'singRule' => "apiName={@-apiName-@}&notifyTime={@-notifyTime-@}&tradeAmt={@-tradeAmt-@}&merchNo={@-merchNo-@}&merchParam={@-merchParam-@}&orderNo={@-orderNo-@}&tradeDate={@-tradeDate-@}&accNo={@-accNo-@}&accDate={@-accDate-@}&orderStatus={@-orderStatus-@}{@-PKMd5Key-@}"
    ],
    'Zhaocaibaopay'    => [
        'space'    => '&',
        'singRule' => "{@-orderNo-@}|{@-outId-@}|{@-payMoney-@}|{@-realPayMoney-@}|{@-PKMd5Key-@}"
    ],
    'Zhinengyunpay'    => [
        'space'    => '&',
        'singRule' => "{@-orderid-@}{@-orderuid-@}{@-ordno-@}{@-price-@}{@-realprice-@}{@-PKMd5Key-@}"
    ],
    'Chuangxintianpay' => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Hongbaopay'       => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Shunxinfupay'     => [
        'space'    => '&',
        'singRule' => "moboAccount={@-moboAccount-@}&respData={@-respData-@}&respCode={@-respCode-@}&respDesc={@-respDesc-@}&codeUrl={@-codeUrl-@}{@-PKMd5Key-@}"
    ],
    'Fuyingtongpay'    => [
        'space'    => '|',
        'singRule' => "{@-tradeNo-@}|{@-desc-@}|{@-time-@}|{@-userid-@}|{@-amount-@}|{@-status-@}|{@-type-@}|{@-PKMd5Key-@}"
    ],
    'Gojuhepay'        => [
        'space'    => '&',
        'singRule' => "orderAmount={@-orderAmount-@}&memberid={@-memberid-@}&goodsno={@-goodsno-@}&paycode={@-paycode-@}&goodsunitprice={@-goodsunitprice-@}&goodsname={@-goodsname-@}&timestamp={@-timestamp-@}&status={@-status-@}{@-PKMd5Key-@}"
    ],
    'Yingfupay'        => [
        'space'    => '&',
        'singRule' => "memberid={@-memberid-@}&orderid={@-orderid-@}&amount={@-amount-@}&datetime={@-datetime-@}&transaction_id={@-transaction_id-@}&returncode={@-returncode-@}{@-PKMd5Key-@}"
    ],
    'Wangfutongpay'    => [
        'space'    => '&',
        'singRule' => "MerId={@-MerId-@}&OrdId={@-OrdId-@}&OrdAmt={@-OrdAmt-@}&OrdNo={@-OrdNo-@}&ResultCode={@-ResultCode-@}&Remark={@-Remark-@}&SignType={@-SignType-@}{@-PKMd5Key-@}"
    ],
    'Yikuaipay'        => [
        'space'    => '&',
        'singRule' => "r0_Cmd={@-r0_Cmd-@}&r1_Code={@-r1_Code-@}&r2_TrxId={@-r2_TrxId-@}&r3_Amt={@-r3_Amt-@}&r4_Cur={@-r4_Cur-@}&r5_Order={@-r5_Order-@}&r6_Type={@-r6_Type-@}&p1_MerId={@-p1_MerId-@}{@-PKMd5Key-@}"
    ],
    'Yintongbaopay'    => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}{@-PKMd5Key-@}"
    ],
    'Eshidaipay'       => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&charset={@-charset-@}&sign_type={@-sign_type-@}&status={@-status-@}&message={@-message-@}&result_code={@-result_code-@}&merchantid={@-merchantid-@}&storeNumber={@-storeNumber-@}&nonce_str={@-nonce_str-@}&err_code={@-err_code-@}&err_msg={@-err_msg-@}&service={@-service-@}&total_fee={@-total_fee-@}&orderid={@-orderid-@}&remarks={@-remarks-@}&mch_create_ip={@-mch_create_ip-@}&shoporderId={@-shoporderId-@}{@-PKMd5Key-@}"
    ],
    'Jiandanfupay'     => [
        'space'    => '&',
        'singRule' => "merchantId={@-merchantId-@}&orderNo={@-orderNo-@}&tradeDate={@-tradeDate-@}&tradeTime={@-tradeTime-@}&amount={@-amount-@}&resultUrl={@-resultUrl-@}&attach={@-attach-@}&repCode={@-repCode-@}&repMsg={@-repMsg-@}{@-PKMd5Key-@}"
    ],
    'Xiaomayipay'      => [
        'space'    => '&',
        'singRule' => "transDate={@-transDate-@}&transTime={@-transTime-@}&merchno={@-merchno-@}&amount={@-amount-@}&traceno={@-traceno-@}&payType={@-payType-@}&status={@-status-@}&payAmt={@-payAmt-@}{@-PKMd5Key-@}"
    ],
    'Dabofupay'        => [
        'space'    => '&',
        'singRule' => "success={@-success-@}&data={@-data-@}&bank_url={@-bank_url-@}&order_no={@-order_no-@}&order_time={@-order_time-@}&remark={@-remark-@}&status={@-status-@}&pay_time={@-pay_time-@}&nonce_str={@-nonce_str-@}{@-PKMd5Key-@}"
    ],
    'Rujinfupay'       => [
        'space'    => '&',
        'singRule' => "{@-PKMd5Key-@}{@-record-@}{@-money-@}"
    ],
    'Boshipay'         => [
        'space'    => '&',
        'singRule' => "MerchantCode=[{@-MerchantCode-@}]OrderId=[{@-OrderId-@}]OutTradeNo=[{@-OutTradeNo-@}]Amount=[{@-Amount-@}]OrderDate=[{@-OrderDate-@}]BankCode=[{@-BankCode-@}]Remark=[{@-Remark-@}]Status=[{@-Status-@}]Time=[{@-Time-@}]TokenKey=[{@-PKMd5Key-@}]"
    ],
    'Xinlupay'         => [
        'space'    => '&',
        'singRule' => "orderNo={@-orderNo-@}&payAmt={@-payAmt-@}&retCode={@-retCode-@}&transNo={@-transNo-@}&userId={@-userId-@}&KEY={@-PKMd5Key-@}"
    ],
    'Qijipay'          => [
        'space'    => '&',
        'singRule' => "account={@-account-@}&amount={@-amount-@}&orderno={@-orderno-@}&tradeno={@-tradeno-@}&tradestatus={@-tradestatus-@}#{@-PKMd5Key-@}"
    ],
    'Xinfapay'         => [
        'space'    => '&',
        'singRule' => "feeAmt={@-feeAmt-@}&merId={@-merId-@}&notifyType={@-notifyType-@}&orderNo={@-orderNo-@}&payAmt={@-payAmt-@}&paycode={@-paycode-@}&productName={@-productName-@}&rtnCode={@-rtnCode-@}{@-PKMd5Key-@}"
    ],
    'Jishipay'         => [
        'space'    => '.',
        'singRule' => "{@-out_order_no-@}{@-total_fee-@}{@-trade_status-@}{@-PKMd5Key-@}"
    ],
    'Shengyfpay'       => [
        'space'    => '&',
        'singRule' => "channelNo={@-channelNo-@}&merchNo={@-merchNo-@}&pt={@-pt-@}&remark={@-remark-@}&tradeAmount={@-tradeAmount-@}&tradeDate={@-tradeDate-@}&tradeNo={@-tradeNo-@}&tradeStatus={@-tradeStatus-@}&tradeTime={@-tradeTime-@}{@-PKMd5Key-@}"
    ],
    'Longtoupay'   => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Rujinbaopay'      => [
        'space'    => '&',
        'singRule' => "merchant_no={@-merchant_no-@}&version={@-version-@}&out_trade_no={@-out_trade_no-@}&out_trade_no={@-out_trade_no-@}&payment_bank={@-payment_bank-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&notify_time={@-notify_time-@}&body={@-body-@}&total_fee={@-total_fee-@}&obtain_fee={@-obtain_fee-@}{@-PKMd5Key-@}"
    ],
    'Xinglianpay'      => [
        'space'    => '&',
        'singRule' => "payOrderId={@-payOrderId-@}&mchId={@-mchId-@}&mchOrderNo={@-mchOrderNo-@}&channelId={@-channelId-@}&amount={@-amount-@}&status={@-status-@}&param1={@-param1-@}&param2={@-param2-@}&paySuccTime={@-paySuccTime-@}{@-PKMd5Key-@}"
    ],
    'Aifupay'          => [
        'space'    => '&',
        'singRule' => "merchant_no={@-merchant_no-@}&order_no={@-order_no-@}&order_amount={@-order_amount-@}&original_amount={@-original_amount-@}&upstream_settle={@-upstream_settle-@}&result={@-result-@}&pay_time={@-pay_time-@}&trace_id={@-trace_id-@}&reserve={@-reserve-@}{@-PKMd5Key-@}"
    ],
    'Wofubaopay'       => [
        'space'    => '&',
        'singRule' => "payKey={@-payKey-@}&productName={@-productName-@}&outTradeNo={@-outTradeNo-@}&orderPrice={@-orderPrice-@}&productType={@-productType-@}&tradeStatus={@-tradeStatus-@}&successTime={@-successTime-@}&orderTime={@-orderTime-@}&trxNo={@-trxNo-@}&remark={@-remark-@}{@-PKMd5Key-@}"
    ],
    'Zhongbaopay'      => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&result={@-result-@}&amount={@-amount-@}&systemorderid={@-systemorderid-@}&completetime={@-completetime-@}&key={@-PKMd5Key-@}"
    ],
    'Shangyipay'       => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}&time={@-systime-@}&sysorderid={@-sysorderid-@}{@-PKMd5Key-@}"
    ],
    'Pkpay'            => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&merId={@-merId-@}&merOrderNo={@-merOrderNo-@}&payNo={@-payNo-@}&payStatus={@-payStatus-@}&payDate={@-payDate-@}&payTime={@-payTime-@}&orderTitle={@-orderTitle-@}&orderDesc={@-orderDesc-@}&orderAmt={@-orderAmt-@}&realAmt={@-realAmt-@}&key={@-PKMd5Key-@}"
    ],
    'Youjiupay'        => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&merId={@-merId-@}&merOrderNo={@-merOrderNo-@}&payNo={@-payNo-@}&payStatus={@-payStatus-@}&payDate={@-payDate-@}&payTime={@-payTime-@}&orderTitle={@-orderTitle-@}&orderDesc={@-orderDesc-@}&orderAmt={@-orderAmt-@}&realAmt={@-realAmt-@}&key={@-PKMd5Key-@}"
    ],
    'Jialianpay'       => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Xinzhifupay'      => [
        'space'    => '&',
        'singRule' => "bill_no={@-bill_no-@}&bill_fee={@-bill_fee-@}&key={@-PKMd5Key-@}"
    ],
    'Yinfupay'         => [
        'space'    => '&',
        'singRule' => "status={@-status-@}&result={@-result-@}&outerCode={@-outerCode-@}&transactionId={@-transactionId-@}&tradeTime={@-tradeTime-@}&fee={@-fee-@}&tradeTotal={@-tradeTotal-@}&notifyStatus={@-notifyStatus-@}&key={@-PKMd5Key-@}"
    ],
    'Yuanpay'          => [
        'space'    => '&',
        'singRule' => "{@-flow_sn-@}{@-attach-@}{@-business_order-@}{@-business_code-@}{@-flow_balance-@}{@-flow_state-@}{@-return_url-@}{@-public_key-@}{@-PKMd5Key-@}"
    ],
    'Maifuyunpay'      => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Xifupay'      => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}&{@-PKMd5Key-@}"
    ],
    'Zhongjinpay'      => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Donglipay'      => [
        'space'    => '&',
        'singRule' => "{@-tradeNo-@}{@-orderno-@}{@-Paytime-@}{@-Money-@}{@-Gateway-@}"
    ],
    'Tianzepay'      => [
        'space'    => '&',
        'singRule' => "{@-p1_MerId-@}{@-r0_Cmd-@}{@-r1_Code-@}{@-r2_TrxId-@}{@-r3_Amt-@}{@-r4_Cur-@}{@-r5_Pid-@}{@-r6_Order-@}{@-r7_Uid-@}{@-r8_MP-@}{@-r9_BType-@}"
    ],
    'Hengxinpay'      => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Bangdepay'      => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&key={@-PKMd5Key-@}"
    ],
    'Anquanpay'      => [
        'space'    => '&',
        'singRule' => "subject={@-subject-@}&body={@-body-@}&trade_status={@-trade_status-@}&total_amount={@-total_amount-@}&sysd_time={@-sysd_time-@}&trade_time={@-trade_time-@}&trade_no={@-trade_no-@}&out_trade_no={@-out_trade_no-@}&notify_time={@-notify_time-@}key={@-PKMd5Key-@}"
    ],
    'Zhichengpay'      => [
        'space'    => '&',
        'singRule' => "apiName={@-apiName-@}&notifyTime={@-notifyTime-@}&tradeAmt={@-tradeAmt-@}&merchNo={@-merchNo-@}&merchParam={@-merchParam-@}&orderNo={@-orderNo-@}&tradeDate={@-tradeDate-@}&accNo={@-accNo-@}&accDate={@-accDate-@}&orderStatus={@-orderStatus-@}{@-PKMd5Key-@}"
    ],
    'Huitongshidaipay'  => [
        'space'    => '&',
        'singRule' => "cpid={@-cpid-@}&cpparam={@-cpparam-@}&fee={@-fee-@}&ffid={@-ffid-@}&ip={@-ip-@}&status={@-status-@}&key={@-PKMd5Key-@}"
    ],
    'Mengbaopay'  => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Bajiupay'  => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&cpOrderNo={@-cpOrderNo-@}&mchNo={@-mchNo-@}&mchOrderNo={@-mchOrderNo-@}&message={@-message-@}&payResult={@-payResult-@}&resultCode={@-resultCode-@}&traceTime={@-traceTime-@}&paySecret={@-PKMd5Key-@}"
    ],
    'Randompay'  => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Fancepay'  => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Kuyoupay'  => [
        'space'    => '&',
        'singRule' => "memberGoods={@-memberGoods-@}&noticeSysaddress={@-noticeSysaddress-@}&noticeWebaddress={@-noticeWebaddress-@}&productNo={@-productNo-@}&requestAmount={@-requestAmount-@}&trxMerchantNo={@-trxMerchantNo-@}&trxMerchantOrderno={@-trxMerchantOrderno-@}&key={@-PKMd5Key-@}"
    ],
    'Rongyipay'  => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Sufupay'  => [
        'space'    => '&',
        'singRule' => "order_no={@-order_no-@}&merchant_code={@-merchant_code-@}&ade_time={@-ade_time-@}&order_time={@-order_time-@}&trade_status={@-trade_status-@}&{@-PKMd5Key-@}"
    ],
    'Jinyanggpay'  => [
        'space'    => '&',
        'singRule' => "payment_type={@-payment_type-@}&out_trade_no={@-out_trade_no-@}&trade_no={@-trade_no-@}&total_fee={@-total_fee-@}&fact_fee={@-fact_fee-@}&body={@-body-@}&order_status={@-order_status-@}&status={@-status-@}&key={@-PKMd5Key-@}"
    ],
    'Applepay'  => [
        'space'    => '&',
        'singRule' => "mer_id={@-mer_id-@}&out_trade_no={@-out_trade_no-@}&pay_type={@-pay_type-@}&real_fee={@-real_fee-@}&total_fee={@-total_fee-@}&key={@-PKMd5Key-@}"
    ],
    'Yawupay'  => [
        'space'    => '&',
        'singRule' => "pay_no={@-pay_no-@}&trade_no={@-trade_no-@}&type={@-type-@}&pay_id={@-pay_id-@}&money={@-money-@}&status={@-status-@}&createtime={@-createtime-@}&updatetime={@-updatetime-@}&paytime={@-paytime-@}{@-PKMd5Key-@}"
    ],
    'Litaobopay'  => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&merNo={@-merNo-@}&amount={@-amount-@}&tradeType={@-tradeType-@}&platOrderNo={@-platOrderNo-@}&settDate={@-settDate-@}&bindId={@-bindId-@}&status={@-status-@}&respCode={@-respCode-@}&respDesc={@-respDesc-@}&key={@-PKMd5Key-@}"
    ],
    'Xintianpay' => [
        'space'    => '&',
        'singRule' => "trade_no={@-trade_no-@}&trade_type={@-trade_type-@}&time_start={@-time_start-@}&total_fee={@-total_fee-@}&goods_name={@-goods_name-@}&goods_detail={@-goods_detail-@}&order_status={@-order_status-@}&src_code={@-src_code-@}&fee_type={@-fee_type-@}&orig_trade_no={@-orig_trade_no-@}&mchid={@-mchid-@}&pay_time={@-pay_time-@}&out_mchid={@-out_mchid-@}&cancel={@-cancel-@}&out_trade_no={@-out_trade_no-@}&time_expire={@-time_expire-@}&order_type={@-order_type-@}&key={@-PKMd5Key-@}"
    ],
    'Zfbptpay'  => [
        'space'    => '&',
        'singRule' => "merchant_order_no={@-merchant_order_no-@}&status={@-status-@}&msg={@-msg-@}&amount={@-amount-@}&prd_ord_no={@-prd_ord_no-@}&pay_channel={@-pay_channel-@}&{@-PKMd5Key-@}"
    ],
    'Chengxinpay'  => [
        'space'    => '&',
        'singRule' => "{@-pay_id-@}&{@-PKMd5Key-@}&{@-out_trade_no-@}&{@-money-@}"
    ],
    'Quicktongpay'  => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'AIpay'  => [
        'space'    => '&',
        'singRule' => "merchant_no={@-merchant_no-@}&order_no={@-order_no-@}&original_amount={@-original_amount-@}&upstream_settle={@-upstream_settle-@}&result={@-result-@}&pay_time={@-pay_time-@}&trace_id={@-trace_id-@}&reserve={@-reserve-@}&key={@-PKMd5Key-@}"
    ],
    'Yunjianpay'  => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Xxiongbpay'  => [
        'space'    => '&',
        'singRule' => "{@-orderNo-@}&{@-merchantOrderNo-@}&{@-money-@}&{@-payAmount-@}&{@-PKMd5Key-@}"
    ],
    'Yuncaipay'  => [
        'space'    => '&',
        'singRule' => "reCode={@-reCode-@}&merchantNo={@-merchantNo-@}&merchantOrderno={@-merchantOrderno-@}&paymentType={@-paymentType-@}&goods={@-goods-@}&amount={@-amount-@}&key={@-PKMd5Key-@}"
    ],
    'Htxxpay'  => [
        'space'    => '&',
        'singRule' => "orderNo={@-orderNo-@}&payAmt={@-payAmt-@}&retCode={@-retCode-@}&transNo={@-transNo-@}&userId={@-userId-@}&key={@-PKMd5Key-@}"
    ],
    'Ryunpay'  => [
        'space'    => '&',
        'singRule' => "out_trade_no={@-out_trade_no-@}&total_amount={@-total_amount-@}&trade_status={@-trade_status-@}{@-PKMd5Key-@}"
    ],
    'Jhpfpay'  => [
        'space'    => '&',
        'singRule' => "out_trade_no={@-out_trade_no-@}&total_fee={@-total_fee-@}&key={@-PKMd5Key-@}"
    ],
    'Quefupay'  => [
        'space'    => '&',
        'singRule' => "”memberid={@-”memberid-@}&orderid={@-orderid-@}&transaction_id={@-transaction_id-@}&amount={@-amount-@}&datetime={@-datetime-@}&returncode={@-returncode-@}&key={@-PKMd5Key-@}"
    ],
    'Kahuitpay'  => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&restate={@-restate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Doupay'  => [
        'space'    => '&',
        'singRule' => "instructCode={@-instructCode-@}&merchantCode={@-merchantCode-@}&outOrderId={@-outOrderId-@}&totalAmount={@-totalAmount-@}&transTime={@-transTime-@}&KEY={@-PKMd5Key-@}"
    ],
    'Zhonghtpay'  => [
        'space'    => '&',
        'singRule' => "{@-merchantId-@}{@-corp_flow_no-@}{@-reqMsgId-@}{@-respType-@}{@-PKMd5Key-@}"
    ],
    'Yixunjpay'  => [
        'space'    => '&',
        'singRule' => "orderNo={@-orderNo-@}&payAmt={@-payAmt-@}&retCode={@-retCode-@}&transNo={@-transNo-@}&userId={@-userId-@}&key={@-PKMd5Key-@}"
    ],
    'yunfumapay' => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Chengyupay' => [
        'space'    => '&',
        'singRule' => "memberid={@-memberid-@}&orderid={@-orderid-@}&amount={@-amount-@}&datetime={@-datetime-@}&returncode={@-returncode-@}&key={@-PKMd5Key-@}"
    ],
    'Yinlibaopay' => [
        'space'    => '&',
        'singRule' => "mch_id={@-mch_id-@}&nonce={@-nonce-@}&out_trade_no={@-out_trade_no-@}&platform_trade_no={@-platform_trade_no-@}&sign_type={@-sign_type-@}&key={@-PKMd5Key-@}"
    ],
    'Rongypay' => [
        'space'    => '&',
        'singRule' => "merchant_id={@-merchant_id-@}&order_id={@-order_id-@}&amount={@-amount-@}&sign={@-PKMd5Key-@}"
    ],
    'Jufuzfpay' => [
        'space'    => '&',
        'singRule' => "{@-OrderNo-@}{@-MerchantNo-@}{@-Amount-@}{@-OutTradeNo-@}{@-Status-@}{@-PKMd5Key-@}"
    ],
    'Xingxhtpay' => [
        'space'    => '&',
        'singRule' => "paysapi_id={@-paysapi_id-@}&orderid={@-orderid-@}&is_type={@-is_type-@}&price={@-price-@}&real_price={@-real_price-@}&mark={@-mark-@}&code={@-code-@}&key={@-PKMd5Key-@}"
    ],
    'Yapay' => [
        'space'    => '&',
        'singRule' => "oid={@-oid-@}&status={@-status-@}&m={@-m1-@}{@-PKMd5Key-@}"
    ],
    'Mypay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&merchantNo={@-merchantNo-@}&merId={@-merId-@}&orderNo={@-orderNo-@}&realAmount={@-realAmount-@}&tradeDate={@-tradeDate-@}&key={@-PKMd5Key-@}"
    ],
    'Yibotongpay' => [
        'space'    => '&',
        'singRule' => "code={@-code-@}&endtime={@-endtime-@}&money={@-money-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&status={@-status-@}&trade_no={@-trade_no-@}&type={@-type-@}&version={@-version-@}{@-PKMd5Key-@}"
    ],
    'Yianpay' => [
        'space'    => '&',
        'singRule' => "ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&partner={@-partner-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Wuyoupay' => [
        'space'    => '&',
        'singRule' => "body={@-body-@}&notify_time={@-notify_time-@}&out_trade_no={@-out_trade_no-@}&payment_type={@-payment_type-@}&status={@-status-@}&total_fee={@-total_fee-@}&trade_no={@-trade_no-@}&user_account={@-user_account-@}&key={@-PKMd5Key-@}"
    ],
    'Chunhepay' => [
        'space'    => '&',
        'singRule' => "{@-PKMd5Key-@}&price={@-price-@}&orderNum={@-orderNum-@}"
    ],
    'Xiandaipay' => [
        'space'    => '&',
        'singRule' => "{@-txcode-@}{@-txdate-@}{@-txtime-@}{@-version-@}{@-field003-@}{@-field004-@}{@-field011-@}{@-field041-@}{@-field042-@}{@-field048-@}{@-field055-@}{@-field124-@}{@-field125-@}{@-PKMd5Key-@}"
    ],
    'Changshoufupay' => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Yinfutongpay' => [
        'space'    => '&',
        'singRule' => "body={@-body-@}&notify_time={@-notify_time-@}&out_trade_no={@-out_trade_no-@}&payment_type={@-payment_type-@}&total_fee={@-total_fee-@}&trade_no={@-trade_no-@}&user_account={@-user_account-@}&key={@-PKMd5Key-@}"
    ],
    'Zhifumaopay' => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&restate={@-restate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Yinhaopay' => [
        'space'    => '&',
        'singRule' => "&assCode={@-assCode-@}&assPayMessage={@-assPayMessage-@}&assPayMoney={@-assPayMoney-@}&assPayOrderNo={@-assPayOrderNo-@}&respCode={@-respCode-@}&respMsg={@-respMsg-@}&succTime={@-succTime-@}&sysPayOrderNo={@-sysPayOrderNo-@}{@-PKMd5Key-@}"
    ],
    'Pinbapay' => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Duodpay' => [
        'space'    => '&',
        'singRule' => "&resultcode={@-resultcode-@}&mchid={@-mchid-@}&mchno={@-mchno-@}&tradetype={@-tradetype-@}&totalfee={@-totalfee-@}&attach={@-attach-@}&key={@-PKMd5Key-@}"
    ],
    'Wanbaopay' => [
        'space'    => '&',
        'singRule' => "{@-fxstatus-@}{@-fxid-@}{@-fxorder-@}{@-fxfee-@}{@-PKMd5Key-@}"
    ],
    'Hangqingzhifupay' => [
        'space'    => '&',
        'singRule' => "codeP{@-code-@}is_payP{@-is_pay-@}merchantnumberP{@-merchantnumber-@}msgP{@-msg-@}order_moneyP{@-order_money-@}pay_moneyP{@-pay_money-@}pay_typeP{@-pay_type-@}pay_wayP{@-pay_way-@}three_orderP{@-three_order-@}{@-PKMd5Key-@}"
    ],
    'Julianpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Yunkupay' => [
        'space'    => '&',
        'singRule' => "{@-fxstatus-@}{@-fxid-@}{@-fxddh-@}{@-fxfee-@}{@-PKMd5Key-@}"
    ],
    'Jinduobaopay' => [
        'space'    => '&',
        'singRule' => "{@-money-@}{@-record-@}{@-PKMd5Key-@}"
    ],
    'Wangzhepay' => [
        'space'    => '&',
        'singRule' => "appid={@-appid-@}&out_trade_no={@-out_trade_no-@}&trade_no={@-trade_no-@}&money={@-money-@}&key={@-PKMd5Key-@}"
    ],
    'Shunzhifupay' => [
        'space'    => '&',
        'singRule' => "app_id={@-app_id-@}&content={@-content-@}&method={@-method-@}&sign_type={@-sign_type-@}&version={@-version-@}key={@-PKMd5Key-@}"
    ],
    'Bywypay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&attach={@-attach-@}&notifytime={@-notifytime-@}&out_channel_no={@-out_channel_no-@}&out_trade_no={@-out_trade_no-@}key={@-PKMd5Key-@}"
    ],
    'Jinyangguangpay' => [
        'space'    => '&',
        'singRule' => "body={@-body-@}&notify_time={@-notify_time-@}&out_trade_no={@-out_trade_no-@}&payment_type={@-payment_type-@}&status={@-status-@}&total_fee={@-total_fee-@}&trade_no={@-trade_no-@}&user_account={@-user_account-@}&key={@-PKMd5Key-@}"
    ],
    'EsinPay' => [
        'space'    => '|',
        'singRule' => "{@-P_UserID-@}|{@-P_OrderID-@}|{@-P_CardID-@}|{@-P_CardPass-@}|{@-P_FaceValue-@}|{@-P_ChannelID-@}|{@-PKMd5Key-@}"
    ],
    'Jugoufupay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&attach={@-attach-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Ziyoufupay' => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Zhibangxingpay' => [
        'space'    => '&',
        'singRule' => "accFlag={@-accFlag-@}&accName={@-accName-@}&amount={@-amount-@}&createTime={@-createTime-@}&currentTime={@-currentTime-@}&merchant={@-merchant-@}&orderNo={@-orderNo-@}&payFlag={@-payFlag-@}&payTime={@-payTime-@}&payType={@-payType-@}&remark={@-remark-@}&systemNo={@-systemNo-@}#{@-PKMd5Key-@}"
    ],
    'Xinshunchangpay' => [
        'space'    => '&',
        'singRule' => "appid={@-appid-@}&orderNumber={@-orderNumber-@}&price={@-price-@}&signkey={@-PKMd5Key-@}"
    ],
    'XinFaZhiFuPay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&goodsName={@-goodsName-@}&merchNo={@-merchNo-@}&orderNo={@-orderNo-@}&payDate={@-payDate-@}&payStateCode={@-payStateCode-@}&payType={@-payType-@}{@-PKMd5Key-@}"
    ],
    'Juqianpay' => [
        'space'    => '&',
        'singRule' => "appid={@-appid-@}&body={@-body-@}&mch_id={@-mch_id-@}&method={@-method-@}&nonce_str={@-nonce_str-@}&notify_url={@-notify_url-@}&out_trade_no={@-out_trade_no-@}&return_code={@-return_code-@}&sign={@-sign-@}total_fee={@-total_fee-@}version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Lixinpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&code={@-code-@}&date={@-date-@}&msg={@-msg-@}&platformOrderId={@-platformOrderId-@}&sign={@-sign-@}&status={@-status-@}&tradeNo={@-tradeNo-@}&key={@-PKMd5Key-@}"
    ],
    'Liubaliupay' => [
        'space'    => '&',
        'singRule' => "code={@-code-@}&goodsClauses={@-goodsClauses-@}&msg={@-msg-@}&nonStr={@-nonStr-@}&outOrderNo={@-outOrderNo-@}&shopCode={@-shopCode-@}&tradeAmount={@-tradeAmount-@}&key={@-PKMd5Key-@}"
    ],
    'Shenmapay' => [
        'space'    => '&',
        'singRule' => "accFlag={@-accFlag-@}&accName={@-accName-@}&amount={@-amount-@}&createTime={@-createTime-@}&currentTime={@-currentTime-@}&merchant={@-merchant-@}&orderNo={@-orderNo-@}&payFlag={@-payFlag-@}&payTime={@-payTime-@}&payType={@-payType-@}&remark={@-remark-@}&systemNo={@-systemNo-@}#{@-PKMd5Key-@}"
    ],
    'Fajiazhifupay' => [
        'space'    => '&',
        'singRule' => "returncode={@-returncode-@}&userid={@-userid-@}&orderid={@-orderid-@}&money={@-money-@}&keyvalue={@-PKMd5Key-@}"
    ],
    'Dingfupay' => [
        'space'    => '&',
        'singRule' => "Attach={@-Attach-@}&ErrCode={@-ErrCode-@}&ErrMsg={@-ErrMsg-@}&FaceValue={@-FaceValue-@}&MerchantId={@-MerchantId-@}&OrderId={@-OrderId-@}&PayMethod={@-PayMethod-@}&PayMoney={@-PayMoney-@}&TransactionId={@-TransactionId-@}&TransactionTime={@-TransactionTime-@}{@-PKMd5Key-@}"
    ],
    'Xiaocaishen' => [
        'space'    => '&',
        'singRule' => "{@-orderNo-@}&{@-merchantOrderNo-@}&{@-money-@}&{@-payAmount-@}&{@-PKMd5Key-@}"
    ],
    'Yihuipay' => [
        'space'    => '&',
        'singRule' => "out_trade_no={@-out_trade_no-@}&pay_time={@-pay_time-@}&sign={@-sign-@}&sign_type={@-sign_type-@}&status={@-status-@}&total_amount={@-total_amount-@}&trade_no={@-trade_no-@}&trade_type={@-trade_type-@}&key={@-PKMd5Key-@}"
    ],
    'Huihongvpay' => [
        'space'    => '&',
        'singRule' => "attach={@-attach-@}&datetime={@-datetime-@}&mch_id={@-mch_id-@}&merchant_rate={@-merchant_rate-@}&order_no={@-order_no-@}&out_trade_no={@-out_trade_no-@}&returncode={@-returncode-@}&rsp_msg={@-rsp_msg-@}&sign={@-sign-@}&total_fee={@-total_fee-@}&key={@-PKMd5Key-@}"
    ],
    'Boqingpay' => [
        'space'    => '&',
        'singRule' => "accFlag={@-accFlag-@}&accName={@-accName-@}&amount={@-amount-@}&createTime={@-createTime-@}&currentTime={@-currentTime-@}&merchant={@-merchant-@}&orderNo={@-orderNo-@}&payFlag={@-payFlag-@}&payTime={@-payTime-@}&payType={@-payType-@}&remark={@-remark-@}&sign={@-sign-@}&systemNo={@-systemNo-@}#{@-PKMd5Key-@}"
    ],
    'Heibaopay' => [
        'space'    => '&',
        'singRule' => "{@-money-@}{@-orderNumber-@}{@-payDate-@}{@-remark-@}{@-signature-@}{@-success-@}{@-PKMd5Key-@}"
    ],
    'Weiyingbaopay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Laoyunbeipay' => [
        'space'    => '_',
        'singRule' => "{@-companyId-@}_{@-userOrderId-@}_{@-fee-@}_{@-PKMd5Key-@}"
    ],
    'Yizhifupay' => [
        'space'    => '&',
        'singRule' => "body={@-body-@}&mchId={@-mchId-@}&notifyUrl={@-notifyUrl-@}&outTradeNo={@-outTradeNo-@}&sign={@-sign-@}&spbillCreateIp={@-spbillCreateIp-@}&totalFee={@-totalFee-@}&tradeType={@-tradeType-@}&key={@-PKMd5Key-@}"
    ],
    'Kongpay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_md5sign={@-pay_md5sign-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],

    'Xinyizfpay' => [
        'space'    => '&',
        'singRule' => "&assCode={@-assCode-@}&assPayMessage={@-assPayMessage-@}&assPayMoney={@-assPayMoney-@}&assPayOrderNo={@-assPayOrderNo-@}&respCode={@-respCode-@}&succTime={@-succTime-@}&sysPayOrderNo={@-sysPayOrderNo-@}{@-PKMd5Key-@}"
    ],

    'Bachenpay' => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&notify_url={@-notify_url-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&return_url={@-return_url-@}&type={@-type-@}&key={@-PKMd5Key-@}"
    ],

    'Jainpay' => [
        'space'    => '&',
        'singRule' => "{@-mchid-@}{@-notifyurl-@}{@-out_order_id-@}{@-price-@}{@-type-@}{@-PKMd5Key-@}"
    ],
    'Wanjiapay' => [
        'space'    => '&',
        'singRule' => "{@-istype-@}{@-notify_url-@}{@-orderid-@}{@-price-@}{@-return_url-@}{@-token-@}{@-uid-@}{@-PKMd5Key-@}"
    ],
    'Weilianpay' => [
        'space'    => '&',
        'singRule' => "parter={@-parter-@}&type={@-type-@}&value={@-value-@}&orderid={@-orderid-@}&callbackurl={@-callbackurl-@}{@-PKMd5Key-@}"
    ],
    'Weishanyunpay' => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Yishipay' => [
        'space'    => '&',
        'singRule' => "{@-attach-@}{@-orderno-@}{@-orderuid-@}{@-price-@}{@-realprice-@}{@-trade_no-@}{@-transaction_no-@}{@-PKMd5Key-@}"
    ],
    'Dangdfupay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&merchName={@-merchName-@}&merchno={@-merchno-@}&orderno={@-orderno-@}&payType={@-payType-@}&traceno={@-traceno-@}&transDate={@-transDate-@}&transTime={@-transTime-@}&{@-PKMd5Key-@}"
    ],
    'Nanguafupay' => [
        'space'    => '&',
        'singRule' => "accFlag={@-accFlag-@}&accName={@-accName-@}&amount={@-amount-@}&createTime={@-createTime-@}&currentTime={@-currentTime-@}&merchant={@-merchant-@}&orderNo={@-payFlag-@}&payTime={@-payTime-@}&payType={@-payType-@}&systemNo={@-systemNo-@}#{@-PKMd5Key-@}"
    ],
    'Qidianpay' => [
        'space'    => '&',
        'singRule' => "mchid={@-mchid-@}&money={@-money-@}&nonce_str={@-nonce_str-@}&order_id={@-order_id-@}&pay_type={@-pay_type-@}&status={@-status-@}&system_orderid={@-system_orderid-@}&transaction_id={@-transaction_id-@}&mchkey={@-PKMd5Key-@}"
    ],
    'Badapay' => [
        'space'    => '&',
        'singRule' => "banktype={@-banktype-@}&callbackurl={@-callbackurl-@}&method={@-method-@}&ordernumber={@-ordernumber-@}&partner={@-partner-@}&paymoney={@-paymoney-@}&timestamp={@-timestamp-@}&version={@-version-@}&{@-PKMd5Key-@}"
    ],
    'Quanyinpay' => [
        'space'    => '&',
        'singRule' => "notifyUrl={@-notifyUrl-@}&orderDate={@-orderDate-@}&orderNo={@-orderNo-@}&orderPrice={@-orderPrice-@}&orderTime={@-orderTime-@}&payKey={@-payKey-@}&payTypeCode={@-payTypeCode-@}&payWayCode={@-payWayCode-@}&productName={@-productName-@}&{@-PKMd5Key-@}"
    ],
    'Chengshangpay' => [
        'space'    => '&',
        'singRule' => "parter={@-parter-@}&type={@-type-@}&value={@-value-@}&orderid={@-orderid-@}&callbackurl={@-callbackurl-@}&sign={@-PKMd5Key-@}"
    ],
    'Ruixinpay' => [
        'space'    => '&',
        'singRule' => "&assCancelUrl={@-assCancelUrl-@}&assCode={@-assCode-@}&assNotifyUrl={@-assNotifyUrl-@}&assPayMoney={@-assPayMoney-@}&assPayOrderNo={@-assPayOrderNo-@}&assReturnUrl={@-assReturnUrl-@}&paymentType={@-paymentType-@}&subPayCode={@-subPayCode-@}&{@-PKMd5Key-@}"
    ],
    'Qingzhifupay' => [
        'space'    => '&',
        'singRule' => "{@-fxstatus-@}{@-fxid-@}{@-fxddh-@}{@-fxfee-@}{@-PKMd5Key-@}"
    ],
    'Fuhuitongpay1' => [
        'space'    => '&',
        'singRule' => "orderId={@-orderId-@}&respCode={@-respCode-@}&respMsg={@-respMsg-@}&status={@-status-@}&tranAmt={@-tranAmt-@}&tranNo={@-tranNo-@}&tranTime={@-tranTime-@}{@-PKMd5Key-@}"
    ],
    'Aobangpay' => [
        'space'    => '&',
        'singRule' => "complete_date={@-complete_date-@}&ord_amount={@-ord_amount-@}&ord_status={@-ord_status-@}&pay_request_id={@-pay_request_id-@}&request_id={@-request_id-@}&rsp_code={@-rsp_code-@}&rsp_msg={@-rsp_msg-@}&secret_key={@-PKMd5Key-@}"
    ],

    'Yirongtongpay' => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&method={@-method-@}&partner={@-partner-@}&banktype={@-banktype-@}&paymoney={@-paymoney-@}&ordernumber={@-ordernumber-@}&callbackurl={@-callbackurl-@}{@-PKMd5Key-@}"
    ],
    'Mapay' => [
        'space'    => '&',
        'singRule' => "id={@-id-@}&notify_url={@-notify_url-@}&param={@-param-@}&pay_id={@-pay_id-@}&price={@-price-@}&return_url={@-return_url-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Changqifupay' => [
        'space'    => '&',
        'singRule' => "attach={@-attach-@}&code={@-code-@}&is_pay={@-is_pay-@}&merchantnumber={@-merchantnumber-@}&msg={@-msg-@}&order_money={@-order_money-@}&pay_money={@-pay_money-@}&pay_type={@-pay_type-@}&pay_way={@-pay_way-@}&three_order={@-three_order-@}&{@-PKMd5Key-@}"
    ],
    'Bpayingpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Lebaipay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Jiefutongpay' => [
        'space'    => '&',
        'singRule' => "{@-fxstatus-@}{@-fxid-@}{@-fxddh-@}{@-fxfee-@}{@-PKMd5Key-@}"
    ],
    'Wangyinpay' => [
        'space'    => '&',
        'singRule' => "&assCancelUrl={@-assCancelUrl-@}&assCode={@-assCode-@}&assNotifyUrl={@-assNotifyUrl-@}&assPayMoney={@-assPayMoney-@}&assPayOrderNo={@-assPayOrderNo-@}&assReturnUrl={@-assReturnUrl-@}&paymentType={@-paymentType-@}&subPayCode={@-subPayCode-@}{@-PKMd5Key-@}"
    ],
    'Weifutongpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&orderNo={@-orderNo-@}&transactionNo={@-transactionNo-@}#{@-PKMd5Key-@}"
    ],
    'Wanhuibaopay' => [
        'space'    => '&',
        'singRule' => "currency={@-currency-@}&mer_no={@-mer_no-@}&mer_order_no={@-mer_order_no-@}&mer_return_msg={@-mer_return_msg-@}&notify_type={@-notify_type-@}&order_date={@-order_date-@}&order_no={@-order_no-@}&pay_date={@-pay_date-@}&trade_amount={@-trade_amount-@}&trade_result={@-trade_result-@}&key={@-PKMd5Key-@}"
    ],
    'Qianyingfupay' => [
        'space'    => '&',
        'singRule' => "{@-orderid -@}{@-platform_trade_no-@}{@-price -@}{@-realprice -@}{@-PKMd5Key-@}"
    ],

    'Paysapipay' => [
        'space'    => '&',
        'singRule' => "{@-istype-@}{@-notify_url-@}{@-orderid-@}{@-price-@}{@-return_url-@}{@-token-@}{@-uid-@}{@-PKMd5Key-@}"
    ],

    'Yichongpay' => [
        'space'    => '&',
        'singRule' => "createtime={@-createtime-@}&endtime={@-endtime-@}&money={@-money-@}&nonce={@-nonce-@}&param={@-param-@}&pay_id={@-pay_id-@}&pay_no={@-pay_no-@}&paytime={@-paytime-@}&status={@-status-@}&timestamp={@-timestamp-@}&trade_no={@-trade_no-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Juzhifupay' => [
        'space'    => '&',
        'singRule' => "funCode={@-funCode-@}&orderAmt={@-orderAmt-@}&outTradeNo={@-outTradeNo-@}&pay_id={@-pay_id-@}&pay_no={@-pay_no-@}&platMerId={@-platMerId-@}&platOrderId={@-platOrderId-@}&retCode={@-retCode-@}&retMsg={@-retMsg-@}&tradeState={@-tradeState-@}&trade_no={@-trade_no-@}&type={@-type-@}&key={@-PKMd5Key-@}"
    ],
    'LZzhifupay' => [
        'space'    => '&',
        'singRule' => "asynNotifyUrl={@-asynNotifyUrl-@}&merId={@-merId-@}&orderAmount={@-orderAmount-@}&orderStatus={@-orderStatus-@}&payId={@-payId-@}&payTime={@-payTime-@}&prdOrdNo={@-prdOrdNo-@}&signType={@-signType-@}&synNotifyUrl={@-synNotifyUrl-@}&transType={@-transType-@}&versionId={@-versionId-@}&key={@-PKMd5Key-@}"
    ],
    'Yongpay' => [
        'space'    => '&',
        'singRule' => "pay_memberid^{@-pay_memberid-@}&pay_orderid^{@-pay_orderid-@}&pay_amount^{@-pay_amount-@}&pay_applydate^{@-pay_applydate-@}&pay_channelCode^{@-pay_channelCode-@}&pay_notifyurl^{@-pay_notifyurl-@}&key={@-PKMd5Key-@}"
    ],
    'Svippay' => [
        'space'    => '&',
        'singRule' => "body={@-body-@}&notify_time={@-notify_time-@}&out_trade_no={@-out_trade_no-@}&payment_type={@-payment_type-@}&status={@-status-@}&total_fee={@-total_fee-@}&trade_no={@-trade_no-@}&user_account={@-user_account-@}&key={@-PKMd5Key-@}"
    ],
    'Zhongnuopay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_order_id={@-pay_order_id-@}&pay_status={@-pay_status-@}&pay_success_date={@-pay_success_date-@}&key={@-PKMd5Key-@}"
    ],
    'Suiyiypay' => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Xpay' => [
        'space'    => '&',
        'singRule' => "pay_memberid^{@-pay_memberid-@}&pay_orderid^{@-pay_orderid-@}&pay_amount^{@-pay_amount-@}&pay_applydate^{@-pay_applydate-@}&pay_channelCode^{@-pay_channelCode-@}&pay_notifyurl^{@-pay_notifyurl-@}&key={@-PKMd5Key-@}"
    ],
    'Zhifubaopay' => [
        'space'    => '&',
        'singRule' => "{@-appid-@}{@-out_trade_no-@}{@-money-@}{@-paytype-@}{@-PKMd5Key-@}"
    ],
    'Fengjiepay' => [
        'space'    => '&',
        'singRule' => "complete_time={@-complete_time-@}&currency={@-currency-@}&merchant_no={@-merchant_no-@}&ord_status={@-ord_status-@}&order_amount={@-order_amount-@}&order_no={@-order_no-@}&pay_amount={@-pay_amount-@}&pay_code={@-pay_code-@}&payment_trx_no={@-payment_trx_no-@}&product_name={@-product_name-@}&key={@-PKMd5Key-@}"
    ],
    'Shunyipay' => [
        'space'    => '&',
        'singRule' => "orderNo={@-orderNo-@}&payAmt={@-payAmt-@}&retCode={@-retCode-@}&transNo={@-transNo-@}&userId={@-userId-@}&key={@-PKMd5Key-@}"
    ],

    'Tianruipay' => [
        'space'    => '&',
        'singRule' => "account_type={@-account_type-@}&amount={@-amount-@}&bankname={@-bankname-@}&cardtype={@-cardtype-@}&clienttype={@-clienttype-@}&ip_addr={@-ip_addr-@}&merchant_no={@-merchant_no-@}&nonce_str={@-nonce_str-@}&pay_channel={@-pay_channel-@}&pay_notifyurl={@-pay_notifyurl-@}&request_no={@-request_no-@}&request_time={@-request_time-@}&return_url={@-return_url-@}&key={@-PKMd5Key-@}"
    ],
    'Fengshengpay' => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&method={@-method-@}&partner={@-partner-@}&banktype={@-banktype-@}&paymoney={@-paymoney-@}&ordernumber={@-ordernumber-@}&callbackurl={@-callbackurl-@}{@-PKMd5Key-@}"
    ],
    'Sunmapay' => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&method={@-method-@}&partner={@-partner-@}&banktype={@-banktype-@}&paymoney={@-paymoney-@}&ordernumber={@-ordernumber-@}&callbackurl={@-callbackurl-@}{@-PKMd5Key-@}"
    ],
    'Hexingfupay' => [
        'space'    => '&',
        'singRule' => "appid={@-appid-@}&out_trade_no={@-out_trade_no-@}&trade_no={@-trade_no-@}&money={@-money-@}&key={@-PKMd5Key-@}"
    ],
    'Ziranzfpay' => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Mingshupay' => [
        'space'    => '&',
        'singRule' => "dealId={@-dealId-@}&dealTime={@-dealTime-@}&orderAmount={@-orderAmount-@}&payAmount={@-payAmount-@}&payType={@-payType-@}&retCode={@-retCode-@}&transStatus={@-transStatus-@}&transactionId={@-transactionId-@}&key={@-PKMd5Key-@}"
    ],
    'Tianhezfpay' => [
        'space'    => '&',
        'singRule' => "code={@-code-@}&create_time={@-create_time-@}&mer_id={@-mer_id-@}&money={@-money-@}&order_id={@-order_id-@}&out_trade_no={@-out_trade_no-@}&trade_type={@-trade_type-@}&{@-PKMd5Key-@}"
    ],
    'Gongniupay' => [
        'space'    => '&',
        'singRule' => "merchant_code={@-merchant_code-@}&merchant_order_no={@-merchant_order_no-@}&merchant_goods={@-merchant_goods-@}&merchant_amount={@-merchant_amount-@}&merchant_md5={@-merchant_md5-@}"
    ],
    'Shunfutongpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&buyerId={@-buyerId-@}&buyerName={@-buyerName-@}&clientIp={@-clientIp-@}&goodsName={@-goodsName-@}&mchOrderNo={@-mchOrderNo-@}&merchantNo={@-merchantNo-@}&nonceStr={@-nonceStr-@}&notifyUrl={@-notifyUrl-@}&orderTime={@-orderTime-@}&paymentType={@-paymentType-@}&appkey={@-PKMd5Key-@}"
    ],
    'Huizhongpay' => [
        'space'    => '&',
        'singRule' => "parter={@-parter-@}&type={@-type-@}&value={@-value-@}&orderid={@-orderid-@}&callbackurl={@-callbackurl-@}{@-PKMd5Key-@}"
    ],
    'Yinshangpay' => [
        'space'    => '&',
        'singRule' => "status={@-status-@}&shid={@-shid-@}&bb={@-bb-@}&zftd={@-zftd-@}&ddh={@-ddh-@}&je={@-je-@}&ddmc={@-ddmc-@}&ddbz={@-ddbz-@}&ybtz={@-ybtz-@}&tbtz={@-tbtz-@}&{@-PKMd5Key-@}"
    ],
    'Manilapay' => [
        'space'    => '&',
        'singRule' => "{@-notify_url-@}{@-order_number-@}{@-order_uid-@}{@-qr_amount-@}{@-return_url-@}{@-type-@}{@-uid-@}{@-PKMd5Key-@}"
    ],
    'Aimazfpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&attach={@-attach-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Yunyizfpay' => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Juhebosspay' => [
        'space'    => '&',
        'singRule' => "attach={@-attach-@}&mchNo={@-mchNo-@}&money={@-money-@}&orderid={@-orderid-@}&paytype={@-paytype-@}&status={@-status-@}&tradeno={@-tradeno-@}&key={@-PKMd5Key-@}"
    ],
    'Quanrifupay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&clientIp={@-clientIp-@}&createTime={@-createTime-@}&nonceStr={@-nonceStr-@}&notifyTime={@-notifyTime-@}&notifyType={@-notifyType-@}&orderNo={@-orderNo-@}&orderStatus={@-orderStatus-@}&outOrderNo={@-outOrderNo-@}&payTime={@-payTime-@}&startTime={@-startTime-@}&key={@-PKMd5Key-@}"
    ],
    'Shandianfupay' => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Ekatongpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&mchid={@-mchid-@}&message={@-message-@}&order_no={@-order_no-@}&out_trade_no={@-out_trade_no-@}&pay_status={@-pay_status-@}&pay_time={@-pay_time-@}&remark={@-remark-@}&{@-PKMd5Key-@}"
    ],
    'Kuixingpay' => [
        'space'    => '&',
        'singRule' => "accFlag={@-accFlag-@}&accName={@-accName-@}&amount={@-amount-@}&createTime={@-createTime-@}&currentTime={@-currentTime-@}&merchant={@-merchant-@}&orderNo={@-orderNo-@}&payFlag={@-payFlag-@}&payTime={@-payTime-@}&payType={@-payType-@}&remark={@-remark-@}&systemNo={@-systemNo-@}#{@-PKMd5Key-@}"
    ],
    'Fengyitianxiangpay' => [
        'space'    => '&',
        'singRule' => "api_code={@-api_code-@}&code={@-code-@}&is_type={@-is_type-@}&mark={@-mark-@}&order_id={@-order_id-@}&paysapi_id={@-paysapi_id-@}&price={@-price-@}&real_price={@-real_price-@}&key={@-PKMd5Key-@}"
    ],
    'Yizfpay' => [
        'space'    => '&',
        'singRule' => "{@-order_id-@}{@-price-@}{@-PKMd5Key-@}"
    ],
    'Wangwangpay' => [
        'space'    => '&',
        'singRule' => "callbackUrl={@-callbackUrl-@}&merId={@-merId-@}&notifyUrl={@-notifyUrl-@}&orderAmt={@-orderAmt-@}&orderNo={@-orderNo-@}&payprod={@-payprod-@}&remark1={@-remark1-@}&remark2={@-remark2-@}&thirdChannel={@-thirdChannel-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Jichuangzfpay' => [
        'space'    => '&',
        'singRule' => "tradeno={@-tradeno-@}&outtradeno={@-outtradeno-@}&amount={@-amount-@}&partner={@-partner-@}&inttime={@-inttime-@}&key={@-PKMd5Key-@}"
    ],
    'Luzhoutdpay' => [
        'space'    => '&',
        'singRule' => "asynNotifyUrl={@-asynNotifyUrl-@}&currency={@-currency-@}&merId={@-merId-@}&orderAmount={@-orderAmount-@}&orderDate={@-orderDate-@}&payMode={@-payMode-@}&prdAmt={@-prdAmt-@}&prdName={@-prdName-@}&prdOrdNo={@-prdOrdNo-@}&receivableType={@-receivableType-@}&signType={@-signType-@}&synNotifyUrl={@-synNotifyUrl-@}&transType={@-transType-@}&versionId={@-versionId-@}&key={@-PKMd5Key-@}"
    ],
    'Caishenpay' => [
        'space'    => '&',
        'singRule' => "parter={@-parter-@}&type={@-type-@}&value={@-value-@}&orderid={@-orderid-@}&callbackurl={@-callbackurl-@}&sign={@-PKMd5Key-@}"
    ],
    'Shangrubaopay' => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&orderuid={@-orderuid-@}&paysapi_id={@-paysapi_id-@}&price={@-price-@}&realprice={@-realprice-@}&token={@-PKMd5Key-@}"
    ],
    'Qianddiwcpay' => [
        'space'    => '&',
        'singRule' => "{@-fxstatus-@}{@-fxid-@}{@-fxddh-@}{@-fxfee-@}{@-PKMd5Key-@}"
    ],
    'Youyouvpay' => [
        'space'    => '&',
        'singRule' => "fxid={@-fxid-@}&fxddh={@-fxddh-@}&fxfee={@-fxfee-@}&fxnotifyurl={@-fxnotifyurl-@}&{@-PKMd5Key-@}"
    ],
    'Epay' => [
        'space'    => '&',
        'singRule' => "app_id={@-app_id-@}&notify={@-notify-@}&order_amount={@-order_amount-@}&order_no={@-order_no-@}&type={@-type-@}&key={@-PKMd5Key-@}"
    ],
    'Mibaopay' => [
        'space'    => '&',
        'singRule' => "parter={@-parter-@}&type={@-type-@}&value={@-value-@}&orderid={@-orderid-@}&callbackurl={@-callbackurl-@}&sign={@-PKMd5Key-@}"
    ],
    'Yibupay' => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&notify_url={@-notify_url-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&return_url={@-return_url-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Xiongmaopay' => [
        'space'    => '&',
        'singRule' => "bankCode={@-bankCode-@}&collectWay={@-collectWay-@}&merCode={@-merCode-@}&noticeUrl={@-noticeUrl-@}&orderDesc={@-orderDesc-@}&tranAmt={@-tranAmt-@}&tranNo={@-tranNo-@}&tranTime={@-tranTime-@}&tranType={@-tranType-@}{@-PKMd5Key-@}"
    ],
    'Sanliulingpay' => [
        'space'    => '&',
        'singRule' => "merId={@-merId-@}&orderAmt={@-orderAmt-@}&orderNo={@-orderNo-@}&payDate={@-payDate-@}&payNo={@-payNo-@}&payStatus={@-payStatus-@}&payTime={@-payTime-@}&realAmt={@-realAmt-@}&remark1={@-remark1-@}&remark2={@-remark2-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Ajiuzfpay' => [
        'space'    => '&',
        'singRule' => "MerchantId={@-MerchantId-@}&attach={@-attach-@}&out_trade_no={@-out_trade_no-@}&paytime={@-paytime-@}&total_amount={@-total_amount-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}{@-PKMd5Key-@}"
    ],
    'Tbpay' => [
        'space'    => '&',
        'singRule' => "status={@-status-@}&shid={@-shid-@}&bb={@-bb-@}&zftd={@-zftd-@}&ddh={@-ddh-@}&je={@-je-@}&ddmc={@-ddmc-@}&ddbz={@-ddbz-@}&ybtz={@-ybtz-@}&tbtz={@-tbtz-@}&{@-PKMd5Key-@}"
    ],
    'Yurenmatoupay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&attach={@-attach-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Shenzhoupay' => [
        'space'    => '&',
        'singRule' => "body={@-body-@}&mch_create_ip={@-mch_create_ip-@}&mch_id={@-mch_id-@}&nonce_str={@-nonce_str-@}&notify_url={@-notify_url-@}&out_trade_no={@-out_trade_no-@}&total_fee={@-total_fee-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Qianmiaopay' => [
        'space'    => '&',
        'singRule' => "body={@-body-@}&mch_create_ip={@-mch_create_ip-@}&mch_id={@-mch_id-@}&nonce_str={@-nonce_str-@}&notify_url={@-notify_url-@}&out_trade_no={@-out_trade_no-@}&total_fee={@-total_fee-@}&key={@-PKMd5Key-@}"
    ],
    'Zhengyangpay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Kuaifutongpay' => [
        'space'    => '&',
        'singRule' => "actualAmount={@-actualAmount-@}&orderNo={@-orderNo-@}&orderStatus={@-orderStatus-@}&ret_code={@-ret_code-@}&ret_msg={@-ret_msg-@}&tranSerno={@-tranSerno-@}&transAmt={@-transAmt-@}&transDate={@-transDate-@}&key={@-PKMd5Key-@}"
    ],
    'Wufangpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&orderNo={@-orderNo-@}&callbackurl={@-callbackurl-@}&orderno={@-orderno-@}&partner={@-partner-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Tongbaoyhpay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_bankcode={@-pay_bankcode-@}&pay_biztype={@-pay_biztype-@}&pay_merberid={@-pay_merberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderno={@-pay_orderno-@}&pay_ordertime={@-pay_ordertime-@}&key={@-PKMd5Key-@}"
    ],
    'Aabpay' => [
        'space'    => '&',
        'singRule' => "{@-orderid-@}{@-orderuid-@}{@-pay_id-@}{@-price-@}{@-realprice-@}{@-PKMd5Key-@}"
    ],
    'Yunbaozfpay' => [
        'space'    => '&',
        'singRule' => "{@-order_id-@}{@-order_uid-@}{@-pay_num-@}{@-price-@}{@-PKMd5Key-@}{@-transaction_id-@}"
    ],
    'SuiEfupay' => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&orderuid={@-orderuid-@}&paysapi_id={@-paysapi_id-@}&price={@-price-@}&realprice={@-realprice-@}&token={@-PKMd5Key-@}"
    ],
    'Fengxiepay' => [
        'space'    => '&',
        'singRule' => "{@-fx_merchant_id-@}|{@-fx_order_id-@}|{@-fx_order_amount-@}|{@-fx_notify_url-@}|{@-PKMd5Key-@}"
    ],
    'Juyoupay' => [
        'space'    => '&',
        'singRule' => "parter={@-parter-@}&type={@-type-@}&value={@-value-@}&orderid={@-orderid-@}&callbackurl={@-callbackurl-@}{@-PKMd5Key-@}"
    ],
    'Jinshanguipay' => [
        'space'    => '&',
        'singRule' => "{@-amount-@}{@-merchantNo-@}{@-notify_url-@}{@-orderNo-@}{@-terminalNo-@}{@-type-@}{@-PKMd5Key-@}"
    ],
    'Jiufubaopay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Feilongpay' => [
        'space'    => '&',
        'singRule' => "{@-companyId-@}_{@-userOrderId-@}_{@-fee-@}_{@-PKMd5Key-@}"
    ],
    'Qianshouzfpay' => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Yimozfpay' => [
        'space'    => '&',
        'singRule' => "status={@-status-@}&amount={@-amount-@}&order_no={@-order_no-@}&order_status={@-order_status-@}&pay_time={@-pay_time-@}&app_secret={@-PKMd5Key-@}"
    ],
    'Baduzfpay' => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Jiuchuanzfpay' => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&paymoney={@-paymoney-@}&paytype={@-paytype-@}&sdorderno={@-sdorderno-@}&sdpayno={@-sdpayno-@}&status={@-status-@}&{@-PKMd5Key-@}"
    ],
    'Jiaxinpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&channel={@-channel-@}&merid={@-merid-@}&message={@-message-@}&order_no={@-order_no-@}&responsecode={@-responsecode-@}&key={@-PKMd5Key-@}"
    ],
    'Feifanjuhepay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Zhanlanzfpay' => [
        'space'    => '&',
        'singRule' => "complete_time={@-complete_time-@}&currency={@-currency-@}&merchant_no={@-merchant_no-@}&ord_status={@-ord_status-@}&order_amount={@-order_amount-@}&order_no={@-order_no-@}&pay_amount={@-pay_amount-@}&pay_code={@-pay_code-@}&payment_trx_no={@-payment_trx_no-@}&=product_name={@-product_name-@}&key={@-PKMd5Key-@}"
    ],
    'Zhongyizfpay' => [
        'space'    => '&',
        'singRule' => "{@-appid-@}{@-PKMd5Key-@}{@-ddh-@}{@-lb-@}{@-money-@}{@-out_trade_no-@}{@-pay_time-@}"
    ],
    'Xinbaopay' => [
        'space'    => '&',
        'singRule' => "parter={@-parter-@}&type={@-type-@}&value={@-value-@}&orderid={@-orderid-@}&callbackurl={@-callbackurl-@}{@-PKMd5Key-@}"
    ],
    'Quanqiupay' => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Qianrongpay' => [
        'space'    => '&',
        'singRule' => "api_code={@-api_code-@}&is_type={@-is_type-@}&mark={@-mark-@}&notify_url={@-notify_url-@}&order_id={@-order_id-@}&price={@-price-@}&return_type={@-return_type-@}&return_url={@-return_url-@}&time={@-time-@}&key={@-PKMd5Key-@}"
    ],
    'Mobaipay' => [
        'space'    => '&',
        'singRule' => "message={@-message-@}&okordertime={@-okordertime-@}&orderno={@-orderno-@}&orderstatus={@-orderstatus-@}&partnerid={@-partnerid-@}&partnerorderid={@-partnerorderid-@}&payamount={@-payamount-@}&paytype={@-paytype-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Dedaopay' => [
        'space'    => '&',
        'singRule' => "issuer_id={@-issuer_id-@}&mch_id={@-mch_id-@}&nonce={@-nonce-@}&notify_url={@-notify_url-@}&out_trade_no={@-out_trade_no-@}&spbill_create_ip={@-spbill_create_ip-@}&subject={@-subject-@}&timestamp={@-timestamp-@}&total_fee={@-total_fee-@}&trade_type={@-trade_type-@}&key={@-PKMd5Key-@}"
    ],
    'Huohuojfpay' => [
        'space'    => '&',
        'singRule' => "{@-orderid-@}{@-orderuid-@}{@-platform_trade_no-@}{@-price-@}{@-realprice-@}}{@-PKMd5Key-@}"
    ],
    'Leshuapay' => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&customerid={@-customerid-@}&total_fee={@-total_fee-@}&sdorderno={@-sdorderno-@}&notifyurl={@-notifyurl-@}&returnurl={@-returnurl-@}&{@-PKMd5Key-@}"
    ],
    'Yishoupay' => [
        'space'    => '&',
        'singRule' => "{@-transTypeNo-@}{@-merchantNum-@}{@-orderId-@}{@-txnTime-@}{@-PKMd5Key-@}{@-txnAmt-@}"
    ],
    'Xiaomapay' => [
        'space'    => '&',
        'singRule' => "attach={@-attach-@}&body={@-body-@}&customer_id={@-customer_id-@}&detail={@-detail-@}&notify_url={@-notify_url-@}&out_trade_no={@-out_trade_no-@}&pay_type={@-pay_type-@}&request_type={@-request_type-@}&return_url={@-return_url-@}&subject={@-subject-@}&total_fee={@-total_fee-@}{@-PKMd5Key-@}"
    ],
    'Mangojinfupay' => [
        'space'    => '&',
        'singRule' => "createTime={@-createTime-@}&orderAmount={@-orderAmount-@}&orderNo={@-orderNo-@}&paySerial={@-paySerial-@}&key={@-PKMd5Key-@}"
    ],
    'Xianjinbaopay' => [
        'space'    => '&',
        'singRule' => "body={@-body-@}&cardNo={@-cardNo-@}&mch_id={@-mch_id-@}&nonce_str={@-nonce_str-@}&notify_url={@-notify_url-@}&out_trade_no={@-out_trade_no-@}&spbill_create_ip={@-spbill_create_ip-@}&total_fee={@-total_fee-@}&trade_type={@-trade_type-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Hongdazfpay' => [
        'space'    => '&',
        'singRule' => "{@-pid-@}{@-type-@}{@-no-@}{@-money-@}{@-extend-@}{@-out_order_id-@}{@-PKMd5Key-@}"
    ],
    'Weishoubaopay' => [
        'space'    => '&',
        'singRule' => "opstate={@-opstate-@}&orderid={@-orderid-@}&ovalue={@-ovalue-@}&parter={@-parter-@}&key={@-PKMd5Key-@}"
    ],
    'Yiliantongpay' => [
        'space'    => '&',
        'singRule' => "amt={@-amt-@}&channelId={@-channelId-@}&frontUrl={@-frontUrl-@}&funCode={@-funCode-@}&funName={@-funName-@}&notifyUrl={@-notifyUrl-@}&orderTime={@-orderTime-@}&payMethod={@-payMethod-@}&platMerId={@-platMerId-@}&platOrderId={@-platOrderId-@}&subject={@-subject-@}&tradeTime={@-tradeTime-@}&tradeType={@-tradeType-@}&key={@-PKMd5Key-@}"
    ],
    'md5Rules.php' => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Muzhifupay' => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Mihuipay' => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&customerid={@-customerid-@}&total_fee={@-total_fee-@}&sdorderno={@-sdorderno-@}&notifyurl={@-notifyurl-@}&returnurl={@-returnurl-@}&{@-PKMd5Key-@}"
    ],
    'Zhongpay' => [
        'space'    => '&',
        'singRule' => "merchant_id={@-merchant_id-@}&order_id={@-order_id-@}&amount={@-amount-@}&sign={@-PKMd5Key-@}"
    ],
    'Huaxiazfpay' => [
        'space'    => '&',
        'singRule' => "{@-appid-@}{@-rmb-@}{@-record-@}{@-PKMd5Key-@}"
    ],
    'Xunbaozfpay' => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Aomenpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&currentTime={@-currentTime-@}&merchant={@-merchant-@}&notifyUrl={@-notifyUrl-@}&orderNo={@-orderNo-@}&payType={@-payType-@}&returnUrl={@-returnUrl-@}#{@-PKMd5Key-@}"
    ],
    'Yidianpay' => [
        'space'    => '&',
        'singRule' => "{@-edstatus-@}{@-edid-@}{@-edddh-@}{@-edfee-@}{@-PKMd5Key-@}"
    ],
    'Xinlipay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Xiangyunpay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'APzfpay' => [
        'space'    => '&',
        'singRule' => "merchant_code={@-merchant_code-@}&notify_type={@-notify_type-@}&sign_type={@-sign_type-@}&order_no={@-order_no-@}&order_amount={@-order_amount-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&key={@-PKMd5Key-@}"
    ],
    'Paytongpay' => [
        'space'    => '&',
        'singRule' => "notifyUrl={@-notifyUrl-@}&orderIp={@-orderIp-@}&orderPrice={@-orderPrice-@}&orderTime={@-orderTime-@}&outTradeNo={@-outTradeNo-@}&payKey={@-payKey-@}&productName={@-productName-@}&productType={@-productType-@}&remark={@-remark-@}&returnUrl={@-returnUrl-@}&paySecret={@-PKMd5Key-@}"
    ],
    'Tianrunzfpay' => [
        'space'    => '&',
        'singRule' => "payid={@-payid-@}&orderid={@-orderid-@}&orderuid={@-orderuid-@}&paymoney={@-paymoney-@}&realpay={@-realpay-@}&token={@-PKMd5Key-@}"
    ],
    'Jifutong' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Chaofanpay' => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&customerid={@-customerid-@}&total_fee={@-total_fee-@}&sdorderno={@-sdorderno-@}&notifyurl={@-notifyurl-@}&returnurl={@-returnurl-@}&{@-PKMd5Key-@}"
    ],
    'Zhuanqianbaopay' => [
        'space'    => '&',
        'singRule' => "{@-Payment_order-@}&{@-Order_id-@}&{@-Order_money-@}&{@-Real_money-@}&{@-PKMd5Key-@}"
    ],
    'Madaipay' => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&customerid={@-customerid-@}&total_fee={@-total_fee-@}&sdorderno={@-sdorderno-@}&notifyurl={@-notifyurl-@}&returnurl={@-returnurl-@}&{@-PKMd5Key-@}"
    ],
    'Hengyangpay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Xinshunchangvpay' => [
        'space'    => '&',
        'singRule' => "notifyUrl={@-notifyUrl-@}&orderPrice={@-orderPrice-@}&organizationId={@-organizationId-@}&organizationOrderCode={@-organizationOrderCode-@}&payment={@-payment-@}&token={@-PKMd5Key-@}"
    ],
    'Juyopay' => [
        'space'    => '&',
        'singRule' => "{@-mid-@}{@-oid-@}{@-amt-@}{@-way-@}{@-back-@}{@-notify-@}{@-PKMd5Key-@}"
    ],
    'Baodupay' => [
        'space'    => '&',
        'singRule' => "parter={@-parter-@}&type={@-type-@}&value={@-value-@}&orderid={@-orderid-@}&callbackurl={@-callbackurl-@}&gign={@-PKMd5Key-@}"
    ],
    'Guangxinpay' => [
        'space'    => '&',
        'singRule' => "merchantNo={@-merchantNo-@}&no={@-no-@}&nonce={@-nonce-@}&timestamp={@-timestamp-@}&key={@-PKMd5Key-@}"
    ],
    'Baiweipay' => [
        'space'    => '&',
        'singRule' => "{@-mch_id-@}{@-time_end-@}{@-out_trade_no-@}{@-ordernumber-@}{@-transtypeid-@}{@-transaction_id-@}{@-total_fee-@}{@-service-@}{@-way-@}{@-result_code-@}{@-PKMd5Key-@}"
    ],
    'Yijipay' => [
        'space'    => '&',
        'singRule' => "banktype={@-banktype-@}&callbackurl={@-callbackurl-@}&hrefbackurl={@-hrefbackurl-@}&key={@-PKMd5Key-@}&ordernumber={@-ordernumber-@}&partner={@-partner-@}&paymoney={@-paymoney-@}&subject={@-subject-@}"
    ],
    'Tongyinpay' => [
        'space'    => '&',
        'singRule' => "parter={@-parter-@}&type={@-type-@}&value={@-value-@}&orderid={@-orderid-@}&callbackurl={@-callbackurl-@}{@-PKMd5Key-@}"
    ],
    'Tongxingzfpay' => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Niugouspay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&sign={@-PKMd5Key-@}"
    ],
    'Mashangfupay' => [
        'space'    => '&',
        'singRule' => "merId={@-merId-@}&merNo={@-merNo-@}&status={@-status-@}&amout={@-amout-@}&sysNo={@-sysNo-@}{@-PKMd5Key-@}"
    ],
    'Yishunfupay' => [
        'space'    => '&',
        'singRule' => "merchantId={@-merchantId-@}&orderId={@-orderId-@}&orderTime={@-orderTime-@}&status={@-status-@}&transAmt={@-transAmt-@}&transId={@-transId-@}&key={@-PKMd5Key-@}"
    ],
    'Miaochongpay' => [
        'space'    => '&',
        'singRule' => "{@-UID-@}&{@-Merorderno-@}&{@-Username-@}&{@-Money-@}&{@-Paymentype-@}&{@-PKMd5Key-@}"
    ],
    'Yihuibaopay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Fujupay' => [
        'space'    => '&',
        'singRule' => "version={@-version-@}&customerid={@-customerid-@}&total_fee={@-total_fee-@}&sdorderno={@-sdorderno-@}&notifyurl={@-notifyurl-@}&returnurl={@-returnurl-@}&{@-PKMd5Key-@}"
    ],
    'Kuaijiepay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&frontUrl={@-frontUrl-@}&merchantNo={@-merchantNo-@}&notifyUrl={@-notifyUrl-@}&payType={@-payType-@}&spbillCreateIp={@-spbillCreateIp-@}&tradeNo={@-tradeNo-@}&appkey={@-PKMd5Key-@}"
    ],
    'Aishangpay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Bababapay' => [
        'space'    => '&',
        'singRule' => "'appid={@-'appid-@}&productID={@-productID-@}&price={@-price-@}&productCount={@-productCount-@}&userID={@-userID-@}&sign={@-PKMd5Key-@}"
    ],
    'Zhongfupay' => [
        'space'    => '&',
        'singRule' => "'channel_id{@-'channel_id-@}&mchid{@-mchid-@}&order_id{@-order_id-@}&return_url{@-return_url-@}&total_amount{@-total_amount-@}{@-PKMd5Key-@}"
    ],
    'Bihuizfpay' => [
        'space'    => '&',
        'singRule' => "account={@-account-@}&key={@-PKMd5Key-@}&pay_amount={@-pay_amount-@}&remark={@-remark-@}&resqn={@-resqn-@}&status={@-status-@}&trade_no={@-trade_no-@}"
    ],
    'Weifupay' => [
        'space'    => '&',
        'singRule' => "BuCode={@-BuCode-@}&OrderId={@-OrderId-@}&PayChannel={@-PayChannel-@}&OrderAccount={@-OrderAccount-@}&Amount={@-Amount-@}&Key={@-PKMd5Key-@}"
    ],
    'Huanqiukpay' => [
        'space'    => '&',
        'singRule' => "api_code={@-api_code-@}&is_type={@-is_type-@}&mark={@-mark-@}&notify_url={@-notify_url-@}&order_id={@-order_id-@}&price={@-price-@}&return_type={@-return_type-@}&time={@-time-@}&key={@-PKMd5Key-@}"
    ],
    'Yijidmpay' => [
        'space'    => '&',
        'singRule' => "banktype={@-banktype-@}&callbackurl={@-callbackurl-@}&hrefbackurl={@-hrefbackurl-@}&key={@-PKMd5Key-@}&ordernumber={@-ordernumber-@}&partner={@-partner-@}&paymoney={@-paymoney-@}&subject={@-subject-@}"
    ],
    'Kuaiyipay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&appId={@-appId-@}&body={@-body-@}&currency={@-currency-@}&extra={@-extra-@}&mchId={@-mchId-@}&mchOrderNo={@-mchOrderNo-@}&notifyUrl={@-notifyUrl-@}&productId={@-productId-@}&subject={@-subject-@}&key={@-PKMd5Key-@}"
    ],
    'Xiaoxingxpay' => [
        'space'    => '&',
        'singRule' => "appid={@-appid-@}&data={@-data-@}&money={@-money-@}&type={@-type-@}&uip={@-uip-@}&appkey={@-PKMd5Key-@}"
    ],
    'Gemapay' => [
        'space'    => '&',
        'singRule' => "'amount{@-'amount-@}&cb_url{@-cb_url-@}&desc{@-desc-@}&mch_id{@-mch_id-@}&order_id{@-order_id-@}&pay_type{@-pay_type-@}&version{@-version-@}{@-PKMd5Key-@}"
    ],
    'Anxtongpay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Benbenpay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Huifupay' => [
        'space'    => '&',
        'singRule' => "callback={@-callback-@}&merchant={@-merchant-@}&money={@-money-@}&payWay={@-payWay-@}&return_url={@-return_url-@}&tradeId={@-tradeId-@}{@-PKMd5Key-@}"
    ],
    'Xunkepay' => [
        'space'    => '&',
        'singRule' => "parter={@-parter-@}&type={@-type-@}&value={@-value-@}&orderid={@-orderid-@}&callbackurl={@-callbackurl-@}{@-PKMd5Key-@}"
    ],
    'Jiuzhoupay' => [
        'space'    => '&',
        'singRule' => "{@-goodsname-@}{@-istype-@}{@-notify_url-@}{@-orderid-@}{@-orderuid-@}{@-price-@}{@-return_url-@}{@-PKMd5Key-@}{@-uid-@}"
    ],
    'Tongfbaopay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Meilianpay' => [
        'space'    => '&',
        'singRule' => "callback={@-callback-@}&channel={@-channel-@}&ip={@-ip-@}&orderid={@-orderid-@}&paytype={@-paytype-@}&txnAmt={@-txnAmt-@}&key={@-PKMd5Key-@}"
    ],
    'Yunpay' => [
        'space'    => '&',
        'singRule' => "attach={@-attach-@}&body={@-body-@}&merchantId={@-merchantId-@}&nonceStr={@-nonceStr-@}&notifyUrl={@-notifyUrl-@}&outTradeNo={@-outTradeNo-@}&payMode={@-payMode-@}&payType={@-payType-@}&totalFee={@-totalFee-@}&key={@-PKMd5Key-@}"
    ],
    'Shuntongpay' => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&notify_url={@-notify_url-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&return_url={@-return_url-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Kairuiyunpay' => [
        'space'    => '&',
        'singRule' => "{@-istype-@}{@-notify_url-@}{@-orderid-@}{@-price-@}{@-return_url-@}{@-PKMd5Key-@}{@-uid-@}"
    ],
    'Yidavpay' => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&notify_url={@-notify_url-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&return_url={@-return_url-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Shukepay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&app_id={@-app_id-@}&extra_return_params={@-extra_return_params-@}&notify_url={@-notify_url-@}&orderNo={@-orderNo-@}&pay_type={@-pay_type-@}&subject={@-subject-@}&user_ip={@-user_ip-@}&{@-PKMd5Key-@}"
    ],
    'Yaoqianshupay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&goods={@-goods-@}&mch_id={@-mch_id-@}&notify_url={@-notify_url-@}&order_no={@-order_no-@}&service={@-service-@}&key={@-PKMd5Key-@}"
    ],
    'Shilianpay' => [
        'space'    => '&',
        'singRule' => "mch_id={@-mch_id-@}&nonce={@-nonce-@}&notify_url={@-notify_url-@}&out_trade_no={@-out_trade_no-@}&return_url={@-return_url-@}&sign_type={@-sign_type-@}&spbill_create_ip={@-spbill_create_ip-@}&subject={@-subject-@}&timestamp={@-timestamp-@}&total_fee={@-total_fee-@}&trade_type={@-trade_type-@}&key={@-PKMd5Key-@}"
    ],
    'Dongfangpay' => [
        'space'    => '',
        'singRule' => "{@-money-@}{@-record-@}{@-appid-@}{@-PKMd5Key-@}"
    ],
    'Jifupay' => [
        'space'    => '&',
        'singRule' => "appId={@-appId-@}&appSecret={@-appSecret-@}&bizCode={@-bizCode-@}&merId={@-merId-@}&orderNo={@-orderNo-@}&orderPrice={@-orderPrice-@}&serviceType={@-serviceType-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Wanlianzfpay' => [
        'space'    => '&',
        'singRule' => "complete_time={@-complete_time-@}&currency={@-currency-@}&merchant_no={@-merchant_no-@}&ord_status={@-ord_status-@}&order_amount={@-order_amount-@}&order_no={@-order_no-@}&pay_amount={@-pay_amount-@}&pay_code={@-pay_code-@}&payment_trx_no={@-payment_trx_no-@}&product_name={@product_name@}&key={@-PKMd5Key-@}"
    ],
    'Guanbaozfpay' => [
        'space'    => '&',
        'singRule' => "cpParam={@-cpParam-@}&linkId={@-linkId-@}&money={@-money-@}&orderId={@-orderId-@}&orderIdCp={@-orderIdCp-@}&payTime={@-payTime-@}&status={@-status-@}&version={@-version-@}&{@-PKMd5Key-@}"
    ],
    'Kuaifupingpay' => [
        'space'    => '&',
        'singRule' => "Merid={@-Merid-@}&Merorderno={@-Merorderno-@}&Notifyurl={@-Notifyurl-@}&Paymentype={@-Paymentype-@}&Return_url={@-Return_url-@}&Toamount={@-Toamount-@}&key={@-PKMd5Key-@}"
    ],
    'Kuaitreepay' => [
        'space'    => '&',
        'singRule' => "member_name={@-member_name-@}&notifyUrl={@-notifyUrl-@}&openid={@-openid-@}&orderNo={@-orderNo-@}&orderPeriod={@-orderPeriod-@}&orderPrice={@-orderPrice-@}&orderTimestamp={@-orderTimestamp-@}&payType={@-payType-@}&productName={@-productName-@}&returnUrl={@returnUrl@}&key={@-PKMd5Key-@}"
    ],
    'Hengyangjuhepay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&applydate={@-applydate-@}&mchid={@-mchid-@}&msg={@-msg-@}&orderid={@-orderid-@}&out_trade_id={@-out_trade_id-@}&payamount={@-payamount-@}&status={@-status-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Qingguocpay' => [
        'space'    => '&',
        'singRule' => "body={@-body-@}&nonce_str={@-nonce_str-@}&out_trade_no={@-out_trade_no-@}&store_id={@-store_id-@}&total={@-total-@}&key={@-PKMd5Key-@}"
    ],
    'Zongfutpay' => [
        'space'    => '&',
        'singRule' => "parter={@-parter-@}&type={@-type-@}&value={@-value-@}&orderid={@-orderid-@}&callbackurl={@-callbackurl-@}{@-PKMd5Key-@}"
    ],
    'Yixinpay' => [
        'space'    => '&',
        'singRule' => "channel={@-channel-@}&command={@-command-@}&cpId={@-cpId-@}&description={@-description-@}&ip={@-ip-@}&money={@-money-@}&notifyUrl={@-notifyUrl-@}&orderIdCp={@-orderIdCp-@}&subject={@-subject-@}&timestamp={@-timestamp-@}&version={@-version-@}&{@-PKMd5Key-@}"
    ],
    'Jintongpay' => [
        'space'    => '&',
        'singRule' => "notifyUrl={@-notifyUrl-@}&orderNo={@-orderNo-@}&payAmt={@-payAmt-@}&returnUrl={@-returnUrl-@}&tradeType={@-tradeType-@}&userId={@-userId-@}&key={@-PKMd5Key-@}"
    ],
    'Zhongyuntpay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Huitongpay' => [
        'space'    => '&',
        'singRule' => "backNoticeUrl={@-backNoticeUrl-@}&merchantNo={@-merchantNo-@}&merchantOrderNo={@-merchantOrderNo-@}&merchantReqTime={@-merchantReqTime-@}&orderAmount={@-orderAmount-@}&payModel={@-payModel-@}&payType={@-payType-@}&tradeSummary={@-tradeSummary-@}&userIp={@-userIp-@}&userTerminal={@-userTerminal-@}{@-PKMd5Key-@}"
    ],
    'Huiyunzfpay' => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Yifubaopay' => [
        'space'    => '&',
        'singRule' => "body={@-body-@}&channelCode={@-channelCode-@}&clientIp={@-clientIp-@}&description={@-description-@}&mchntCode={@-mchntCode-@}&mchntOrderNo={@-mchntOrderNo-@}&notifyUrl={@-notifyUrl-@}&orderAmount={@-orderAmount-@}&orderExpireTime={@-orderExpireTime-@}&orderTime={@-orderTime-@}&pageUrl={@-pageUrl-@}&subject={@-subject-@}&ts={@-ts-@}{@-PKMd5Key-@}"
    ],
    'Guazipay' => [
        'space'    => '',
        'singRule' => "{@-orderid-@}{@-ordno-@}{@-price-@}{@-realprice-@}{@-PKMd5Key-@}"
    ],
    'Fengshoupay' => [
        'space'    => '',
        'singRule' => "{@-orderid-@}{@-ordno-@}{@-price-@}{@-realprice-@}{@-PKMd5Key-@}"
    ],
    'Jinmaopay' => [
        'space'    => '&',
        'singRule' => "goods_desc={@-goods_desc-@}&goods_name={@-goods_name-@}&merchant_no={@-merchant_no-@}&merchant_order_no={@-merchant_order_no-@}&notify_url={@-notify_url-@}&return_url={@-return_url-@}&sign_type={@-sign_type-@}&start_time={@-start_time-@}&trade_amount={@-trade_amount-@}&key={@-PKMd5Key-@}"
    ],
    'Koubeipay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&goodsName={@-goodsName-@}&merchno={@-merchno-@}&notifyUrl={@-notifyUrl-@}&payType={@-payType-@}&settleType={@-settleType-@}&traceno={@-traceno-@}&{@-PKMd5Key-@}"
    ],
    'Yinlinpay' => [
        'space'    => '&',
        'singRule' => "appid={@-appid-@}&money={@-money-@}&notifyurl={@-notifyurl-@}&orderid={@-orderid-@}&paycode={@-paycode-@}&returnurl={@-returnurl-@}&appkey={@-PKMd5Key-@}"
    ],
    'Jinbaobaopay' => [
        'space'    => '&',
        'singRule' => "apikey={@-PKMd5Key-@}&notify_url={@-notify_url-@}&order_id={@-order_id-@}&order_price={@-order_price-@}&return_url={@-return_url-@}&type={@-type-@}&user_id={@-user_id-@}"
    ],
    'Dingrongpay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Zhilianyipay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Qbjiupay' => [
        'space'    => '&',
        'singRule' => "merId={@-merId-@}&merNo={@-merNo-@}&status={@-status-@}&amout={@-amout-@}&sysNo={@-sysNo-@}{@-PKMd5Key-@}"
    ],
    'Lefuwxpay' => [
        'space'    => '&',
        'singRule' => "callbackUrl={@-callbackUrl-@}&cpId={@-cpId-@}&description={@-description-@}&fee={@-fee-@}&ip={@-ip-@}&notifyUrl={@-notifyUrl-@}&orderIdCp={@-orderIdCp-@}&payType={@-payType-@}&serviceId={@-serviceId-@}&subject={@-subject-@}&timestamp={@-timestamp-@}&version={@-version-@}&{@-PKMd5Key-@}"
    ],
    'Yiqufupay' => [
        'space'    => '&',
        'singRule' => "attach={@-attach-@}&merchantId={@-merchantId-@}&nonceStr={@-nonceStr-@}&orderState={@-orderState-@}&outTradeNo={@-outTradeNo-@}&payTime={@-payTime-@}&payType={@-payType-@}&totalFee={@-totalFee-@}&key={@-PKMd5Key-@}"
    ],
    'Yunjupay' => [
        'space'    => '&',
        'singRule' => "mer_id={@-mer_id-@}&nonce_str={@-nonce_str-@}&out_trade_no={@-out_trade_no-@}&total_fee={@-total_fee-@}&key={@-PKMd5Key-@}"
    ],
    'Kalapay' => [
        'space'    => '&',
        'singRule' => "shid={@-shid-@}&bb={@-bb-@}&zftd={@-zftd-@}&ddh={@-ddh-@}&je={@-je-@}&ddmc={@-ddmc-@}&ddbz={@-ddbz-@}&ybtz={@-ybtz-@}&tbtz={@-tbtz-@}&{@-PKMd5Key-@}"
    ],
    'Tianmapay' => [
        'space'    => '&',
        'singRule' => "app_id={@-app_id-@}&content={@-content-@}&method={@-method-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Otcpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&notify_url={@-notify_url-@}&order_no={@-order_no-@}&partner_id={@-partner_id-@}&pay_type={@-pay_type-@}&version={@-version-@}&{@-PKMd5Key-@}"
    ],
    'Yipingpay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Hetongpay' => [
        'space'    => '&',
        'singRule' => "code={@-code-@}&out_trade_no={@-out_trade_no-@}&partner={@-partner-@}&service_charge={@-service_charge-@}&state={@-state-@}&timestamp={@-timestamp-@}&total_fee={@-total_fee-@}&trade_no={@-trade_no-@}{@-PKMd5Key-@}"
    ],
    'Zhifupay' => [
        'space'    => '',
        'singRule' => "{@-uid-@}{@-price-@}{@-paytype-@}{@-notify_url-@}{@-return_url-@}{@-user_order_no-@}{@-PKMd5Key-@}"
    ],
    'Longtengpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&callback_url={@-callback_url-@}&client_ip={@-client_ip-@}&down_num={@-down_num-@}&notify_url={@-notify_url-@}&order_down={@-order_down-@}&pay_service={@-pay_service-@}&subject={@-subject-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Didipay' => [
        'space'    => '&',
        'singRule' => "notifyurl={@-notifyurl-@}&orderid={@-orderid-@}&partnerid={@-partnerid-@}&payamount={@-payamount-@}&payip={@-payip-@}&paytype={@-paytype-@}&returnurl={@-returnurl-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Baochengpay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Shunxinvpay' => [
        'space'    => '&',
        'singRule' => "appid={@-appid-@}&money={@-money-@}&notifyurl={@-notifyurl-@}&orderid={@-orderid-@}&paycode={@-paycode-@}&returnurl={@-returnurl-@}&appkey={@-PKMd5Key-@}"
    ],
    'Jinyunpay' => [
        'space'    => '&',
        'singRule' => "&assCancelUrl={@-assCancelUrl-@}&assCode={@-assCode-@}&assNotifyUrl={@-assNotifyUrl-@}&assPayMoney={@-assPayMoney-@}&assPayOrderNo={@-assPayOrderNo-@}&assReturnUrl={@-assReturnUrl-@}&paymentType={@-paymentType-@}&subPayCode={@-subPayCode-@}{@-PKMd5Key-@}"
    ],
    'Xilaipay' => [
        'space'    => '&',
        'singRule' => "pay_amount={@-pay_amount-@}&pay_applydate={@-pay_applydate-@}&pay_bankcode={@-pay_bankcode-@}&pay_callbackurl={@-pay_callbackurl-@}&pay_memberid={@-pay_memberid-@}&pay_notifyurl={@-pay_notifyurl-@}&pay_orderid={@-pay_orderid-@}&key={@-PKMd5Key-@}"
    ],
    'Yaxiyapay' => [
        'space'    => '',
        'singRule' => "{@-istype-@}{@-notify_url-@}{@-orderid-@}{@-price-@}{@-return_url-@}{@-PKMd5Key-@}{@-uid-@}"
    ],
    'Pilinpay' => [
        'space'    => '&',
        'singRule' => "&goodsname={@-goodsname-@}&istype={@-istype-@}&notify_url={@-notify_url-@}&orderid={@-orderid-@}&orderuid={@-orderuid-@}&price={@-price-@}&return_url={@-return_url-@}&token={@-PKMd5Key-@}&uid={@-uid-@}&version={@-version-@}"
    ],
    'Bifupay' => [
        'space'    => '&',
        'singRule' => "m_order_code={@-m_order_code-@}&order_status={@-order_status-@}&order_status_msg={@-order_status_msg-@}&pay_time={@-pay_time-@}&sys_order_code={@-sys_order_code-@}&total_fee={@-total_fee-@}{@-PKMd5Key-@}"
    ],
    'Jinlianzfpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Ebeizfpay' => [
        'space'    => '&',
        'singRule' => "merchantNo={@-merchantNo-@}&orderAmount={@-orderAmount-@}&orderNo={@-orderNo-@}&orderStatus={@-orderStatus-@}&payTime={@-payTime-@}&productName={@-productName-@}&wtfOrderNo={@-wtfOrderNo-@}{@-PKMd5Key-@}"
    ],
    'Hengrunfupay' => [
        'space'    => '|',
        'singRule' => "{@-appID-@}|{@-outTradeNo-@}|{@-payCode-@}|{@-payDatetime-@}|{@-productTitle-@}|{@-totalAmount-@}|{@-tradeCode-@}|{@-PKMd5Key-@}"
    ],
    'Youjiuzfpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Renxinzfpay' => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Tianlongzfpay' => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Shunyoufupay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&currency={@-currency-@}&merNo={@-merNo-@}&merOdNo={@-merOdNo-@}&notifyType={@-notifyType-@}&orderDate={@-orderDate-@}&orderNo={@-orderNo-@}&oriAmount={@-oriAmount-@}&tradeResult={@-tradeResult-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Chengzhifupay' => [
        'space'    => '&',
        'singRule' => "code={@-code-@}&currency={@-currency-@}&mchntId={@-mchntId-@}&mchntOrderId={@-mchntOrderId-@}&message={@-message-@}&orderId={@-orderId-@}&remarks={@-remarks-@}&success={@-success-@}&txnAmt={@-txnAmt-@}{@-PKMd5Key-@}"
    ],
    'Wuyoufupay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Jiefuzfpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Yunyipay' => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Weifubaopay' => [
        'space'    => '&',
        'singRule' => "{@-fxstatus-@}{@-fxid-@}{@-fxddh-@}{@-fxfee-@}{@-PKMd5Key-@}"
    ],
    'Xuanyangpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&applydate={@-applydate-@}&code={@-code-@}&mchid={@-mchid-@}&msg={@-msg-@}&orderid={@-orderid-@}&out_trade_id={@-out_trade_id-@}&payamount={@-payamount-@}&status={@-status-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Sanlianpay' => [
        'space'    => '&',
        'singRule' => "{@-amount-@}&{@-partner_id-@}&{@-out_trade_no-@}&{@-ProfitOrderNo-@}&{@-payType-@}&{@-PKMd5Key-@}"
    ],
    'Yifupay' => [
        'space'    => '&',
        'singRule' => "{@-orderno-@}{@-price-@}{@-realprice-@}{@-PKMd5Key-@}{@-trade_no-@}{@-transaction_no-@}"
    ],
    'Wujiuerpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Fengyizfpay' => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Changtongzfpay' => [
        'space'    => '&',
        'singRule' => "orderCode={@-orderCode-@}&orderPayStatus={@-orderPayStatus-@}&orderPrice={@-orderPrice-@}&organizationId={@-organizationId-@}&organizationOrderCode={@-organizationOrderCode-@}&payTime={@-payTime-@}&token={@-PKMd5Key-@}"
    ],
    'Zhihuibaopay' => [
        'space'    => '&',
        'singRule' => "status={@-status-@}&shid={@-shid-@}&bb={@-bb-@}&zftd={@-zftd-@}&ddh={@-ddh-@}&je={@-je-@}&ddmc={@-ddmc-@}&ddbz={@-ddbz-@}&ybtz={@-ybtz-@}&tbtz={@-tbtz-@}&{@-PKMd5Key-@}"
    ],
    'Babazfpay' => [
        'space'    => '&',
        'singRule' => "{@-PKMd5Key-@}{@-trade_no-@}{@-order_id-@}{@-channel-@}{@-money-@}{@-remark-@}{@-order_uid-@}{@-goods_name-@}"
    ],
    'Mayisjpay' => [
        'space'    => '&',
        'singRule' => "merId={@-merId-@}&merOrderNo={@-merOrderNo-@}&payTime={@-payTime-@}&status={@-status-@}&totalAmount={@-totalAmount-@}&tradeNo={@-tradeNo-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Kaixinzfpay' => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Caifutongpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&orderNo={@-orderNo-@}&transactionNo={@-transactionNo-@}#{@-PKMd5Key-@}"
    ],
    'Mayizfpay' => [
        'space'    => '&',
        'singRule' => "status={@-status-@}&shid={@-shid-@}&bb={@-bb-@}&zftd={@-zftd-@}&ddh={@-ddh-@}&je={@-je-@}&ddmc={@-ddmc-@}&ddbz={@-ddbz-@}&ybtz={@-ybtz-@}&tbtz={@-tbtz-@}&{@-PKMd5Key-@}"
    ],
    'Tianbaopay' => [
        'space'    => '&',
        'singRule' => "merchantCode={@-merchantCode-@}&orderAmount={@-orderAmount-@}&orderNo={@-orderNo-@}&orderTime={@-orderTime-@}&outTradeNo={@-outTradeNo-@}&payAmount={@-payAmount-@}&payStatus={@-payStatus-@}&serviceType={@-serviceType-@}&signType={@-signType-@}{@-PKMd5Key-@}"
    ],
    'Zhongyzfpay' => [
        'space'    => '&',
        'singRule' => "merchantCode={@-merchantCode-@}&orderAmount={@-orderAmount-@}&orderNo={@-orderNo-@}&orderTime={@-orderTime-@}&outTradeNo={@-outTradeNo-@}&payAmount={@-payAmount-@}&payStatus={@-payStatus-@}&serviceType={@-serviceType-@}&signType={@-signType-@}{@-PKMd5Key-@}"
    ],
    'Fspay' => [
        'space'    => '&',
        'singRule' => "cp_trade_no={@-cp_trade_no-@}&cpid={@-cpid-@}&fee={@-fee-@}&pay_transaction_id={@-pay_transaction_id-@}&pay_type={@-pay_type-@}&result_code={@-result_code-@}&key={@-PKMd5Key-@}"
    ],
    'Rongyingpay' => [
        'space'    => '&',
        'singRule' => "code={@-code-@}&goodsClauses={@-goodsClauses-@}&msg={@-msg-@}&nonStr={@-nonStr-@}&outOrderNo={@-outOrderNo-@}&shopCode={@-shopCode-@}&tradeAmount={@-tradeAmount-@}&key={@-PKMd5Key-@}"
    ],
    'Yinglipay' => [
        'space'    => '&',
        'singRule' => "amount=>{@-amount-@}&datetime=>{@-datetime-@}&memberid=>{@-memberid-@}&orderid=>{@-orderid-@}&returncode=>{@-returncode-@}&key={@-PKMd5Key-@}"
    ],
    'Sutongpay' => [
        'space'    => '&',
        'singRule' => "code={@-code-@}&goodsClauses={@-goodsClauses-@}&msg={@-msg-@}&nonStr={@-nonStr-@}&outOrderNo={@-outOrderNo-@}&shopCode={@-shopCode-@}&tradeAmount={@-tradeAmount-@}&key={@-PKMd5Key-@}"
    ],
    'Jinbaovpay' => [
        'space'    => '&',
        'singRule' => "payordid={@-payordid-@}&paystate={@-paystate-@}&paymoney={@-paymoney-@}&paytype={@-paytype-@}{@-PKMd5Key-@}"
    ],
    'Hengtongpay' => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&result={@-result-@}&amount={@-amount-@}&systemorderid={@-systemorderid-@}&completetime={@-completetime-@}&key={@-PKMd5Key-@}"
    ],
    'Liyingpay' => [
        'space'    => '&',
        'singRule' => "mch_id={@-mch_id-@}&nonce_str={@-nonce_str-@}&out_trade_no={@-out_trade_no-@}&time_end={@-time_end-@}&total_fee={@-total_fee-@}&trade_no={@-trade_no-@}&trade_state={@-trade_state-@}&trade_type={@-trade_type-@}&key={@-PKMd5Key-@}"
    ],
    'Xintbpay' => [
        'space'    => '&',
        'singRule' => "status={@-status-@}&shid={@-shid-@}&bb={@-bb-@}&zftd={@-zftd-@}&ddh={@-ddh-@}&je={@-je-@}&ddmc={@-ddmc-@}&ddbz={@-ddbz-@}&ybtz={@-ybtz-@}&tbtz={@-tbtz-@}&{@-PKMd5Key-@}"
    ],
    'Mayizfvepay' =>[
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Tianxinzfpay' => [
        'space'    => '&',
        'singRule' => "created_at={@-created_at-@}&merchant_order_no={@-merchant_order_no-@}&no={@-no-@}&order_no={@-order_no-@}&payable={@-payable-@}&price={@-price-@}&received={@-received-@}&status={@-status-@}&api_token={@-PKMd5Key-@}"
    ],
    'Kuaishunzfpay' =>[
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Akzfpay' =>[
        'space'    => '&',
        'singRule' => "banktype={@-banktype-@}&id={@-id-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}&sign={@-sign-@}&usernumber={@-usernumber-@}"
    ],
    'Longxinpay' =>[
        'space'    => '_',
        'singRule' => "{@-companyId-@}_{@-userOrderId-@}_{@-fee-@}_{@-PKMd5Key-@}"
    ],
    'Baoxunzpay' =>[
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Babbapay' =>[
        'space'    => '_',
        'singRule' => "{@-companyId-@}_{@-userOrderId-@}_{@-fee-@}_{@-PKMd5Key-@}"
    ],
    'Xinghpay' =>[
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Zbpay' =>[
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&result={@-result-@}&amount={@-amount-@}&systemorderid={@-systemorderid-@}&completetime={@-completetime-@}&key={@-PKMd5Key-@}"
    ],
    'Weisaopay' =>[
        'space'    => '&',
        'singRule' => "opstate={@-opstate-@}&orderid={@-orderid-@}&ovalue={@-ovalue-@}&parter={@-parter-@}&key={@-PKMd5Key-@}"
    ],
    'Dingxinzfpay' =>[
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}&key={@-PKMd5Key-@}"
    ],
    'Juyouvpay' => [
        'space'    => '',
        'singRule' => "{@-mid-@}{@-oid-@}{@-amt-@}{@-way-@}{@-code-@}{@-PKMd5Key-@}"
    ],
    'Facaifvpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Qianwanjiapay' => [
        'space'     => '&',
        'singRule'  => "orderNo={@-orderNo-@}&payAmt={@-payAmt-@}&retCode={@-retCode-@}&transNo={@-transNo-@}&userId={@-userId-@}&key={@-PKMd5Key-@}"
    ],
    'Jidianpay' => [
        'space'     => '&',
        'singRule'  => "amount={@-amount-@}&mch_code={@-mch_code-@}&out_trade_no={@-out_trade_no-@}&pay_date={@-pay_date-@}&status={@-status-@}&trans_date={@-trans_date-@}&key={@-PKMd5Key-@}"
    ],
    'Yitongbpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Lolpay' => [
    'space'    => '&',
    'singRule' => "{@-price-@}{@-orderNo-@}{@-sysOrderNo-@}&{@-PKMd5Key-@}"
    ],
    'Accpay' => [
    'space'    => '&',
    'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}}&@-PKMd5Key-@}"
    ],
    'Jindapay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Yibopay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Yijpay' => [
        'space'    => '&',
        'singRule' => "resultcode={@-resultcode-@}&transactionid={@-transactionid-@}&mchid={@-mchid-@}&mchno={@-mchno-@}&tradetype={@-tradetype-@}&totalfee={@-totalfee-@}&attach={@-attach-@}&key={@-PKMd5Key-@}"
    ],
    'Ylianzfpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Newdingshengpay' => [
        'space'    => '&',
        'singRule' => "merId={@-merId-@}&orderAmt={@-orderAmt-@}&orderNo={@-orderNo-@}&payDate={@-payDate-@}&payNo={@-payNo-@}&payStatus={@-payStatus-@}&payTime={@-payTime-@}&realAmt={@-realAmt-@}&remark1={@-remark1-@}&remark2={@-remark2-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Chengyipay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Ebaifupay' => [
        'space'    => '&',
        'singRule' => "{@-order_id-@}{@-price-@}{@-txnTime -@}{@-PKMd5Key-@}"
    ],
    'Sbzfpay' => [
        'space'    => '&',
        'singRule' => "merId={@-merId-@}&orderAmt={@-orderAmt-@}&orderNo={@-orderNo-@}&payDate={@-payDate-@}&payNo={@-payNo-@}&payStatus={@-payStatus-@}&payTime={@-payTime-@}&realAmt={@-realAmt-@}&remark1={@-remark1-@}&remark2={@-remark2-@}&version={@-version-@}&key={@-PKMd5Key-@}"
    ],
    'Jiedazfpay' => [
        'space'    => '&',
        'singRule' => "nonce={@-nonce-@}&ordercode={@-ordercode-@}&returncode={@-returncode-@}&total={@-total-@}&key={@-PKMd5Key-@}"
    ],
    'Kuaitingzfpay' => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&paytype={@-paytype-@}&realmoney={@-realmoney-@}&remark={@-remark-@}&sdorderno={@-sdorderno-@}&sdpayno={@-sdpayno-@}&status={@-status-@}&total_fee={@-total_fee-@}&{@-PKMd5Key-@}"
    ],
    'Xintengpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&attach={@-attach-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Wanglianpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Wandazfpay' => [
        'space'    => '+',
        'singRule' => "{@-orderid-@}+{@-orderuid-@}+{@-platform_trade_no-@}+{@-price-@}+{@-realprice-@}+{@-PKMd5Key-@}"
    ],
    'Mifengvpay' => [
        'space'    => '&',
        'singRule' => "code={@-code-@}&data={@-data-@}&msg={@-msg-@}{@-PKMd5Key-@}"
    ],
    'Xinshouxinpay' => [
        'space'    => '_',
        'singRule' => "{@-companyId-@}_{@-userOrderId-@}_{@-fee-@}_{@-PKMd5Key-@}"
    ],
    'Newnorthbearpay' => [
        'space'    => '&',
        'singRule' => "merchantNo={@-merchantNo-@}&no={@-no-@}&nonce={@-nonce-@}&timestamp={@-timestamp-@}&key={@-PKMd5Key-@}"
    ],
    'Yihaipay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&orderNo={@-orderNo-@}&transactionNo={@-transactionNo-@}#{@-PKMd5Key-@}"
    ],
    'Lianshengpay' => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Fuyintongpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Huijietongpay' => [
        'space'    => '.',
        'singRule' => "fxstatus={@-fxstatus-@}fxid={@-fxid-@}fxddh={@-fxddh-@}{@-PKMd5Key-@}"
    ],
    'Xinznypay' => [
        'space'    => '&',
        'singRule' => "{@-orderid-@}{@-orderuid-@}{@-yftNo-@}{@-price-@}{@-istype-@}{@-realprice-@}{@-createAt-@}{@-uid-@}{@-PKMd5Key-@}{@-notifyurl-@}"
    ],
    'Wangfubaopay' => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Xinyidapay' => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Xinmiaopay' => [
        'space'    => '&',
        'singRule' => "{@-out_order_no-@}{@-total_fee-@}{@-trade_status-@}{@-public_key-@}{@-PKMd5Key-@}"
    ],
    'Weifutpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&orderNo={@-orderNo-@}&transactionNo={@-transactionNo-@}#{@-PKMd5Key-@}"
    ],
    'Fajpay' => [
        'space'    => '&',
        'singRule' => "{@-shopAccountId-@}{@-user_id-@}{@-trade_no-@}{@-PKMd5Key-@}{@-money-@}{@-type-@}"
    ],
    'Hetpay' => [
        'space'    => '&',
        'singRule' => "code={@-code-@}&partner={@-partner-@}&out_trade_no={@-out_trade_no-@}&total_fee={@-total_fee-@}&service_charge={@-service_charge-@}&state={@-state-@}&trade_no={@-trade_no-@}{@-PKMd5Key-@}"
    ],
    'Huiyunonepay' => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Yidazpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Jinfafupay' => [
        'space'    => '&',
        'singRule' => "pay_order={@-pay_order-@}&order_id={@-order_id-@}&price={@-price-@}&pay_type={@-pay_type-@}&code={@-code-@}&timestamp={@-timestamp-@}&key={@-PKMd5Key-@}"
    ],
    'Kdpay' => [
        'space'    => '|',
        'singRule' => "{@-business_num-@}|{@-P_OrderId-@}|{@-P_CardId-@}|{@-P_CardPass-@}{@-PKMd5Key-@}"
    ],
    'Shanrubpay'   => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&orderuid={@-orderuid-@}&paysapi_id={@-paysapi_id-@}&price={@-price-@}&realprice={@-realprice-@}&token={@-PKMd5Key-@}"
    ],
    'Weifuzpay'   => [
        'space'    => '&',
        'singRule' => "merchantNo={@-merchantNo-@}&no={@-no-@}&nonce={@-nonce-@}&timestamp={@-timestamp-@}&key={@-PKMd5Key-@}"
    ],
    'Bbcpay'   => [
        'space'    => '&',
        'singRule' => "{@-orderid-@}{@-orderuid-@}{@-pay_id-@}{@-price-@}{@-realprice-@}{@-PKMd5Key-@}"
    ],
    'Bohzfpay'   => [
        'space'    => '&',
        'singRule' => "resultcode={@-resultcode-@}&transactionid={@-transactionid-@}&mchid={@-mchid-@}&mchno={@-mchno-@}&tradetype={@-tradetype-@}&totalfee={@-totalfee-@}&attach={@-attach-@}&key={@-PKMd5Key-@}"
    ],
    'Yjiazfpay'   => [
        'space'    => '&',
        'singRule' => "app_id={@-app_id-@}&ext={@-ext-@}&out_trade_no={@-out_trade_no-@}&status={@-status-@}&subject={@-subject-@}&time={@-time-@}&total_amount={@-total_amount-@}&trade_no={@-trade_no-@}&key={@-PKMd5Key-@}"
    ],
    'Firefoxpay'   => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Wantbpay'   => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Baoxpay'   => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Hefutongpay'   => [
        'space'    => '&',
        'singRule' => "{@-mch_id-@}{@-time_end-@}{@-out_trade_no-@}{@-ordernumber-@}{@-transtypeid-@}{@-transaction_id-@}{@-total_fee-@}{@-service-@}{@-way-@}{@-result_code-@}{@-PKMd5Key-@}"
    ],
    'Youninepay'   => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Xinshengzpay'   => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Uvpay'   => [
        'space'    => '&',
        'singRule' => "pid={@-pid-@}&cid={@-cid-@}&oid={@-oid-@}&sid={@-sid-@}&uid={@-uid-@}&amount={@-amount-@}&stime={@-stime-@}&code={@-code-@}&key={@-PKMd5Key-@}"
    ],
    'Zhiyujuhepay'   => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Jiujzfpay'   => [
        'space'    => '&',
        'singRule' => "attach={@-attach-@}&body={@-body-@}&merchantNo={@-merchantNo-@}&message={@-message-@}&orderNo={@-orderNo-@}&outTradeNo={@-outTradeNo-@}&payMoney={@-payMoney-@}&payTime={@-payTime-@}&resultCode={@-resultCode-@}&key={@-PKMd5Key-@}"
    ],
    'Vvpay'   => [
        'space'    => '.',
        'singRule' => "{@-xxorder-@}{@-order-@}{@-paytype-@}{@-fee-@}{@-paytm-@}{@-PKMd5Key-@}"
    ],
    'Xinmangopay'   => [
        'space'    => '&',
        'singRule' => "{@-fxstatus-@}{@-fxid-@}{@-fxddh-@}{@-fxfee-@}{@-PKMd5Key-@}"
    ],
    'Aabzfpay'   => [
        'space'    => '&',
        'singRule' => "{@-orderid-@}{@-orderuid-@}{@-pay_id-@}{@-price-@}{@-realprice-@}{@-PKMd5Key-@}"
    ],
    'Yifuapipay'   => [
        'space'    => '&',
        'singRule' => "{@-attach-@}{@-orderno-@}{@-orderuid-@}{@-price-@}{@-realprice-@}{@-PKMd5Key-@}{@-trade_no-@}{@-transaction_no-@}"
    ],
    'Kuaitongbaopay'   => [
        'space'    => '&',
        'singRule' => "{@-customerAmount-@}{@-customerAmountCny-@}{@-outOrderId-@}{@-orderId-@}{@-signType-@}{@-status-@}{@-PKMd5Key-@}"
    ],
    'Migopay'   => [
        'space'    => '&',
        'singRule' => "{@-ubozt-@}{@-uboid-@}{@-ubodingdan-@}{@-ubomoney-@}{@-PKMd5Key-@}"
    ],
    'Huidzfpay'   => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&mer_id={@-mer_id-@}&order_id={@-order_id-@}&pay_state={@-pay_state-@}&key={@-PKMd5Key-@}"
    ],
    'Weifengpay'   => [
        'space'    => '&',
        'singRule' => "amount=>{@-amount-@}&datetime=>{@-datetime-@}&memberid=>{@-memberid-@}&orderid=>{@-orderid-@}&returncode=>{@-returncode-@}&key={@-PKMd5Key-@}"
    ],
    'Weizhifupay'   => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Shunfuvpay'   => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Jifubpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Akzfvpay'   => [
        'space'    => '',
        'singRule' => "{@-fxstatus-@}{@-fxid-@}{@-fxddh-@}{@-fxfee-@}{@-PKMd5Key-@}"
    ],
    'Huoshanzpay'   => [
        'space'    => '&',
        'singRule' => "order_sn={@-order_sn-@}&pay_time={@-pay_time-@}&key={@-PKMd5Key-@}"
    ],
    'Nuomipay'   => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}{@-PKMd5Key-@}"
    ],
    'Yusanpay'   => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Cntpay'   => [
        'space'    => '|',
        'singRule' => "{@-userId-@}|{@-orderId-@}|{@-userOrder-@}|{@-number-@}|{@-merPriv-@}|{@-remark-@}|{@-date-@}|{@-resultCode-@}|{@-resultMsg-@}|{@-appID-@}|{@-PKMd5Key-@}"
    ],
    'Aidzfpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Anshengpay' => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&amount={@-amount-@}{@-PKMd5Key-@}"
    ],
    'Shangrubpay'   => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&orderuid={@-orderuid-@}&paysapi_id={@-paysapi_id-@}&price={@-price-@}&realprice={@-realprice-@}&token={@-PKMd5Key-@}"
    ],
    'Jinniuzfpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Hefuzpay' => [
        'space'    => '&',
        'singRule' => "merchant_id={@-merchant_id-@}&order_id={@-order_id-@}&amount={@-amount-@}&sign={@-PKMd5Key-@}"
    ],
    'Heyingpay' => [
        'space'    => '&',
        'singRule' => "{@-amount-@}&{@-partner_id-@}&{@-out_trade_no-@}&{@-ProfitOrderNo-@}&{@-payType-@}&{@-PKMd5Key-@}"
    ],
    'Starfirepay' => [
        'space'    => '',
        'singRule' => "{@-customerAmount-@}{@-customerAmountCny-@}{@-outOrderId-@}{@-orderId-@}{@-signType-@}{@-status-@}{@-PKMd5Key-@}"
    ],
    'Zhongqzfvpay' => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&restate={@-restate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Qibjzfvpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Haifpay' => [
        'space'    => '&',
        'singRule' => "customerid={@-customerid-@}&status={@-status-@}&sdpayno={@-sdpayno-@}&sdorderno={@-sdorderno-@}&total_fee={@-total_fee-@}&paytype={@-paytype-@}&{@-PKMd5Key-@}"
    ],
    'Anszfpay'        => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&amount={@-amount-@}{@-PKMd5Key-@}"
    ],
    'Heweizfpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Juheyipay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Shanfvspay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Sanliuwzfpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&key={@-PKMd5Key-@}&littlepay_id={@-littlepay_id-@}&order_no={@-order_no-@}&status={@-status-@}"
    ],
    'Changhepay' => [
        'space'    => '&',
        'singRule' => "merNum={@-merNum-@}&orderMoney={@-orderMoney-@}&orderNum={@-orderNum-@}&orderStatus={@-orderStatus-@}&key={@-PKMd5Key-@}"
    ],
    'Longshengpay' => [
        'space'    => '|',
        'singRule' => "{@-Amount-@}|{@-PayOrderNo-@}|{@-PayOrderTime-@}|{@-PKMd5Key-@}"
    ],
    'Jinxiangpay' => [
        'space'    => '',
        'singRule' => "{@-mid-@}{@-oid-@}{@-amt-@}{@-way-@}{@-code-@}{@-PKMd5Key-@}"
    ],
    'Jiaduobaopay' => [
        'space'    => '&',
        'singRule' => "mcode={@-mcode-@}&orderid={@-orderid-@}&amt={@-amt-@}&key={@-PKMd5Key-@}"
    ],
    'Yubfpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Baihuipay' => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&restate={@-restate-@}&ovalue={@-ovalue-@}{@-PKMd5Key-@}"
    ],
    'Yidazfupay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Changfupay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Fulzfpay' => [
        'space'    => '',
        'singRule' => "{@-order_id-@}{@-price-@}{@-txnTime-@}{@-PKMd5Key-@}"
    ],
    'Beijezfvpay' => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
    'Badatongpay' => [
        'space'    => '&',
        'singRule' => "childOrderno={@-childOrderno-@}&payAmount={@-payAmount-@}&orderStatus={@-orderStatus-@}&time={@-time-@}&{@-PKMd5Key-@}"
    ],
    'Anyifpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Yunyizpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Kuairupay'   => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&orderuid={@-orderuid-@}&paysapi_id={@-paysapi_id-@}&price={@-price-@}&realprice={@-realprice-@}&token={@-PKMd5Key-@}"
    ],
    'Delipay'  => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
    ],
	'Shunlianpay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Qianbfvpay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Huifutianxiapay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Jingniuzfpay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
		
    'Baitongpay' => [
       'space'    => '&',
       'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
   ],
	'Linglyzfpay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Hedepay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Zhichenpay' => [
		'space' => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}",
	],
	'Fuyifuvpay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&attach={@-attach-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Wanltpay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Qihangtzpay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Yibaozfpay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
    'Caifuzfpay' => [
        'space'    => '&',
        'singRule' => "status={@-status-@}&shid={@-shid-@}&bb={@-bb-@}&zftd={@-zftd-@}&ddh={@-ddh-@}&je={@-je-@}&ddmc={@-ddmc-@}&ddbz={@-ddbz-@}&ybtz={@-ybtz-@}&tbtz={@-tbtz-@}&{@-PKMd5Key-@}"
    ],
	'Taishanhypay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Gmspay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Sufuvpay' => [
		'space'    => '&',
		'singRule' => "amount=>{@-amount-@}&datetime=>{@-datetime-@}&memberid=>{@-memberid-@}&orderid=>{@-orderid-@}&returncode=>{@-returncode-@}&key={@-PKMd5Key-@}"
	],
    'Xinweilcpay'        => [
        'space'    => '&',
        'singRule' => "sErrorCode={@-sErrorCode-@}&bType={@-bType-@}&ForUserId={@-ForUserId-@}&LinkID={@-LinkID-@}&Moneys={@-Moneys-@}&AssistStr={@-AssistStr-@}&keyValue={@-PKMd5Key-@}"
    ],
    'Zhenxinpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
	'Yihuibpay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
    'Yuanquanzfpay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
	'Xinlefupay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Yimjlpay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Juxingbaopay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
	'Qianbfvvpay' => [
		'space'    => '&',
		'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
	],
    'Kaixinpay' => [
        'space'    => '&',
        'singRule' => "money={@-money-@}&name={@-name-@}&out_trade_no={@-out_trade_no-@}&pid={@-pid-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&type={@-type-@}{@-PKMd5Key-@}"
    ],
    'Fengwozpay' => [
        'space'    => '&',
        'singRule' => "code={@-code-@}&data={@-data-@}&msg={@-msg-@}{@-PKMd5Key-@}"
    ],
    'Jisufupay'    =>[
        'space'    => '',
        'singRule' => "{@-orderid-@}{@-platform_trade_no-@}{@-price-@}{@-realprice-@}{@-PKMd5Key-@}"
    ],
    'Changchengzfpay' => [
        'space'    => '&',
        'singRule' => "actual_amount={@-actual_amount-@}&amount={@-amount-@}&appid={@-appid-@}&attach={@-attach-@}&order_no={@-order_no-@}&pay_status={@-pay_status-@}&pay_time={@-pay_time-@}&platform_order_no={@-platform_order_no-@}&secret={@-PKMd5Key-@}"
    ],
    'Xinjinqiupay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Wanlibaopay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Sangeyipay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Qihangjuhepay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&datetime={@-datetime-@}&memberid={@-memberid-@}&orderid={@-orderid-@}&returncode={@-returncode-@}&transaction_id={@-transaction_id-@}&key={@-PKMd5Key-@}"
    ],
    'Mangfutangpay'  => [
        'space'    => '&',
        'singRule' => "partner={@-partner-@}&ordernumber={@-ordernumber-@}&orderstatus={@-orderstatus-@}&paymoney={@-paymoney-@}{@-PKMd5Key-@}"
     ],
    'Xunziufupay'       => [
        'space'    => '&',
        'singRule' => "orderid={@-orderid-@}&opstate={@-opstate-@}&ovalue={@-ovalue-@}&time={@-systime-@}&sysorderid={@-sysorderid-@}{@-PKMd5Key-@}"
    ],
];

