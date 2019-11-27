<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Extensions\RedisConPool;

class PayBank extends Model
{
    protected $table = 'pay_bank_config';

    public $incrementing = false;

    public static $BankCacheKey = 'PayBankList';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * 银行列表 缓存
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public static function getBankList()
    {
        $PayBankCache = RedisConPool::get(self::$BankCacheKey);
        if ($PayBankCache) {
            $payBanks = json_decode($PayBankCache);
        } else {
            $payBanks = self::getBankId();
            RedisConPool::set(self::$BankCacheKey, json_encode($payBanks));
        }
        return $payBanks;
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getBankId()
    {
        return self::select(['bank_id', 'pay_id', 'bank_name', 'bank_code'])
            ->where('bank_status', 1)
            ->get();
    }
}