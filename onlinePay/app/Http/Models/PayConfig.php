<?php

namespace App\Http\Models;

use App\Extensions\RedisConPool;
use \Illuminate\Database\Eloquent\Model;

class PayConfig extends Model
{
    protected $table = 'pay_config';

    protected $primaryKey = 'pay_id';

    protected $guarded = ['pay_id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * 多对多
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function payTypes()
    {
        return $this->belongsToMany(PayType::class, 'pay_config_pay_type', 'pay_config_pay_id', 'pay_type_type_id');
    }

    /** 获取模型名称
     * @param $id
     * @param bool $type
     * @return bool|string
     */
    public static function getPayTypeCode($id, $type = true)
    {
        $bankArr = self::getConfigRedis();
        $payCode = array();
        foreach ($bankArr as $key => $value) {
            if ($value->is_status == 1) {
                if($type){
                    if ($value->in_state == 1 && $id == $value->pay_id) {
                        $payCode = $value;
                    }
                } else {
                    if ($value->out_state == 1 && $id == $value->pay_id) {
                        $payCode = $value;
                    }
                }
            }
        }
        if ($payCode) {
            return trim($payCode->mod);
        } else {
            return false;
        }
    }

    /**
     * 获取三方正向代理开启状态
     * @param $mod string 模型名
     * @return string
     */
    public static function getAgentStatus($mod)
    {
        $conf = self::getConfigRedis();
        $status = '';
        foreach ($conf as $key => $value) {
            if ($value->mod === $mod) {
                $status = $value->agent_status;
            }
        }
        return $status;
    }

    public static function getOutConfigNeedQuery($id)
    {
        $bankArr = self::getConfigRedis();
        $payOut = [];
        foreach ($bankArr as $key => $value) {
            if ($value->out_state == 1 && $id == $value->pay_id) {
                $payOut = $value;
            }
        }
        if ($payOut) {
            return trim($payOut->need_query);
        } else {
            return false;
        }

    }

    /**
     * 获取三方对应支付方式的Code码
     * @param $id
     * @param $payWay
     * @return string
     */
    public static function getConfigCode($id,$payWay)
    {

        $bankArr = self::getConfigRedis();
        $Code = $payCode = '';

        foreach ($bankArr as $value) {
            if ($value->in_state == 1 && $id == $value->pay_id) {
                $payCode = $value->pay_code;
            }
        }
        if($payCode){
            $codeArr = explode(',',$payCode);
            foreach ($codeArr as $val) {
                if (explode('-',$val)[0] == $payWay) {
                    $Code = explode('-',$val)[1];
                }
            }
        }
        return $Code;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getPayTypeData()
    {
        $data = self::orderBy('pay_id', 'ASC')
            ->get();

        return $data;
    }

    /** 白名单
     * @param $payId
     * @return bool
     */
    public static function GetPayIsIpByPayType($payId)
    {
        if (empty($payId)) {
            return false;
        }
        $bankArr = self::getConfigRedis();
        foreach ($bankArr as $item) {
            if ($item->is_status == 1 && $item->pay_id == $payId && $item->whitelist_state == 1) {
                return true;
            }
        }
        return false;
    }

    /** 根据三方id 与 支付方式获取支付网关
     * @param $payId  三方id
     * @param $payWay 支付网关
     * @return bool
     */
    public static function getPayUrl($payId, $payWay)
    {
        if (empty($payId)) {
            return false;
        }
        $bankArr = self::getConfigRedis();
        foreach ($bankArr as $v) {
            if ($v->pay_id == $payId && $v->in_state == 1) {
                $result = $v;
            }
        }
        if (empty($result)) {
            return false;
        }
        $payType = PayType::getPayTypeRedis();

        if (empty($payType)) {
            return false;
        }

        foreach ($payType as $type) {
            if ($payWay == $type->type_id) {
                $typeName = $type->english_name;
            }
        }
        if (empty($typeName)) {
            return false;
        }
        return $result->{"{$typeName}_url"};
    }

    /**
     * 自动出款代付网关
     *
     * @param $payId
     * @return bool|mixed
     */
    public static function getOutPayUrl($payId)
    {
        if (empty($payId)) {
            return false;
        }
        $result = [];
        $bankArr = self::getConfigRedis();
        foreach ($bankArr as $v) {
            if ($v->pay_id == $payId && $v->out_state == 1) {
                $result = (array)$v;
            }
        }
        if($result['dispensing_url']){
            return $result['dispensing_url'];
        } else {
            return false;
        }
    }

    /** redis
     * @return array
     */
    public static function getConfigRedis()
    {
        $payType = RedisConPool::get('PayConfigList');
        if (empty($payType)) {
            // redis中数据为空 从数据库读取
            $payType = self::getPayTypeData();
            RedisConPool::set('PayConfigList', json_encode($payType));
        } else {
            $payType = json_decode($payType);
        }
        $array = array();
        foreach ($payType as $v) {
            $array[$v->pay_id] = $v;
        }
        return $array;
    }

    /**
     * 对象转数组
     *
     * @param $object
     * @return array
     */
    public static function objectToArray($object)
    {
        $array = [];
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        } else {
            $array = $object;
        }
        return $array;
    }
}