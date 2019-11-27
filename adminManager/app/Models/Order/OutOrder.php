<?php

namespace App\Models\Order;

use App\Models\ApiModel;
use App\Models\Config\ApiClients;
use App\Models\Config\PayConfig;

class OutOrder extends ApiModel
{
    protected $table = 'out_order';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    /**
     * 平台线路
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function apiClients(){
        return $this->belongsTo(ApiClients::class,'client_user_id','id');
    }

    /**
     * 商户类型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payConfigs(){
        return $this->belongsTo(PayConfig::class,'pay_id','pay_id');
    }

    /**
     * 出款订单数据伪装
     * @param $outOrders
     * @return array
     */
    public static function dataCamouflage($outOrders){
        $data = [];
        if ($outOrders) {
            foreach ($outOrders as $key => $outOrder) {
                $data[$key]['outOrderId']   = (int)$outOrder->id;
                $data[$key]['agentId']      = (string)$outOrder->agent_id;
                $data[$key]['agentNum']     = (string)$outOrder->agent_num;
                $data[$key]['clientUserId'] = (int)$outOrder->client_user_id;
                $data[$key]['payId']        = (int)$outOrder->pay_id;
                $data[$key]['merchantId']   = (int)$outOrder->merchant_id;
                $data[$key]['orderNumber']  = (string)$outOrder->order_number;
                $data[$key]['payMoney']     = (string)$outOrder->money;
                $data[$key]['callbackUrl']  = (string)$outOrder->callback_url;
                $data[$key]['bankCard']     = (string)$outOrder->bank_card;
                $data[$key]['businessNum']  = (string)$outOrder->business_num;
                $data[$key]['inFo']         = (string)$outOrder->info;
                $data[$key]['isStatus']     = (int)$outOrder->is_status;
                $data[$key]['isSued']       = (int)$outOrder->issued;
                $data[$key]['createdAt']    = (string)$outOrder->created_at;
                $data[$key]['updatedAt']    = (string)$outOrder->updated_at;
                //平台线路
                $data[$key]['clientName']   = $outOrder->apiClients->client_name;
                //商户类型
                $data[$key]['confName']     = $outOrder->payConfigs->conf_name;
            }
        }

        return $data;
    }
}