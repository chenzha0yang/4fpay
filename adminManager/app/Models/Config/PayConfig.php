<?php

namespace App\Models\Config;

use App\Extensions\RedisConPool;
use App\Models\ApiModel;
use App\Models\Whitelist\Whitelist;

class PayConfig extends ApiModel
{
    protected $table = 'pay_config';

    protected $primaryKey = 'pay_id';

    protected $guarded = ['pay_id'];

    public static $payTypeId = [];

    //redis缓存
    public static $ConfigRedisKeyAdmin = 'PayConfigListAdmin';
    public static $ConfigRedisKey = 'PayConfigList';

    /*******************************  三方列表部分 *******************************/

    /**
     * 三方列表数据伪装
     * @param $payConfigs
     * @return array
     */
    public static function dataCamouflage($payConfigs)
    {
        $data = [];
        if ($payConfigs) {
            foreach ($payConfigs as $key => $payConfig) {
                $data[$key]['payId']          = (int)$payConfig->pay_id;
                $data[$key]['confName']       = (string)$payConfig->conf_name;
                $data[$key]['confMod']        = (string)$payConfig->mod;
                $data[$key]['whiteListState'] = (int)$payConfig->whitelist_state;
                $data[$key]['payCode']        = (string)$payConfig->pay_code;
                $data[$key]['isStatus']       = (int)$payConfig->is_status;
                $data[$key]['inState']        = (int)$payConfig->in_state;
                $data[$key]['outState']       = (int)$payConfig->out_state;
                $data[$key]['countUse']       = (int)$payConfig->count_use;
                $data[$key]['remarkName']     = (string)$payConfig->remark;
                $data[$key]['version']       = (int)$payConfig->version;
                //入款网关
                $payType = PayType::typeList();
                foreach ($payType as $type) {
                    $types                                   = $type->english_name . '_url';
                    $data[$key][$type->english_name . 'Url'] = (string)$payConfig->$types;
                }
                //自动出款款代付网关
                $data[$key]['dispensingUrl'] = (string)$payConfig->dispensing_url;
                $data[$key]['createdAt']     = (string)$payConfig->created_at;
                $data[$key]['updatedAt']     = (string)$payConfig->updated_at;
                if ($payConfig->pay_code) {
                    //字符串分割成一维数组
                    $payCode = explode(',', (string)$payConfig->pay_code);
                    //再次分割为二维数组
                    $codes = array();
                    foreach ($payCode as $v) {
                        //将数组的值赋给变量
                        list($typeId, $code) = explode('-', $v);
                        $codes[$typeId]['code'] = $code;
                    }
                    //支付类型(多对多) -- 组合分割的二维数组
                    foreach ($payConfig->payTypes as $payType) {
                        //判断type_id是否存在
                        if (isset($codes[$payType->type_id])) {
                            $codes[$payType->type_id]['name'] = $payType->type_name;
                            $codes[$payType->type_id]['id']   = $payType->type_id;
                        }
                        $data[$key]['typeName'][] = $payType->type_name;
                        $data[$key]['typeId'][]   = $payType->type_id;
                    }
                    //将数组合并
                    $data[$key]['payCodeType'] = array_values($codes);
                } else {  //当pay_code为空
                    $data[$key]['payCodeType'] = array();
                    $data[$key]['typeName']    = array();
                    $data[$key]['typeId']      = array();
                }
                unset($codes);
            }

        }

        return $data;
    }

    /*******************************  三方列表新增部分 *******************************/
    /**
     * 三方添加数据
     * @param $post
     * @return array
     */
    public static function addData($post)
    {
        $data = [
            'conf_name'       => $post['confName'],                                                   //商户类型(三方名称)
            'mod'             => $post['confMod'],                                                    //模型名称
            'in_state'        => $post['inState'],                                                    //是否开启入款1开启 2关闭
            'out_state'       => $post['outState'],                                                   //是否开启出款1开启 2关闭
            'whitelist_state' => $post['whiteListState'],                                             //是否开启IP白名单 1开启 2关闭
            'is_status'       => $post['isStatus'],                                                   //是否开启 1开启 2关闭
            'pay_code'        => !empty($post['payCode']) ? (string)$post['payCode'] : '',            //三方扫码编码
            'dispensing_url'  => !empty($post['dispensingUrl']) ? (string)$post['dispensingUrl'] : '',//自动出款支付网关
            'extend_name'     => !empty($post['extendName']) ? (string)$post['extendName'] : '',      //扩展字段名称
            'remark'          => !empty($post['remarkName']) ? (string)$post['remarkName'] : '',      //备注
            'need_query'      => (int)$post['needQuery'],                                             //是否需要查询
            'version'         => !empty($post['version']) ? (int)$post['version'] : 1,
        ];

        return $data;
    }

    /**
     * 新增三方 - 返回数据
     * @param $response
     * @param $post
     * @return array
     */
    public static function returnDataCamouflage($response, $post)
    {
        $data = array(
            'payId'          => (int)$response->pay_id,
            'confName'       => (string)$response->conf_name,
            'confMod'        => (string)$response->mod,
            'inState'        => (int)$response->in_state,
            'isStatus'       => (int)$response->is_status,
            'outState'       => (int)$response->out_state,
            'whiteListState' => (int)$response->whitelist_state,
            'payCode'        => (string)$response->pay_code,
            'dispensingUrl'  => (string)$response->dispensing_url,
            'extendName'     => (string)$response->extend_name,
            'remarkName'     => (string)$response->remark,
            'typeId'         => (array)$post['typeId'],
            'createdAt'      => (string)$response->created_at,
            'updatedAt'      => (string)$response->updated_at,
            'version'         => (int)$post['version'],
        );

        return $data;
    }

    /*******************************  三方列表修改部分 *******************************/
    public static function saveData($put)
    {
        $data = [];
        if (isset($put['confName'])) {
            $data['conf_name'] = $put['confName'];
        }
        if (isset($put['confMod'])) {
            $data['mod'] = $put['confMod'];
        }
        if (isset($put['inState'])) {
            $data['in_state'] = $put['inState'];
        }
        if (isset($put['outState'])) {
            $data['out_state'] = $put['outState'];
        }
        if (isset($put['whiteListState'])) {
            $data['whitelist_state'] = $put['whiteListState'];
        }
        if (isset($put['isStatus'])) {
            $data['is_status'] = $put['isStatus'];
        }
        if (isset($put['payCode'])) {
            $data['pay_code'] = $put['payCode'];
        }
        if (isset($put['dispensingUrl'])) {
            $data['dispensing_url'] = $put['dispensingUrl'];
        }
        if (isset($put['extendName'])) {
            $data['extend_name'] = $put['extendName'];
        }
        if (isset($put['remarkName'])) {
            $data['remark'] = $put['remarkName'];
        }
        if (isset($put['version'])) {
            $data['version'] = $put['version'];
        }

        return $data;
    }

    /**
     * 多对多
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function payTypes()
    {
        return $this->belongsToMany(PayType::class, 'pay_config_pay_type', 'pay_config_pay_id', 'pay_type_type_id');
    }

    /**
     * 多对一
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function whiteLists()
    {
        return $this->belongsTo(Whitelist::class, 'pay_id', 'id');
    }

    /**
     * 与 支付方式 表关联，需重写
     *
     * @return mixed
     */
    public static function addToData()
    {
        $client = parent::addToData();
        if (!empty(self::$payTypeId)) {
            $client->payTypes()->attach(self::$payTypeId);
        }
        return $client;
    }

    /**
     * 与 支付方式 表关联，需重写
     *
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public static function delToData(int $id = 0)
    {
        $client = parent::getOne($id);

        foreach ($client->payTypes as $payType) {
            $payTypeIds[] = $payType->id;
        }
        if (!empty($payTypeIds)) {
            $client->payTypes()->detach($payTypeIds);
        }

        return parent::delToData($id);
    }

    /**
     * 与 支付方式 表关联，需重写
     * @param int $id
     * @return mixed
     */

    public static function editToData(int $id = 0)
    {
        $client = parent::getOne($id);

        if (!empty(self::$payTypeId)) {
            $client->payTypes()->sync(self::$payTypeId, $id);
        }

        return parent::editToData();
    }

    /**
     * @return mixed
     */
    public static function getTwentyList()
    {
        return PayConfig::where('is_status', 1)->orderBy('count_use', 'desc')->limit(20)->get();
    }

    /**
     * 三方配置 缓存
     *
     * @return array
     */
    public static function getConfigRedis()
    {
        $payConfigs = RedisConPool::get(self::$ConfigRedisKeyAdmin);
        if ($payConfigs) {
            $payConfigs = json_decode($payConfigs,true);
        } else {
            $payConfigs = self::getData();
            foreach ($payConfigs as $config){
                $config->pay_type = $config->payTypes->toArray();
            }
            $payConfigs = self::objectToArray($payConfigs);
            RedisConPool::set(self::$ConfigRedisKeyAdmin, json_encode($payConfigs));
        }
        return $payConfigs;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function getData()
    {
        return self::getList();
    }

    /** 判断三方类型是否存在 并且 可以使用
     * @param $id
     * @param int 1:入款 ；2：出款
     * @return string
     */
    public static function getPayType($id,$type = 1)
    {
        $bankArr = self::getConfigRedis();
//        dd($bankArr);
        $payCode = array();
        foreach ($bankArr as $value) {
            if ($type === 1) {
                if ($value['in_state'] == 1 && $value['pay_id'] == $id) {
                    $payCode = $value;
                }
            } else {
                if ($value['out_state'] == 1 && $value['pay_id'] == $id) {
                    $payCode = $value;
                }
            }

        }
        if (empty($payCode)) {
            return true;
        } else {
            return false;
        }
    }
}
