<?php

namespace App\Models\Order;

use App\Models\ApiModel;
use App\Models\Config\PayType;
use App\Models\Config\ApiClients;
use App\Models\Config\PayConfig;
use Illuminate\Support\Facades\DB;

class InOrder extends ApiModel
{
    protected $table = 'pay_order';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    /**
     * 一对多 关联pay_type
     */
    public function payType()
    {
        return $this->belongsTo(PayType::class,'pay_way','type_id');
    }

    /**
     * 平台线路
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function apiClients(){
        return $this->belongsTo(ApiClients::class,'client_user_id','user_id');
    }

    /**
     * 商户类型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payConfigs(){
        return $this->belongsTo(PayConfig::class,'pay_id','pay_id');
    }

    public static function orderQuery(array $where)
    {
        $result = DB::table('pay_order')
            ->leftJoin('pay_type','pay_type.type_id','=','pay_order.pay_way')
            ->leftJoin('pay_config','pay_config.pay_id','=','pay_order.pay_id')
            ->select('pay_order.order_number','pay_order.money','pay_order.business_num','pay_order.is_status','pay_type.type_name',
                'pay_type.created_at','pay_config.conf_name')
            ->where($where)
            ->get();
        InOrder::_destroy();
        return $result;
    }

    /**
     * 入款订单数据伪装
     * @param $inOrders
     * @return array
     */
    public static function dataCamouflage($inOrders){
        $data = [];
        if ($inOrders) {
            foreach ($inOrders as $key => $inOrder) {
                $data[$key]['inOrderId']     = (int)$inOrder->id;
                $data[$key]['agentId']       = (string)$inOrder->agent_id;
                $data[$key]['clientUserId']  = (int)$inOrder->client_user_id;
                $data[$key]['payId']         = (int)$inOrder->pay_id;
                $data[$key]['merchantId']    = (int)$inOrder->merchant_id;
                $data[$key]['orderNumber']   = (string)$inOrder->order_number;
                $data[$key]['owOrderNumber'] = (string)$inOrder->ow_order_number;
                $data[$key]['payMoney']      = (string)$inOrder->money;
                $data[$key]['callbackUrl']   = (string)$inOrder->callback_url;
                $data[$key]['businessNum']   = (string)$inOrder->business_num;
                $data[$key]['isStatus']      = (int)$inOrder->is_status;
                $data[$key]['payWay']        = (int)$inOrder->pay_way;
                $data[$key]['isSued']        = (int)$inOrder->issued;
                $data[$key]['createdAt']     = (string)$inOrder->created_at;
                $data[$key]['updatedAt']     = (string)$inOrder->updated_at;
                //平台线路 - 模型关联
                $data[$key]['clientName']    = $inOrder->apiClients['client_name'];
                //支付方式 - 模型关联
                $data[$key]['typeName']      = $inOrder->payType['type_name'];
                //商户类型 - 模型关联
                $data[$key]['confName']      = $inOrder->payConfigs['conf_name'];
            }
        }

        return $data;
    }

}