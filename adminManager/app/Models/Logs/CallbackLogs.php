<?php

namespace App\Models\Logs;

use App\Models\ApiModel;
use App\Models\Config\ApiClients;
use App\Models\Config\PayConfig;
use App\Models\Config\PayType;

class CallbackLogs extends ApiModel
{
    protected $table = 'pay_callback_msg';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    /**
     * 平台线路
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function apiClients(){
        return $this->belongsTo(ApiClients::class,'client_id','user_id');
    }

    /**
     * 商户类型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payConfigs(){
        return $this->belongsTo(PayConfig::class,'pay_id','pay_id');
    }

    /**
     * 支付方式
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payTypes()
    {
        return $this->belongsTo(PayType::class,'pay_way','type_id');
    }

    /**
     * 回调日志数据伪装
     * @param $callbackLogs
     * @return array
     */
    public static function dataCamouflage($callbackLogs){
        //数据伪装
        $data = [];
        if ($callbackLogs) {
            foreach ($callbackLogs as $key => $callbackLog) {
                $data[$key]['operationId']  = (int)$callbackLog->id;
                $data[$key]['orderNumber']  = (string)$callbackLog->order_number;
                $data[$key]['payId']        = (int)$callbackLog->pay_id;
                $data[$key]['callbackMsg']  = (string)$callbackLog->callback_msg;
                $data[$key]['callbackIp']   = (string)$callbackLog->callback_ip;
                $data[$key]['errorCode']    = (int)$callbackLog->error_code;
                $data[$key]['callbackUrl']  = (string)$callbackLog->callback_url;
                $data[$key]['payWay']       = (int)$callbackLog->pay_way;
                $data[$key]['clientId']     = (int)$callbackLog->client_id;
                $data[$key]['createdAt']    = (string)$callbackLog->created_at;
                $data[$key]['updatedAt']    = (string)$callbackLog->updated_at;
                if((string)$callbackLog->agent_id == '0' && (int)$callbackLog->client_id === 0){
                    $data[$key]['clientName']   = trans('lang.adminClient')[0];
                    $data[$key]['agentId']      = trans('lang.adminClient')[0];
                }
                if((string)$callbackLog->agent_id == '0' && (int)$callbackLog->client_id != 0){
                    $data[$key]['clientName']   = (string)$callbackLog->apiClients['client_name'];
                    $data[$key]['agentId']      = trans('lang.adminClient')[1];
                }
                if((string)$callbackLog->agent_id !== '0' && (int)$callbackLog->client_id != 0){
                    $data[$key]['clientName']   = (string)$callbackLog->apiClients['client_name'];
                    $data[$key]['agentId']      = (string)$callbackLog->agent_id;
                }
                //支付方式
                $data[$key]['typeName']     = $callbackLog->payTypes->type_name;
                //商户类型
                $data[$key]['confName']     = $callbackLog->payConfigs->conf_name;
            }
        }

        return $data;
    }

}