<?php

namespace App\Http\Models;

use \Illuminate\Database\Eloquent\Model;

class OutBank extends Model
{
    protected $table = 'pay_out_bank_config';

    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @param $payName
     * @param $payId
     * @return Model|null|static
     */
    public static function bankCodeCheck($payName, $payId)
    {
        return self::where('pay_id', $payId)
            ->where('bank_status', 1)
            ->where('bank_name', $payName)
            ->first();
    }
}