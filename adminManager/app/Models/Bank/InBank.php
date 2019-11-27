<?php

namespace App\Models\Bank;

use App\Extensions\RedisConPool;
use App\Models\ApiModel;
use App\Models\Config\PayConfig;

class InBank extends ApiModel
{
    protected $table = 'pay_bank_config';

    protected $primaryKey = 'bank_id';

    protected $guarded = ['bank_id'];

    public static $BankCacheKey = 'PayBankList';

    /**
     * 三方类型
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payConfigs(){
        return $this->belongsTo(PayConfig::class,'pay_id','pay_id');
    }

    /**
     * 入款银行列表检索条件
     * @param $get
     * @return array
     */
    public static function selectWhere($get){
        $where = [];
        //三方类型ID
        if (!empty($get['payId'])) {
            $where['pay_id'] = $get['payId'];
        }
        if (!empty($get['uName'])) {
            $where['bank_name'] = $get['uName'];
        }

        return $where;
    }

    /**
     * 入款银行数据伪装
     * @param $banks
     * @return array
     */
    public static function dataCamouflage($banks){
        $data = [];
        if($banks){
            foreach ($banks as $key => $value) {
                $data[$key]['Id']         = (int)$value->bank_id;
                $data[$key]['payId']      = (int)$value->pay_id;
                $data[$key]['uName']      = (string)$value->bank_name;
                $data[$key]['state']      = (int)$value->bank_status;
                $data[$key]['bankCode']   = (string)$value->bank_code;
                $data[$key]['createdAt']  = (string)$value->created_at;
                $data[$key]['updatedAt']  = (string)$value->updated_at;
                //三方类型名称
                $data[$key]['confName']   = $value->payConfigs['conf_name'];
            }
        }

        return $data;
    }

    /**
     * 获取添加入款银行数据 - 批量添加
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
     * 获取修改入款银行数据
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


    /**
     * 银行列表 缓存
     *
     * @param int $payId
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public static function getBankList($payId = 0)
    {
        $PayBankCache = RedisConPool::get(self::$BankCacheKey);
        if ($PayBankCache) {
            $payBanks = json_decode($PayBankCache);
        } else {
            $payBanks = self::getBankId();
            if($payBanks){
                RedisConPool::set(self::$BankCacheKey, json_encode($payBanks));
            }
        }
        if($payId){
            $data = [];
            foreach ($payBanks as $payBank) {
                if((int)$payId === (int)$payBank->pay_id){
                    $data[] = $payBank;
                }
            }
            if($data){
                return $data;
            } else {
                return [];
            }
        }
        return $payBanks;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getBankId()
    {
        return self::select(['bank_id', 'pay_id', 'bank_name', 'bank_code'])
            ->where('bank_status', 1)
            ->get();
    }

}

