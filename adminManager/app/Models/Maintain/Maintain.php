<?php

namespace App\Models\Maintain;

use App\Models\ApiModel;
use App\Models\Config\PayConfig;

class Maintain extends ApiModel
{
    protected $table = 'pay_maintain';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];


    /**
     * 维护列表-三方配置关联
     */
    public function payConfig()
    {
        return $this->hasOne(PayConfig::class,'pay_id','pay_id');
    }

    /**
     * 是否显示维护
     * @param $id
     * @return mixed
     */
    public static function display($id){
        return self::where('pay_id',$id)->count();
    }
}