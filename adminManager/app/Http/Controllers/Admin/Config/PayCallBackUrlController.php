<?php

namespace App\Http\Controllers\Admin\Config;

use App\Extensions\Code;
use App\Http\Controllers\AdminController;
use App\Models\Config\CallBackUrl;

class PayCallBackUrlController extends AdminController
{
    /**
     * 回调地址列表
     * @return \Illuminate\Http\JsonResponse
     */
    public function payCallBackUrlSelect()
    {
        //参数验证
        $get = self::requestParam(__FUNCTION__);
        //分页(传参：page、limit)
        CallBackUrl::$limit = $this->getPageOffset(self::limitParam());
        /*********************  筛选条件 *********************/
        CallBackUrl::$where = $this->selectWhere($get);
        //排序 按照id降序
        CallBackUrl::$orderBy = 'id desc';
        CallBackUrl::$Craving = ['client'];
        //查询
        $payCallBacks = CallBackUrl::getList();
        //计数
        $count = CallBackUrl::getListCount();
        //销毁条件
        CallBackUrl::_destroy();
        //数据伪装
        $data = CallBackUrl::dataCamouflage($payCallBacks, self::$adminUser);
        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function payCallBackUrlAdd()
    {
        $post                        = self::requestParam(__FUNCTION__);
        if (self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 1) {
            //平台线路
            $post['clientUserId'] = self::$adminUser->client_id;
        }
        CallBackUrl::$where['client_id'] = $post['clientUserId'];
        CallBackUrl::$where['agent_id'] = $post['agentId'];
        $result = CallBackUrl::getOne();
        CallBackUrl::_destroy();
        if ($result) {
            return $this->responseJson(Code::AGENT_IS_HAVE);
        }
        //获取提交数据
        CallBackUrl::$data = CallBackUrl::addData($post);
        //添加数据
        $result = CallBackUrl::addToData();
        //销毁
        CallBackUrl::_destroy();
        if ($result->id) {
            //添加成功返回数据伪装
            $data = CallBackUrl::returnDataCamouflage($result);
            return $this->responseJson($this->translateInfo(Code::ADD_SUCCESS),$data);
        } else {
            return $this->responseJson(Code::FAIL_TO_ADD);
        }

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function payCallBackUrlUpdate()
    {
        $put                        = self::requestParam(__FUNCTION__);
        if (self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 1) {
            //平台线路
            $put['clientUserId'] = self::$adminUser->client_id;
        }
        //条件id
        CallBackUrl::$where['id'] = $put['Id'];
        //提交参数
        CallBackUrl::$data = CallBackUrl::saveData($put);
        //修改数据
        if (CallBackUrl::editToData()) {
            //销毁
            CallBackUrl::_destroy();
            $time = ['updatedTime' => date('Y-m-d H:i:s')];
            return $this->responseJson($this->translateInfo(Code::UPDATE_SUCCESS), $time);
        } else {
            return $this->responseJson(Code::FAIL_TO_UPDATE);
        }
    }

    /**
     * 回调地址 - 查询条件
     * @param $get
     * @return array
     */
    public function selectWhere($get)
    {
        $where = [];
        if (self::$adminUser->view_client == 1 && self::$adminUser->view_agent == 1) {
            //平台线路
            if (!empty($get['clientUserId'])) {
                $where['client_id'] = $get['clientUserId'];
            }
            //代理线
            if (!empty($get['agentId'])) {
                $where['agent_id'] = $get['agentId'];
            }
        }
        if (self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 1) {
            //平台线路
            $where['client_id'] = self::$adminUser->client_id;
            //代理线
            if (!empty($get['agentId'])) {
                $where['agent_id'] = $get['agentId'];
            }
        }
        if (self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 2) {
            //平台线路
            $where['client_id'] = self::$adminUser->client_id;
            //代理线
            $where['agent_id'] = self::$adminUser->agent_id;
        }
        //IP
        if (!empty($get['agentIp'])) {
            $where['ip'] = $get['agentIp'];
        }
        //端口
        if (!empty($get['agentPort'])) {
            $where['port'] = $get['agentPort'];
        }
        //站点域名
        if (!empty($get['siteUrl'])) {
            $where['site_url'] = $get['siteUrl'];
        }

        return $where;
    }
}
