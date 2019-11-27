<?php

namespace App\Models\Config;

use App\Models\ApiModel;

class ApiClients extends ApiModel
{
    protected $table = 'api_clients';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    /**
     * 客户接口数据伪装
     * @param $apiClients
     * @return array
     */
    public static function dataCamouflage($apiClients){
        $data = [];
        if ($apiClients) {
            foreach ($apiClients as $key => $apiClient) {
                $data[$key]['Id']         = (int)$apiClient->id;
                $data[$key]['userId']     = (int)$apiClient->user_id;
                $data[$key]['clientName'] = (string)$apiClient->client_name;
                $data[$key]['Secret']     = (string)$apiClient->secret;
                $data[$key]['Revoked']    = (int)$apiClient->revoked;
                $data[$key]['createdAt']  = (string)$apiClient->created_at;
                $data[$key]['updatedAt']  = (string)$apiClient->updated_at;
            }
        }

        return $data;
    }

    /**
     * 获取添加客户接口数据
     * @param $post
     * @return array
     */
    public static function addData($post){
        $data = array(
            'user_id'     => $post['userId'],     //用户ID
            'client_name' => $post['clientName'], //接口名称
            'secret'      => $post['Secret'],     //授权证书
            'revoked'     => $post['Revoked'],    //是否开启 1开启，2关闭
        );

        return $data;
    }

    /**
     * 添加接口数据 - 返回数据信息
     * @param $result
     * @return array
     */
    public static function returnDataCamouflage($result){
        $data   =   array([
            'Id'            => (int)$result->id,
            'userId'        => (int)$result->user_id,
            'clientName'    => (string)$result->client_name,
            'Secret'        => (string)$result->secret,
            'Revoked'       => (int)$result->revoked,
            'createdAt'     => (string)$result->created_at,
            'updatedAt'     => (string)$result->updated_at,
        ]);

        return $data;
    }

    /**
     * 获取编辑客户接口数据信息
     * @param $put
     * @return array
     */
    public static function saveData($put){
        $data = array(
            'client_name' => !empty($put['clientName']) ? (string)$put['clientName'] : '', //接口名称
            'secret'      => $put['Secret'],                                               //授权证书
            'revoked'     => $put['Revoked'],                                              //是否开启 1开启，2关闭
        );

        return $data;
    }

}