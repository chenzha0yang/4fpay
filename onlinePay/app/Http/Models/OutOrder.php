<?php

namespace App\Http\Models;

use App\Extensions\Common;
use App\Http\Controllers\V1\SendOutCallbackController;
use Illuminate\Database\Eloquent\Model;

class OutOrder extends Model
{
    use Common;

    protected $table = 'out_order';

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
        $orderData = self::where('order_number', $order)->first();
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
            ->first();
    }

    /**
     * 写入订单
     *
     * @param $payMerchant
     * @param $PKData
     * @return $this|Model
     */
    public static function createOrder($payMerchant, $PKData)
    {
        $info                   = [
            'bankName' => $PKData['bankName'],
            'cardName' => $PKData['cardName'],
            'bankCode' => $PKData['bankCode']
        ];
        $data['pay_id']         = (int)$payMerchant->pay_id;
        $data['merchant_id']    = (int)$payMerchant->merchant_id;
        $data['order_number']   = $PKData['order'];
        $data['money']          = $PKData['amount'];
        $data['business_num']   = $PKData['businessNum'];
        $data['bank_card']      = $PKData['bankCard'];
        $data['info']           = json_encode($info);
        $data['callback_url']   = $PKData['notifyUrl'];
        $data['agent_id']       = $PKData['agentId'];
        $data['agent_num']      = $PKData['agentNum'];
        $data['client_user_id'] = (int)$PKData['clientUserId'];
        return self::create($data);
    }

    /**
     * 更改订单状态
     * @param self $PayOrder
     * @param      $array
     * @return bool
     */
    public static function updateOrder(self $PayOrder, $array)
    {
        foreach ($array as $key => $value) {
            $PayOrder->{$key} = $value;
        }

        return $PayOrder->save();
    }

    /**
     * 修改订单下发状态
     *
     * @param $object
     * @param string $callResponse
     * @return mixed
     */
    public static function editOrderIssued($object, $callResponse = '')
    {
        if ($callResponse == 'SUCCESS') {
            $object->issued = 1;
            SendOutCallbackController::setCallMessage([
                'status'  => true,
                'message' => trans('error.issued')[1],
            ]);
        } else {
            $object->issued = 2;
            SendOutCallbackController::setCallMessage(trans('error.issued')[2]);
        }
        return $object->save();
    }

    /**
     * 失败 ，更改订单状态
     *
     * @param $order 订单
     * @param $res   失败信息
     */
    public static function updateErrorOrder($order, $res)
    {
        $data['is_status'] = 2;
        $data['remark']    = $res;
        self::where('order_number', $order)->update($data);
    }

}