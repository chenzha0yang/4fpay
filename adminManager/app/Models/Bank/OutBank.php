<?php

namespace App\Models\Bank;

use App\Models\ApiModel;
use App\Models\Config\PayConfig;

class OutBank extends ApiModel
{
    protected $table = 'pay_out_bank_config';

    protected $primaryKey = 'bank_id';

    protected $guarded = ['bank_id'];

    /**
     * 三方类型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payConfigs(){
        return $this->belongsTo(PayConfig::class,'pay_id','pay_id');
    }

    /**
     * 出款银行检索条件
     * @param $get
     * @return array
     */
    public static function selectWhere($get){
        $where = [];
        //筛选条件
        if (!empty($get['payId'])) {
            $where['pay_id'] = $get['payId'];
        }
        if (!empty($get['uName'])) {
            $where['bank_name'] = $get['uName'];
        }

        return $where;
    }

    /**
     * 出款银行列表数据伪装
     * @param $banks
     * @return array
     */
    public static function dataCamouflage($banks){
        $data = [];
        foreach ($banks as $key => $value) {
            $data[$key]['Id']         = (int)$value->bank_id;
            $data[$key]['payId']      = (int)$value->pay_id;
            $data[$key]['uName']      = (string)$value->bank_name;
            $data[$key]['state']      = (int)$value->bank_status;
            $data[$key]['bankCode']   = (string)$value->bank_code;
            $data[$key]['createdAt']  = (string)$value->created_at;
            $data[$key]['updatedAt']  = (string)$value->updated_at;
            //三方类型名称
            $data[$key]['confName']   = $value->payConfigs->conf_name;
        }

        return $data;
    }

    /**
     * 获取批量添加出款银行数据
     * @param $dataName
     * @param $dataCode
     * @param $post
     * @return array
     */
    public static function addData($dataName,$dataCode,$post){
        //如果计数相等，将两个数组组合
        $arr = [];
        foreach ($dataName as $k => $v){
            $arr[$k]['bank_name']   = $v;
            $arr[$k]['pay_id']      = $post['payId'];
            $arr[$k]['bank_status'] = $post['state'];
            $arr[$k]['bank_code']   = $dataCode[$k];
            $arr[$k]['created_at']  = date('Y-m-d H:i:s');
            $arr[$k]['updated_at']  = date('Y-m-d H:i:s');
        }

        return $arr;
    }

    /**
     * 获取修改出款银行数据
     * @param $put
     * @return array
     */
    public static function saveData($put){
        if(!empty($put['payId'])){
            $data['pay_id'] = $put['payId'];
        }
        if(!empty($put['uName'])){
            $data['bank_name'] = $put['uName'];
        }
        if(!empty($put['state'])){
            $data['bank_status'] = $put['state'];
        }
        if(!empty($put['bankCode'])){
            $data['bank_code'] = $put['bankCode'];
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

}

