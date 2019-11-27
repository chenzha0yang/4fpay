<?php

namespace App\Http\Models;

use App\Extensions\RedisConPool;
use Illuminate\Database\Eloquent\Model;

class PaymentBank extends Model
{
    protected $table = 'short_urls';

    protected $primaryKey = 'payment_id';

    protected $guarded = ['longUrl'];


    /**
     * @param $id
     * @param $agentid
     * @return bool
     */
    public static function getzFbpay($id, $agentid)
    {
        $TypeRedisKey = $agentid . "_PayMenBank";
        $payTypes     = RedisConPool::get($TypeRedisKey);
        if (isset($payTypes)) {
            $payTypes = json_decode($payTypes, true);
        } else {
            $payTypes = self::where("payment_id", $id)->get()->toArray();
            RedisConPool::set($TypeRedisKey, json_encode($payTypes));
        }
        foreach ($payTypes as $key => $value) {
            if ($id == $value['payment_id']) {
                return $value;
            }
        }
        return false;
    }
}
