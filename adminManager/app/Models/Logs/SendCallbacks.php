<?php

namespace App\Models\Logs;

use App\Models\ApiModel;
use App\Models\Config\ApiClients;

class SendCallbacks extends ApiModel
{
    protected $table = 'send_callback_log';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    /**
     * 下发日志列表数据伪装
     * @param $sendCallbackLogs
     * @return array
     */
    public static function dataCamouflage($sendCallbackLogs){
        $data = [];
        if ($sendCallbackLogs) {
            foreach ($sendCallbackLogs as $key => $sendCallbackLog) {
                $data[$key]['sendCallbackId']   = (int)$sendCallbackLog->id;
                $data[$key]['orderNum']         = (string)$sendCallbackLog->order;
                $data[$key]['callbackUrl']      = (string)$sendCallbackLog->callback_url;
                $data[$key]['sendMsg']          = (string)$sendCallbackLog->send_msg;
                $data[$key]['httpCode']         = (string)$sendCallbackLog->http_code;
                $data[$key]['returnMsg']        = (string)$sendCallbackLog->return_msg;
                $data[$key]['isAutoSend']       = (int)$sendCallbackLog->is_auto_send;
                $data[$key]['way']              = (int)$sendCallbackLog->way;
                $data[$key]['clientId']         = (int)$sendCallbackLog->client_id;
                $data[$key]['createdAt']        = (string)$sendCallbackLog->created_at;
                $data[$key]['updatedAt']        = (string)$sendCallbackLog->updated_at;
                if((string)$sendCallbackLog->agent_id == '0' && (int)$sendCallbackLog->client_id === 0){
                    $data[$key]['clientName']   = trans('lang.adminClient')[0];
                    $data[$key]['agentId']      = trans('lang.adminClient')[0];
                }
                if((string)$sendCallbackLog->agent_id == '0' && (int)$sendCallbackLog->client_id != 0){
                    $data[$key]['clientName']   = (string)$sendCallbackLog->apiClients['client_name'];
                    $data[$key]['agentId']      = trans('lang.adminClient')[1];
                }
                if((string)$sendCallbackLog->agent_id !== '0' && (int)$sendCallbackLog->client_id != 0){
                    $data[$key]['clientName']   = (string)$sendCallbackLog->apiClients['client_name'];
                    $data[$key]['agentId']      = (string)$sendCallbackLog->agent_id;
                }
            }
        }

        return $data;
    }

    /**
     * 下发日志
     * @param  string $order 订单号
     * @param  string $returnMsg 响应信息
     * @param  array $array 下发数据
     * @param  string $callbackUrl 下发地址
     * @param  int $issue 是否自动下发 1: 自动; 2: 手动
     * @param  int $way 下发方式：1 入款; 2 出款下发
     * @return SendCallbackLog|Model
     */
    public static function createLog($order, $returnMsg, $array, $callbackUrl, $issue, $way)
    {
        if ($returnMsg === 'SUCCESS') {
            $httpCode = 200;
        } else {
            $httpCode = $returnMsg;
        }
        $data['order']        = $order;
        $data['return_msg']   = $returnMsg;
        $data['send_msg']     = json_encode($array);
        $data['callback_url'] = $callbackUrl;
        $data['http_code']    = $httpCode;
        $data['is_auto_send'] = $issue;
        $data['way']          = $way;
        return self::create($data);
    }
}