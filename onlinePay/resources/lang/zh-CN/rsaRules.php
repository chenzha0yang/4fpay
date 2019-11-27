<?php
/**
 * 验签规则
 */

return [
    'Wofupay' => [
        'space'    => '&', // 分隔符
        // 签名规则
        'singRule' => "bank_seq_no={@-bank_seq_no-@}&extra_return_param={@-extra_return_param-@}&interface_version={@-interface_version-@}&"
        ."merchant_code={@-merchant_code-@}&notify_id={@-notify_id-@}&notify_type={@-notify_type-@}&"
        ."order_amount={@-order_amount-@}&order_no={@-order_no-@}&order_time={@-order_time-@}&trade_no={@-trade_no-@}&"
        ."trade_status={@-trade_status-@}&trade_time={@-trade_time-@}"
        ],
    'Dinpayrsa' => [
        'space'    => '&',
        'singRule' => "bank_seq_no={@-bank_seq_no-@}&extra_return_param={@-extra_return_param-@}&interface_version={@-interface_version-@}&"
            ."merchant_code={@-merchant_code-@}&notify_id={@-notify_id-@}&notify_type={@-notify_type-@}&"
            ."order_amount={@-order_amount-@}&order_no={@-order_no-@}&order_time={@-order_time-@}&trade_no={@-trade_no-@}&"
            ."trade_status={@-trade_status-@}&trade_time={@-trade_time-@}",
    ],
    'Suhuibaopay' => [
        'space'    => '&',
        'singRule' => "bank_seq_no={@-bank_seq_no-@}&client_ip={@-client_ip-@}&extend_param={@-extend_param-@}&extra_return_param={@-extra_return_param-@}&"
            ."interface_version={@-interface_version-@}&merchant_code={@-merchant_code-@}&notify_id={@-notify_id-@}&notify_type={@-notify_type-@}&"
            ."order_amount={@-order_amount-@}&order_no={@-order_no-@}&order_time={@-order_time-@}&trade_no={@-trade_no-@}&"
            ."trade_status={@-trade_status-@}&trade_time={@-trade_time-@}",
    ],
    'Duodebaopay' => [
        'space'    => '&',
        'singRule' => "bank_seq_no={@-bank_seq_no-@}&interface_version={@-interface_version-@}&merchant_code={@-merchant_code-@}&notify_id={@-notify_id-@}"
            ."&notify_type={@-notify_type-@}&order_amount={@-order_amount-@}&order_no={@-order_no-@}&order_time={@-order_time-@}"
            ."&orginal_money={@-orginal_money-@}&trade_no={@-trade_no-@}&trade_status={@-trade_status-@}&trade_time={@-trade_time-@}",
    ],
    'Weifupay' => [
        'space'    => '&', // 分隔符
        // 签名规则
        'singRule' => "bank_seq_no={@-bank_seq_no-@}&extra_return_param={@-extra_return_param-@}&interface_version={@-interface_version-@}&"
            ."merchant_code={@-merchant_code-@}&notify_id={@-notify_id-@}&notify_type={@-notify_type-@}&"
            ."order_amount={@-order_amount-@}&order_no={@-order_no-@}&order_time={@-order_time-@}&trade_no={@-trade_no-@}&"
            ."trade_status={@-trade_status-@}&trade_time={@-trade_time-@}"
    ],
    'Feimiaofupay' => [
        'space'    => '&',
        'singRule' => "amount={@-amount-@}&merchant_id={@-merchant_id-@}&merchant_order_id={@-merchant_order_id-@}",
    ],
    'Rongcanpay' => [
        'space'    => '&', // 分隔符
        // 签名规则
        'singRule' => "merchantCode={@-merchantCode-@}&orderNo={@-orderNo-@}&amount={@-amount-@}&"
            ."successAmt={@-successAmt-@}&payOrderNo={@-payOrderNo-@}&orderStatus={@-orderStatus-@}&"
            ."extraReturnParam={@-extraReturnParam-@}"
    ],
    'Jufupay' => [
        'space'    => '&', // 分隔符
        // 签名规则
        'singRule' => "tradeSeq={@-tradeSeq-@}&signType={@-signType-@}&returnDatetime={@-returnDatetime-@}&"
            ."payResult={@-payResult-@}&payDatetime={@-payDatetime-@}&partnerId={@-partnerId-@}&"
            ."orderNo={@-orderNo-@}&orderDatetime={@-orderDatetime-@}&orderAmount={@-orderAmount-@}&"
            ."inputCharset={@-inputCharset-@}&extraCommonParam={@-extraCommonParam-@}"
    ],
    'Shengyaopay' => [
        'space'    => '&', // 分隔符
        // 签名规则
        'singRule' => "Amount={@-Amount-@}&BusinessOrders={@-BusinessOrders-@}&Description={@-Description-@}&"
            ."Merchants={@-Merchants-@}&OrderStatus={@-OrderStatus-@}&OrderTime={@-OrderTime-@}&"
            ."PayTime={@-PayTime-@}&PostService={@-PostService-@}&SenyoOrder={@-SenyoOrder-@}&"
            ."SubmitIP={@-SubmitIP-@}&TypeService={@-TypeService-@}"
    ],
    'Yidapay' => [
        'space'    => '&', // 分隔符
        // 签名规则
        'singRule' => "extraCommonParam={@-extraCommonParam-@}&inputCharset={@-inputCharset-@}&orderAmount={@-orderAmount-@}&"
            ."orderDatetime={@-orderDatetime-@}&orderNo={@-orderNo-@}&partnerId={@-partnerId-@}&"
            ."payDatetime={@-payDatetime-@}&payResult={@-payResult-@}&returnDatetime={@-returnDatetime-@}&"
            ."signType={@-signType-@}&tradeSeq={@-tradeSeq-@}"
    ],
    'Kenuo' => [
        'space'    => '&', // 分隔符
        // 签名规则
        'singRule' => "apps_id={@-apps_id-@}&body={@-body-@}&extra={@-extra-@}&"
            ."mer_id={@-mer_id-@}&notify_url={@-notify_url-@}&out_trade_no={@-out_trade_no-@}&"
            ."return_url={@-return_url-@}&show_url={@-show_url-@}&subject={@-subject-@}&"
            ."total_fee={@-total_fee-@}&user_id={@-user_id-@}"
    ],
    'Wofuvvpay' => [
        'space'    => '&', // 分隔符
        // 签名规则
        'singRule' => "bank_seq_no={@-bank_seq_no-@}&extra_return_param={@-extra_return_param-@}&interface_version={@-interface_version-@}&"
            ."merchant_code={@-merchant_code-@}&notify_id={@-notify_id-@}&notify_type={@-notify_type-@}&"
            ."order_amount={@-order_amount-@}&order_no={@-order_no-@}&order_time={@-order_time-@}&trade_no={@-trade_no-@}&"
            ."trade_status={@-trade_status-@}&trade_time={@-trade_time-@}"
    ],
];


