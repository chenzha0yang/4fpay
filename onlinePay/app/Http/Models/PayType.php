<?php

namespace App\Http\Models;

use App\Extensions\RedisConPool;
use Illuminate\Database\Eloquent\Model;

class PayType extends Model
{
    protected $table = 'pay_type';

    protected $primaryKey = 'type_id';

    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
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
     * @return \Illuminate\Support\Collection
     */
    public static function getPayType()
    {
        return self::where('is_status', 1)
            ->get();
    }

    /**
     * @param int $typeId
     * @return bool|\Illuminate\Support\Collection|mixed|string
     */
    public static function getPayTypeRedis($typeId = 0)
    {
        $payType = self::getRedis();
        if ($typeId > 0) {
            foreach ($payType as $type) {
                if ($typeId == $type->type_id) {
                    return $type->english_name;
                }
            }
        }
        return $payType;
    }

    /**
     * @param int $typeId
     * @return mixed
     */
    public static function getPayTypeName($typeId = 1)
    {
        $payType = self::getRedis();
        $name = [];
        foreach ($payType as $type) {
            if ($typeId == $type->type_id) {
                $name['englishName'] = $type->english_name;
                $name['chineseName'] = $type->name;
                return $name;
            }
        }

    }

    /**
     * @return bool|\Illuminate\Support\Collection|mixed|string
     */
    public static function getRedis()
    {
        $payType = RedisConPool::get('PayTypeList');
        if (empty($payType)) {
            // redis中数据为空 从数据库读取
            $data = self::getPayType();
            RedisConPool::set(
                'PayTypeList',
                json_encode($data, JSON_UNESCAPED_UNICODE)
            );
            $payType = $data;
        } else {
            $payType = json_decode($payType);
        }
        return $payType;
    }

}