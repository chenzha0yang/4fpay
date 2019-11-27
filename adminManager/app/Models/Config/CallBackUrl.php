<?php

namespace App\Models\Config;

use App\Models\Config\ApiClients;
use App\Models\ApiModel;

class CallBackUrl extends ApiModel
{
    protected $table = 'pay_callback_url';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];


    /**
     *
     */
    public function client()
    {
        return $this->belongsTo(ApiClients::class, 'client_id', 'user_id');
    }

    /**
     * 客户接口数据伪装
     * @param $payCallBacks
     * @return array
     */
    public static function dataCamouflage($payCallBacks, $admin)
    {
        $data = [];
        if ($payCallBacks) {
            foreach ($payCallBacks as $key => $payCallBack) {
                $data[$key]['Id']             = (int)$payCallBack->id;
                $data[$key]['agentId']        = (string)$payCallBack->agent_id;
                $data[$key]['agentIp']        = (string)$payCallBack->ip;
                $data[$key]['agentPort']      = (int)$payCallBack->port;
                $data[$key]['siteUrl']        = (String)$payCallBack->site_url;
                $data[$key]['callBackUrl']    = (String)$payCallBack->callback_url;
                $data[$key]['outCallBackUrl'] = (String)$payCallBack->out_callback_url;
                if ($admin->view_client == 1 && $admin->view_agent == 1) {
                    $data[$key]['clientUserId']   = (int)$payCallBack->client_id;
                    $data[$key]['clientUserName'] = (String)$payCallBack->client['client_name'];
                }
                $data[$key]['createdAt']      = (string)$payCallBack->created_at;
                $data[$key]['updatedAt']      = (string)$payCallBack->updated_at;
            }
        }

        return $data;
    }

    /**
     * 获取添加客户数据
     * @param $post
     * @return array
     */
    public static function addData($post)
    {

        $data = array(
            'client_id'        => $post['clientUserId'],                                                 //平台线路
            'site_url'         => !empty($post['siteUrl']) ? (string)$post['siteUrl'] : '',            //站点域名
            'callback_url'     => $post['callBackUrl'],                                                  //入款异步回调路由
            'agent_id'         => !empty($post['agentId']) ? (string)$post['agentId'] : '',              //代理线
            'agent_num'        => 'a',                                                                   //代理线
            'ip'               => !empty($post['agentIp']) ? (string)$post['agentIp'] : '',              //IP
            'port'             => !empty($post['agentPort']) ? (int)$post['agentPort'] : '',             //端口
            'out_callback_url' => !empty($post['outCallBackUrl']) ? (string)$post['outCallBackUrl'] : ''  //出款异步回调路由
        );

        return $data;
    }

    /**
     * 添加客户接口 - 返回数据信息
     * @param $result
     * @return array
     */
    public static function returnDataCamouflage($result)
    {
        $data = array([
                          'Id'             => (int)$result->id,
                          'agentId'        => (string)$result->agent_id,
                          'agentIp'        => (string)$result->ip,
                          'agentPort'      => (int)$result->port,
                          'siteUrl'        => (string)$result->site_url,
                          'clientUserId'   => (int)$result->client_id,
                          'clientUserName' => (String)$result->client->client_name,
                          'callBackUrl'    => (string)$result->callback_url,
                          'outCallBackUrl' => (string)$result->out_callback_url,
                          'createdAt'      => (string)$result->created_at,
                          'updatedAt'      => (string)$result->updated_at,
                      ]);

        return $data;
    }

    /**
     * 获取编辑数据信息
     * @param $put
     * @return array
     */
    public static function saveData($put)
    {
        $data = array(
            //            'agent_id'         => !empty($put['agentId']) ? (string)$put['agentId'] : '',               //代理线
            //            'client_id'        => $put['clientUserId'],                                                 //平台线路
            'agent_num'        => 'a',                                                                  //代理线
            'ip'               => !empty($put['agentIp']) ? (string)$put['agentIp'] : '',               //IP
            'port'             => !empty($put['agentPort']) ? (int)$put['agentPort'] : '',              //端口
            'site_url'         => !empty($put['siteUrl']) ? (string)$put['siteUrl'] : '',               //站点域名
            'callback_url'     => !empty($put['callBackUrl']) ? (string)$put['callBackUrl'] : '',       //入款异步回调路由
            'out_callback_url' => !empty($put['outCallBackUrl']) ? (string)$put['outCallBackUrl'] : ''  //出款异步回调路由
        );

        return $data;
    }

    /**
     * @param array $attributes
     * @return bool|string
     */
    public static function getCallbackUrlByAgent(array $attributes)
    {
        $callUrl = self::where('agent_id', $attributes['agentId'])
            ->where('client_id', $attributes['clientId'])
            ->first();

        if (empty($callUrl)) {
            return false;
        }
        if(substr($callUrl->callback_url,0,1) != "/"){
            $callUrl->callback_url = "/{$callUrl->callback_url}";
        }
        if ($callUrl) {
            if ($callUrl->ip && $callUrl->port) {
                $url = "http://{$callUrl->ip}:{$callUrl->port}{$callUrl->callback_url}";
            } elseif ($callUrl->site_url) {
                $url = "{$callUrl->site_url}{$callUrl->callback_url}";
            } else {
                return false;
            }
        } else {
            return false;
        }

        return $url;
    }


}