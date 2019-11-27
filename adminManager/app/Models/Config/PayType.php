<?php

namespace App\Models\Config;

use App\Models\ApiModel;
use App\Models\Order\InOrder;
use App\Extensions\RedisConPool;

class PayType extends ApiModel
{
    protected $table = 'pay_type';

    protected $primaryKey = 'type_id';

    protected $guarded = ['type_id'];

    public static $TypeRedisKey = 'PayTypeLists';

    /**
     * 支付方式列表数据伪装
     * @param $payTypes
     * @return array
     */
    public static function dataCamouflage($payTypes){
        $data = [];
        if ($payTypes) {
            foreach ($payTypes as $key => $payType) {
                $data[$key]['typeId']      = (int)$payType->type_id;
                $data[$key]['typeName']    = (string)$payType->type_name;
                $data[$key]['englishName'] = (string)$payType->english_name;
                $data[$key]['isStatus']    = (int)$payType->is_status;
            }
        }

        return $data;
    }

    /**
     * 获取支付方式添加数据
     * @param $post
     * @return array
     */
    public static function addData($post){
        $data = [
            'type_name'    => $post['typeName'],                                                //支付方式名称
            'english_name' => !empty($post['englishName']) ? (string)$post['englishName'] : '',  //支付方式别名
            'is_status'    => $post['isStatus'],                                                //是否使用
        ];

        return $data;
    }

    /**
     * 添加支付方式 - 返回数据信息
     * @param $result
     * @return array
     */
    public static function returnDataCamouflage($result){
        $data   =   array([
            'typeId'        => (int)$result->type_id,
            'typeName'      => (string)$result->type_name,
            'englishName'   => (string)$result->english_name,
            'isStatus'      => (int)$result->is_status,
        ]);

        return $data;
    }

    /**
     * 支付方式 - 修改数据
     * @param $put
     * @return array
     */
    public static function saveData($put){
        $data = [];
        if (isset($put['typeName'])) {
            $data['type_name'] = $put['typeName'];
        }
        if (isset($put['isStatus'])) {
            $data['is_status'] = $put['isStatus'];
        }
        if (isset($put['englishName'])) {
            $data['english_name'] = $put['englishName'];
        }

        return $data;
    }

    /**
     * 多对多
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function payConfigs()
    {
        return $this->belongsToMany(PayConfig::class, 'pay_config_pay_type', 'pay_type_type_id', 'pay_config_pay_id');
    }

    /**
     * 一对多 关联Order
     */
    public function payOrders()
    {
        return $this->hasMany(InOrder::class,'pay_way','type_id');
    }

    /**
     * 查询所有支付方式
     * @return mixed
     */
    public static function typeList(){
        return self::where('is_status', 1)->get();
    }

    public static function getPayType()
    {
        return self::getWayPayRedis();
    }

    /** 支付类型redis
     * @return array
     */
    public static function getWayPayRedis()
    {
        $payTypes = RedisConPool::get(self::$TypeRedisKey);

        if ($payTypes) {
            $payTypes = json_decode($payTypes,true);
        } else {
            $payTypes = self::where("is_status", 1)->get()->toArray();
            RedisConPool::set(self::$TypeRedisKey, json_encode($payTypes));
        }
        return $payTypes;
    }

    /**
     * @return bool
     */
    public static function getWayPay($Way)
    {
        $bankArr = self::getWayPayRedis();
        $payWay = [];
        foreach ($bankArr as $key => $value) {
            if ($value['is_status'] == 1 && $Way == $value['type_id']) {
                $payWay = $value;
            }
        }
        return $payWay;
    }

}