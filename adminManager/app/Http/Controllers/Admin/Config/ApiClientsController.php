<?php

namespace App\Http\Controllers\Admin\Config;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Models\Config\ApiClients;

class ApiClientsController extends AdminController
{
    /**
     * 客户接口列表
     * @return mixed
     */
    public function apiClientsSelect()
    {
        //分页(传参：page、limit)
        ApiClients::$limit = $this->getPageOffset(self::limitParam());
        //排序 按照id降序
        ApiClients::$orderBy = 'user_id asc';
        //查询字段
        ApiClients::$field = ['id', 'user_id', 'client_name', 'secret', 'revoked', 'created_at','updated_at'];
        //查询
        $apiClients = ApiClients::getList();
        //计数
        $count = ApiClients::getListCount();
        //销毁条件
        ApiClients::_destroy();
        //数据伪装
        $data = ApiClients::dataCamouflage($apiClients);

        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * 新增客户接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiClientsAdd()
    {
        //参数验证
        $post = self::requestParam(__FUNCTION__);
        //获取提交数据
        ApiClients::$data = ApiClients::addData($post);
        //添加数据
        $result = ApiClients::addToData();
        //销毁
        ApiClients::_destroy();
        if ($result->id) {
            //添加成功返回数据伪装
            $data = ApiClients::returnDataCamouflage($result);
            return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS),$data);
        } else {
            return $this->responseJson(Code::FAIL_TO_ADD);
        }
    }

    /**
     * 修改客户接口
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiClientsUpdate()
    {
        //参数验证
        $put = self::requestParam(__FUNCTION__);
        //条件id
        ApiClients::$where['id'] = $put['Id'];
        //提交参数
        ApiClients::$data = ApiClients::saveData($put);
        //修改数据
        if (ApiClients::editToData()) {
            //销毁
            ApiClients::_destroy();
            $time = ['updatedTime' => date('Y-m-d H:i:s')];
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }
    }

    /**
     * 删除客户接口
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function apiClientsDelete()
    {
        $del = self::requestParam(__FUNCTION__);
        //设置WHERE条件 根据id 删除
        ApiClients::$where['id'] = $del['Id'];
        //删除
        if (ApiClients::delToData()) {
            //销毁
            ApiClients::_destroy();
            return $this->responseJson(Code::DELETE_SUCCESS);
        } else {
            return $this->responseJson(Code::FAIL_TO_DEL);
        }
    }

    /**
     * 平台线路下拉菜单
     */
    public function clientSelect()
    {
        if(self::$adminUser->view_client == 1){
            //平台线路
            if(!empty($get['clientUserId'])){
                ApiClients::$where['id'] = $get['clientUserId'];
            }
        }
        if(self::$adminUser->view_client == 2){
            //平台线路
            ApiClients::$where['id'] = self::$adminUser->client_id;
        }
        //排序 按照id升序
        ApiClients::$orderBy = 'id asc';
        $clients = ApiClients::getList();
        ApiClients::_destroy();
        if (!$clients) {
            return $this->responseJson(Code::NULL_DATA);
        }
        $data = [];
        foreach ($clients as $k => $v) {
            $data[$k]['value'] = $v->user_id;
            $data[$k]['label'] = $v->client_name;
        }
        return $this->responseJson($data);
    }
}
