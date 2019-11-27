<?php

namespace App\Models\Whitelist;

use App\Models\ApiModel;
use App\Models\Config\PayConfig;

class Whitelist extends ApiModel
{
    protected $table = 'pay_ip_whitelist';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    /**
     * 一对多
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payConfigs()
    {
        return $this->hasMany(PayConfig::class,'pay_id','pay_id');
    }
    public function payConfig()
    {
        return $this->belongsTo(PayConfig::class,'pay_id','pay_id');
    }

    /**
     * 白名单列表 - 检索条件
     * @param $get
     * @return array
     */
    public static function selectWhere($get){
        $where = [];
        //IP
        if (!empty($get['payIp'])) {
            $where['pay_ip'] = $get['payIp'];
        }
        //三方类型ID
        if (!empty($get['payId'])) {
            $where['pay_id'] = $get['payId'];
        }

        return $where;
    }

    /**
     * 白名单列表 - 数据伪装
     * @param $payWhitelists
     * @return array
     */
    public static function dataCamouflage($payWhitelists){
        $data = [];
        if ($payWhitelists) {
            foreach ($payWhitelists as $key => $payWhitelist) {
                $data[$key]['Id']            = (int)$payWhitelist->id;
                $data[$key]['payIp']         = (string)$payWhitelist->pay_ip;
                $data[$key]['state']         = (int)$payWhitelist->ip_state;
                $data[$key]['createdAt']     = (string)$payWhitelist->created_at;
                $data[$key]['updatedAt']     = (string)$payWhitelist->updated_at;
                //三方类型
                foreach ($payWhitelist->payConfigs as $payConfig) {
                    $data[$key]['payId']    = $payConfig->pay_id;
                    $data[$key]['confName'] = $payConfig->conf_name;
                }
            }
        }

        return $data;
    }

    /**
     * 获取添加白名单数据
     * @param $post
     * @return array
     */
    public static function addData($post){
        $data = array(
            'pay_id'     => $post['payId'],   //商户类型
            'pay_ip'     => $post['payIp'],   //IP
            'ip_state'   => $post['state'], //是否启用此IP 1开启 2关闭
        );

        return $data;
    }

    /**
     * 添加白名单 - 返回数据信息
     * @param $result
     * @return array
     */
    public static function returnDataCamouflage($result){
        $data   = array(
            'Id'        => (int)$result->id,
            'payId'     => (int)$result->pay_id,
            'payIp'     => (string)$result->pay_ip,
            'confName'  => $result->payConfig->conf_name,
            'state'     => (int)$result->ip_state,
            'createdAt' => (string)$result->created_at,
            'updatedAt' => (string)$result->updated_at,
        );

        return $data;
    }

    /**
     * 获取修改白名单数据
     * @param $put
     * @return array
     */
    public static function saveData($put){
        if(!empty($put['payId'])){
            $data['pay_id'] = $put['payId'];
        }
        if(!empty($put['payIp'])){
            $data['pay_ip'] = $put['payIp'];
        }
        if(!empty($put['state'])){
            $data['ip_state'] = $put['state'];
        }

        return $data;
    }

}