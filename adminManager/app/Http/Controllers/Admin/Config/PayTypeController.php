<?php

namespace App\Http\Controllers\Admin\Config;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Models\Config\PayType;
use Illuminate\Support\Facades\Redis;

class PayTypeController extends AdminController
{
    /**
     * 支付方式列表(全部)
     * @return mixed
     */
    public function payTypeSelect()
    {
        //排序 按照id降序
        PayType::$orderBy = 'type_id asc';
        //查询字段
        PayType::$field = ['type_id', 'type_name','english_name', 'is_status'];
        //查询
        $payTypes = PayType::getList();
        //计数
        $count    = PayType::getListCount();
        //销毁条件
        PayType::_destroy();
        //数据伪装
        $data = PayType::dataCamouflage($payTypes);

        self::setCount($count);
        return $this->responseJson($data);
    }


    /**
     * 支付方式列表(已开启的)
     * @return mixed
     */
    public function payTypeList()
    {
        //排序 按照id降序
        PayType::$orderBy = 'type_id asc';
        //查询字段
        PayType::$field = ['type_id', 'type_name','english_name'];
        //查询
        PayType::$where['is_status'] = 1;
        $payTypes = PayType::getList();
        //计数
        $count    = PayType::getListCount();
        //销毁条件
        PayType::_destroy();
        //数据伪装
        $data = PayType::dataCamouflage($payTypes);

        self::setCount($count);
        return $this->responseJson($data);
    }


    /**
     * 新增支付方式
     * @return \Illuminate\Http\JsonResponse
     */
    public function payTypeAdd()
    {
        //参数验证
        $post = self::requestParam(__FUNCTION__);
        //获取提交数据
        PayType::$data = PayType::addData($post);
        //添加数据
        $result = PayType::addToData();
        //销毁
        PayType::_destroy();
        if ($result->type_id) {
            //添加成功后返回数据伪装
            $data   = PayType::returnDataCamouflage($result);
            //清除Redis
            Redis::del('PayTypeList');
            return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS),$data);
        } else {
            return $this->responseJson(Code::FAIL_TO_ADD);
        }
    }

    /**
     * 修改支付方式
     * @return \Illuminate\Http\JsonResponse
     */
    public function payTypeUpdate()
    {
        //参数验证
        $put = self::requestParam(__FUNCTION__);
        //条件id
        PayType::$where['type_id'] = $put['typeId'];
        //提交参数
        PayType::$data = PayType::saveData($put);
        //修改数据
        if (PayType::editToData()) {
            //销毁
            PayType::_destroy();
            //清除Redis
            Redis::del('PayTypeList');
            $time = ['updatedTime' => date('Y-m-d H:i:s')];
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }
    }

}
