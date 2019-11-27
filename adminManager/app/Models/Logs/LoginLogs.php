<?php

namespace App\Models\Logs;

use App\Models\ApiModel;
use App\Models\Config\ApiClients;
use Illuminate\Http\Request;

class LoginLogs extends ApiModel
{
    protected $table = 'admin_login_log';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    /**
     * 平台线路
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function apiClients(){
        return $this->belongsTo(ApiClients::class,'client_id','id');
    }

    /**
     * 添加管理员登陆日志
     * @param $adminUser
     * @param $key
     * @param $password
     * @return bool
     */
    public static function LogInfo($adminUser,$key,$password)
    {
        $jsonData = [
            'username'  => $adminUser['username'],
            'password'  => $password,
            'token'     => $key,
        ];
        LoginLogs::$data = [
            'user_id'       => $adminUser['id'],
            'user_name'     => $adminUser['username'],
            'login_ip'      => self::getClientIp(0, true),
            'agent_id'      => $adminUser['agent_id'],
            'client_id'     => $adminUser['client_id'],
            'login_message' => json_encode($jsonData),
        ];

        return LoginLogs::addToData();
    }

    /**
     * 登陆日志数据伪装
     * @param $loginLogs
     * @return array
     */
    public static function dataCamouflage($loginLogs){
        $data = [];
        if ($loginLogs) {
            foreach ($loginLogs as $key => $loginLog) {
                $data[$key]['LoginId']      = (int)$loginLog->id;
                $data[$key]['userId']       = (int)$loginLog->user_id;
                $data[$key]['account']      = (string)$loginLog->user_name;
                $data[$key]['loginIp']      = (string)$loginLog->login_ip;
                $data[$key]['loginMessage'] = (string)$loginLog->login_message;
                $data[$key]['clientId']     = (int)$loginLog->client_id;
                $data[$key]['createdAt']    = (string)$loginLog->created_at;
                $data[$key]['updatedAt']    = (string)$loginLog->updated_at;
                if((string)$loginLog->agent_id == '0' && (int)$loginLog->client_id === 0){
                    $data[$key]['clientName']   = trans('lang.adminClient')[0];
                    $data[$key]['agentId']      = trans('lang.adminClient')[0];
                }
                if((string)$loginLog->agent_id == '0' && (int)$loginLog->client_id != 0){
                    $data[$key]['clientName']   = (string)$loginLog->apiClients['client_name'];
                    $data[$key]['agentId']      = trans('lang.adminClient')[1];
                }
                if((string)$loginLog->agent_id !== '0' && (int)$loginLog->client_id != 0){
                    $data[$key]['clientName']   = (string)$loginLog->apiClients['client_name'];
                    $data[$key]['agentId']      = (string)$loginLog->agent_id;
                }
            }
        }

        return $data;
    }

}