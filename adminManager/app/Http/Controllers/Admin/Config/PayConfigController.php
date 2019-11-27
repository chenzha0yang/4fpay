<?php

namespace App\Http\Controllers\Admin\Config;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Models\Config\PayConfig;
use App\Models\Config\PayType;
use App\Models\Maintain\Maintain;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class PayConfigController extends AdminController
{
    /**
     * 三方列表
     * @return mixed
     */
    public function payConfigSelect()
    {
        //参数验证
        $get = self::requestParam(__FUNCTION__);
        //分页(传参：page、limit)
        PayConfig::$limit = $this->getPageOffset(self::limitParam());
        /*********************  筛选条件 *********************/
        PayConfig::$where = $this->selectWhere($get);
        //排序 按照id降序
        PayConfig::$orderBy = 'pay_id desc';
        // 渴求式查询
        PayConfig::$Craving = ['payTypes'];
        //查询
        $payConfigs = PayConfig::getList();
        //计数
        $count = PayConfig::getListCount();
        //销毁
        PayConfig::_destroy();
        //数据伪装
        $data = PayConfig::dataCamouflage($payConfigs);

        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * 新增三方
     * @return \Illuminate\Http\JsonResponse
     */
    public function payConfigAdd()
    {
        //开启事务
        DB::beginTransaction();
        try {
            //参数验证
            $post = self::requestParam(__FUNCTION__);
            /*******************  获取提交数据并伪装 *************************/
            PayConfig::$data = PayConfig::addData($post);
            //入款网关
            PayType::$where['is_status'] = 1;
            $payType = PayType::getList();
            foreach ($payType as $type) {
                PayConfig::$data[$type->english_name . '_url'] = !empty($post[$type->english_name . 'Url']) ? (string)$post[$type->english_name . 'Url'] : '';
            }
            //获取支付方式
            PayConfig::$payTypeId = $post['typeId'];
            if ($result = PayConfig::addToData()) {
                //销毁
                PayConfig::_destroy();
                /****************** 返回添加的三方列表信息 ***********************/
                $response = PayConfig::getOne($result->pay_id);
                //添加成功返回数据伪装
                $data = PayConfig::returnDataCamouflage($response, $post);
                /******************* 根据code码返回支付方式 **********************/
                $codes               = $this->returnCode($post);
                $data['payCodeType'] = array_values($codes);
                /****************** 根据支付id返回支付方式 ***********************/
                if (!empty($post['typeId'])) {
                    PayType::$whereIn['type_id'] = $post['typeId'];
                    $types                       = PayType::getList();
                    //销毁
                    PayType::_destroy();
                    foreach ($types as $key => $type) {
                        $data['typeName'][] = $type->type_name;
                    }
                }
                //入款网关
                foreach ($payType as $type) {
                    $data[$type->english_name . 'Url'] = !empty($post[$type->english_name . 'Url']) ? (string)$post[$type->english_name . 'Url'] : '';
                }
                /********************* 添加到维护 *******************************/
                $this->maintainAdd($result->pay_id);
                //清除Redis
                Redis::del(PayConfig::$ConfigRedisKeyAdmin);
                Redis::del(PayConfig::$ConfigRedisKey);
                //提交事务
                DB::commit();
                return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS), [$data]);
            }
        } catch (\Exception $e) {
            //事务回滚
            DB::rollBack();
            return $this->responseJson(Code::FAIL_TO_ADD);
        }
    }

    /**
     * 修改三方
     * @return \Illuminate\Http\JsonResponse
     */
    public function payConfigUpdate()
    {
        //参数验证
        $put = self::requestParam(__FUNCTION__);
        //判断条件商户ID pay_id
        PayConfig::$where['pay_id'] = $put['payId'];
        /*******************  获取提交数据并伪装 *************************/
        PayConfig::$data = PayConfig::saveData($put);
        //入款网关
        $payType = PayType::typeList();
        foreach ($payType as $type) {
            if (isset($put[$type->english_name . 'Url'])) {
                PayConfig::$data[$type->english_name . '_url'] = (string)$put[$type->english_name . 'Url'];
            }
        }
        //获取支付方式
        PayConfig::$payTypeId = isset($put['typeId']) ? $put['typeId'] : null;

        //修改数据
        if (PayConfig::editToData($put['payId'])) {
            //销毁
            PayConfig::_destroy();
            //清除Redis
            Redis::del(PayConfig::$ConfigRedisKeyAdmin);
            Redis::del(PayConfig::$ConfigRedisKey);
            $time = ['updatedTime' => date('Y-m-d H:i:s')];
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }
    }

    /**
     * 三方配置下拉数据
     * @return mixed
     */
    public function getConfigLists()
    {
        //要查询的字段
        PayConfig::$field = array(
            'pay_id',
            'conf_name',
            'extend_name',
            'is_status'
        );

        $configLists                   = PayConfig::getList();
        PayConfig::_destroy();
        if (!$configLists) {
            return $this->responseJson(Code::NULL_DATA);
        }
        $data = [];
        foreach ($configLists as $k => $v) {
            $data[$k]['payId']    = $v->pay_id;
            $data[$k]['confName'] = $v->conf_name;
            $data[$k]['status']   = $v->is_status;
            if (!empty($v->conf_name)) {
                $data[$k]['extendName'] = $v->extend_name;
            }
        }
        return $this->responseJson($data);
    }

    /**
     * 添加维护 - 添加三方调用改方法
     * @param $payId - 添加三方返回的pay_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function maintainAdd($payId)
    {
        //查询是否已添加到维护
        Maintain::$where['pay_id'] = $payId;
        if (Maintain::getOne()) {
            //销毁
            Maintain::_destroy();
            return $this->responseJson(Code::HAVE_BEEN_MAINTAINED);
        } else {
            //提交参数
            $addData        = [
                'pay_id' => $payId,
            ];
            Maintain::$data = $addData;
            if (Maintain::addToData()) {
                //销毁
                Maintain::_destroy();
                return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS));
            } else {
                return $this->responseJson(Code::FAIL_TO_ADD);
            }
        }
    }

    /**
     * 根据code码返回支付方式
     * @param $post
     * @return array
     */
    public function returnCode($post)
    {
        //字符串分割成一维数组
        $payCode = explode(',', (string)$post['payCode']);
        $codes   = array();
        if ($post['payCode']) {
            //再次分割为二维数组
            foreach ($payCode as $k => $v) {
                //将数组的值赋给变量
                list($typeId, $code) = explode('-', $v);
                $codes[$k]['id']   = $typeId;
                $codes[$k]['code'] = $code;
                $id['type_id'][]   = $typeId;
            }
            PayType::$whereIn['type_id'] = $id['type_id'];
            $typeCode                    = PayType::getList();
            //销毁
            PayType::_destroy();
            foreach ($typeCode as $key => $type) {
                $codes[$key]['name'] = $type->type_name;
            }
        }

        return $codes;
    }

    /**
     * 三方列表筛选条件
     * @param $get
     * @return array
     */
    public function selectWhere($get)
    {
        $where = [];
        //商户类型(三方名称)
        if (!empty($get['payId'])) {
            $where['pay_id'] = $get['payId'];
        }
        //是否开启入款 1开启 2关闭
        if (!empty($get['inState'])) {
            $where['in_state'] = $get['inState'];
        }
        //是否开启出款 1开启 2关闭
        if (!empty($get['outState'])) {
            $where['out_state'] = $get['outState'];
        }
        //是否开启 1开启 2关闭
        if (!empty($get['isStatus'])) {
            $where['is_status'] = $get['isStatus'];
        }
        //ip白名单 1开启 2关闭
        if (!empty($get['whiteListState'])) {
            $where['whitelist_state'] = $get['whiteListState'];
        }
        return $where;
    }

    /**
     * top20
     * @return \Illuminate\Http\JsonResponse
     */
    public function topTwenty()
    {
        //查询20条数据
        $payConfigs = PayConfig::getTwentyList();
        //数据伪装
        $data = [];
        if ($payConfigs) {
            foreach ($payConfigs as $key => $payConfig) {
                $data[$key]['confName'] = (string)$payConfig->conf_name;
                $data[$key]['countUse'] = (int)$payConfig->count_use;
            }
        }

        return $this->responseJson($data);
    }

}
