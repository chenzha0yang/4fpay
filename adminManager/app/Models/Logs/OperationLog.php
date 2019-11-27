<?php

namespace App\Models\Logs;

use App\Models\ApiModel;
use App\Models\Auth\Users;
use App\Models\Config\ApiClients;

class OperationLog extends ApiModel
{
    protected $table = 'admin_operation_log';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    /**
     * 后台用户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function users(){
        return $this->belongsTo(Users::class,'user_id','id');
    }

    /**
     * 平台线路
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function apiClients(){
        return $this->belongsTo(ApiClients::class,'client_id','id');
    }

    /**
     * 操作日志数据伪装
     * @param $operationLogs
     * @return array
     */
    public static function dataCamouflage($operationLogs){
        $data = [];
        if ($operationLogs) {
            foreach ($operationLogs as $key => $operationLog) {
                $data[$key]['operationId']   = (int)$operationLog->id;
                $data[$key]['userId']        = (int)$operationLog->user_id;
                $data[$key]['Path']          = (string)$operationLog->path;
                $data[$key]['Method']        = (string)$operationLog->method;
                $data[$key]['operationIp']   = (string)$operationLog->ip;
                $data[$key]['Input']         = (string)$operationLog->input;
                $data[$key]['createdAt']     = (string)$operationLog->created_at;
                $data[$key]['updatedAt']     = (string)$operationLog->updated_at;
                //用户名
                $data[$key]['account']       = $operationLog->users['username'];
                if((string)$operationLog->agent_id == '0' && (int)$operationLog->client_id === 0){
                    $data[$key]['clientName']   = trans('lang.adminClient')[0];
                    $data[$key]['agentId']      = trans('lang.adminClient')[0];
                }
                if((string)$operationLog->agent_id == '0' && (int)$operationLog->client_id != 0){
                    $data[$key]['clientName']   = (string)$operationLog->apiClients['client_name'];
                    $data[$key]['agentId']      = trans('lang.adminClient')[1];
                }
                if((string)$operationLog->agent_id !== '0' && (int)$operationLog->client_id != 0){
                    $data[$key]['clientName']   = (string)$operationLog->apiClients['client_name'];
                    $data[$key]['agentId']      = (string)$operationLog->agent_id;
                }
            }
        }

        return $data;
    }

}