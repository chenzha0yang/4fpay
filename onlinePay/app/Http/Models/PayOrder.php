<?php

namespace App\Http\Models;

use App\Extensions\Common;
use Illuminate\Database\Eloquent\Model;

class PayOrder extends Model
{
    use Common;

    protected $table = 'pay_order';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * 查找订单号是否重复
     *
     * @param $order
     * @return bool
     */
    public static function findOrder($order)
    {
        $date      = self::getIntervalDate();
        $orderData = self::where('created_at', '>=', $date['start'])
            ->where('created_at', '<=', $date['end'])
            ->where('order_number', $order)
            ->first();
        if ($orderData) {
            return true;
        }

        return false;
    }

    /**
     * 获取单个订单
     *
     * @param string $order
     * @return mixed
     */
    public static function getOrderData($order = '')
    {
        $date = self::getIntervalDate();
        return self::where('created_at', '>=', $date['start'])
            ->where('created_at', '<=', $date['end'])
            ->where('order_number', $order)
            ->where('is_status', 0)
            ->first();
    }

    /**
     *
     * 写入订单
     * @param $payMerchant
     * @param $PKData
     * @param $owOrderNum
     * @return $this|Model
     */
    public static function createOrder($payMerchant, $PKData, $owOrderNum)
    {
        $data['pay_id']          = (int)$payMerchant->pay_id; // 支付通道
        $data['merchant_id']     = (int)$payMerchant->merchant_id; // 商户信息主键
        $data['order_number']    = $PKData['order']; // 商户订单号
        $data['ow_order_number'] = $owOrderNum; // 平台订单号
        $data['money']           = $PKData['amount']; // 金额
        $data['business_num']    = $PKData['businessNum']; // 商户号
        $data['pay_way']         = (int)$PKData['payWay']; // 支付方式
        $data['callback_url']    = $PKData['notifyUrl']; // 异步通知
        $data['agent_id']        = $PKData['agentId']; // 代理线
        $data['agent_num']       = $PKData['agentNum']; // 子代理线
        $data['client_user_id']  = (int)$PKData['clientUserId']; // 线路
        return self::create($data);
    }

    /**
     * 更改订单状态
     *
     * @param PayOrder $PayOrder
     * @param string $column
     * @param int $value
     * @return bool
     */
    public static function updateOrder(self $PayOrder, string $column, int $value)
    {
        $PayOrder->{$column} = $value;
        return $PayOrder->save();
    }
}
