<?php

namespace App\Http\Controllers\Admin\Logs;

use App\Http\Controllers\AdminController;
use App\Models\Logs\LoginLogs;

class LoginLogController extends AdminController
{
    /**
     * 登陆日志列表
     * @return mixed
     */
    public function loginLogSelect()
    {
        //参数验证
        $get = self::requestParam(__FUNCTION__);
        //分页(传参：page、limit)
        LoginLogs::$limit   = $this->getPageOffset(self::limitParam());
        /*********************  筛选条件 *********************/
        LoginLogs::$where   = $this->selectWhere($get);
        //排序 按照id降序
        LoginLogs::$orderBy = 'id desc';
        // 渴求式查询
        LoginLogs::$Craving = ['apiClients'];
        //查询
        $loginLogs = LoginLogs::getList();
        //计数
        $count = LoginLogs::getListCount();
        //销毁条件
        LoginLogs::_destroy();
        //数据伪装
        $data = LoginLogs::dataCamouflage($loginLogs);

        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * 登陆日志检索条件
     * @param $get
     * @return mixed
     */
    public function selectWhere($get){
        if (self::$adminUser->view_client == 1 && self::$adminUser->view_agent == 1) {
            //平台线路
            if (!empty($get['clientUserId'])) {
                $where['client_id'] = $get['clientUserId'];
            }
            //代理
            if (!empty($get['agentId'])) {
                $where['agent_id'] = $get['agentId'];
            }
            //用户名
            if (!empty($get['account'])) {
                $where['user_name'] = $get['account'];
            }
        }
        if (self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 1) {
            //平台线路
            $where['client_id'] = self::$adminUser->client_id;
            //代理
            if (!empty($get['agentId'])) {
                $where['agent_id'] = $get['agentId'];
            }
            //用户
            $where['user_id']   = self::$adminUser->id;
        }
        if (self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 2){
            $where['client_id'] = self::$adminUser->client_id;
            $where['agent_id']  = self::$adminUser->agent_id;
            $where['user_id']   = self::$adminUser->id;
        }
        //登陆ip
        if (!empty($get['loginIp'])) {
            $where['login_ip'] = $get['loginIp'];
        }
        //默认当天时间
        list($start, $end)  = self::getSelectDayTime($get);
        $where[] = ['created_at', '>=', $start];
        $where[] = ['created_at', '<=', $end];

        return $where;
    }

}
