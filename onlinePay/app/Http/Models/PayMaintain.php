<?php

namespace App\Http\Models;


use Illuminate\Database\Eloquent\Model;

class PayMaintain extends Model
{
    protected $table = 'pay_maintain';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function getMaintainData(array $where)
    {
        return self::where($where)->first();
    }
}