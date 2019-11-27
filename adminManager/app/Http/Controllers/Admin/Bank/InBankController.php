<?php

namespace App\Http\Controllers\Admin\Bank;

use App\Http\Controllers\AdminController;
use App\Models\Bank\InBank;
use App\Extensions\Code;

class InBankController extends AdminController
{

    /**
     * 入款银行列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function selectInBank()
    {
        $get = self::requestParam(__FUNCTION__);
        //分页
        InBank::$limit = $this->getPageOffset(self::limitParam());
        //检索条件
        InBank::$where = InBank::selectWhere($get);
        // 渴求式查询
        InBank::$Craving = ['payConfigs'];
        $banks = InBank::getList();
        $count = InBank::getListCount();
        self::setCount($count);
        InBank::_destroy();
        //参数伪装
        $data = InBank::dataCamouflage($banks);

        return $this->responseJson($data);
    }

    /**
     * 添加入款银行 - 批量添加
     * @return \Illuminate\Http\JsonResponse
     */
    public function addInBank()
    {
        $post       = self::requestParam(__FUNCTION__);

        InBank::$where['pay_id'] = $post['payId'];
        InBank::$where['bank_code'] = $post['bankCode'];
        if (InBank::getOne()) {
            return $this->responseJson(Code::ALREADY_HAVE_BANK);
        }
        InBank::_destroy();

        //银行名称、code码 - 字符串转数组
        $dataName   = explode(',',$post['uName']);
        $dataCode   = explode(',',$post['bankCode']);
        //判断两个数组计数是否相等
        if(count($dataName) == count($dataCode)){
            $arr = InBank::addData($dataName,$dataCode,$post);
        }else{
            return $this->responseJson(Code::BATCH_ADD_FAILURE);
        }
        //判断银行是否已存在
        foreach ($arr as $k => $v){
            InBank::$where['bank_name'] = $v['bank_name'];
            InBank::$where['pay_id'] = $v['pay_id'];
            if(InBank::getOne()){
                //销毁
                InBank::_destroy();
                return $this->responseJson(Code::BANK_TO_ALREADY);
            }
        }
        InBank::$data = $arr;
        if (InBank::insertToData()) {
            //销毁
            InBank::_destroy();
            return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS));
        } else {
            return $this->responseJson(Code::FAIL_TO_ADD);
        }
    }

    /**
     * 编辑入款银行
     * @return \Illuminate\Http\JsonResponse
     */
    public function editInBank()
    {
        $put                      = self::requestParam(__FUNCTION__);
        //判断银行是否已存在
        if(!empty($put['uName'])){
            InBank::$where['bank_name'] = $put['uName'];
        }
        $result = InBank::getOne();
        if(!empty($result) && $result['bank_id'] != $put['Id']){
            //销毁
            InBank::_destroy();
            return $this->responseJson(Code::BANK_TO_ALREADY);
        }
        unset(InBank::$where['bank_name']);
        InBank::$where['bank_id'] = $put['Id'];
        InBank::$data             = InBank::saveData($put);
        if (InBank::editToData()) {
            //销毁
            InBank::_destroy();
            $time = ['updatedTime' => date('Y-m-d H:i:s')];
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }
    }

    /**
     * 入款银行删除
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delInBank()
    {
        $delete                   = self::requestParam(__FUNCTION__);
        InBank::$where['bank_id'] = $delete['Id'];
        if (InBank::delToData()) {
            InBank::_destroy();
            return $this->responseJson(Code::DELETE_SUCCESS);
        } else {
            return $this->responseJson(Code::FAIL_TO_DEL);
        }
    }

}