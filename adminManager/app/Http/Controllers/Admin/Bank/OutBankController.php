<?php

namespace App\Http\Controllers\Admin\Bank;

use App\Http\Controllers\AdminController;
use App\Models\Bank\OutBank;
use App\Extensions\Code;

class OutBankController extends AdminController
{
    /**
     * 出款银行列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function selectOutBank()
    {
        $get = self::requestParam(__FUNCTION__);
        //分页
        OutBank::$limit = $this->getPageOffset(self::limitParam());
        OutBank::$where = OutBank::selectWhere($get);
        // 渴求式查询
        OutBank::$Craving = ['payConfigs'];
        $banks = OutBank::getList();
        $count = OutBank::getListCount();
        self::setCount($count);
        OutBank::_destroy();
        //参数伪装
        $data = OutBank::dataCamouflage($banks);
        return $this->responseJson($data);
    }

    /**
     * 添加出款银行 - 批量添加
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOutBank()
    {
        $post       = self::requestParam(__FUNCTION__);
        //银行名称、code码 - 字符串转数组
        $dataName   = explode(',',$post['uName']);
        $dataCode   = explode(',',$post['bankCode']);
        //判断两个数组计数是否相等
        if(count($dataName) == count($dataCode)){
            $arr = OutBank::addData($dataName,$dataCode,$post);
        }else{
            return $this->responseJson(Code::BATCH_ADD_FAILURE);
        }
        //判断银行是否已存在
        foreach ($arr as $k => $v){
            OutBank::$where['bank_name'] = $v['bank_name'];
            OutBank::$where['pay_id'] = $v['pay_id'];
            if(OutBank::getOne()){
                //销毁
                OutBank::_destroy();
                return $this->responseJson(Code::BANK_TO_ALREADY);
            }
        }
        OutBank::$data = $arr;
        if (OutBank::insertToData()) {
            //销毁
            OutBank::_destroy();
            return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS));
        } else {
            return $this->responseJson(Code::FAIL_TO_ADD);
        }
    }

    /**
     * 编辑出款银行
     * @return \Illuminate\Http\JsonResponse
     */
    public function editOutBank()
    {
        $put                       = self::requestParam(__FUNCTION__);
        //判断银行是否已存在
        if(!empty($put['uName'])){
            OutBank::$where['bank_name'] = $put['uName'];
        }
        $result = OutBank::getOne();
        if(!empty($result) && $result['bank_id'] != $put['Id']){
            //销毁
            OutBank::_destroy();
            return $this->responseJson(Code::BANK_TO_ALREADY);
        }
        unset(OutBank::$where['bank_name']);
        OutBank::$where['bank_id'] = $put['Id'];
        OutBank::$data             = OutBank::saveData($put);
        if (OutBank::editToData()) {
            //销毁
            OutBank::_destroy();
            $time = ['updatedTime' => date('Y-m-d H:i:s')];
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }
    }

    /**
     * 删除出款银行
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delOutBank()
    {
        $delete                    = self::requestParam(__FUNCTION__);
        OutBank::$where['bank_id'] = $delete['Id'];
        if (OutBank::delToData()) {
            OutBank::_destroy();
            return $this->responseJson(Code::DELETE_SUCCESS);
        } else {
            return $this->responseJson(Code::FAIL_TO_DEL);
        }
    }
}
