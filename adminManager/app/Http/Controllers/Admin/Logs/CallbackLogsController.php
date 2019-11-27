<?php

namespace App\Http\Controllers\Admin\Logs;

use App\Http\Controllers\AdminController;
use App\Models\Logs\CallbackLogs;

class CallbackLogsController extends AdminController
{
    /**
     * 回调日志
     * @return \Illuminate\Http\JsonResponse
     */
    public function callbackLogSelect()
    {
        //参数验证
        $get = self::requestParam(__FUNCTION__);
        //分页(传参：page、limit)
        CallbackLogs::$limit = $this->getPageOffset(self::limitParam());
        /*********************  筛选条件 *********************/
        CallbackLogs::$where = $this->selectWhere($get);
        //排序 按照id降序
        CallbackLogs::$orderBy = 'id desc';
        // 渴求式查询
        CallbackLogs::$Craving = ['payConfigs','apiClients','payTypes'];
        //查询
        $callbackLogs = CallbackLogs::getList();
        //计数
        $count = CallbackLogs::getListCount();
        //销毁
        CallbackLogs::_destroy();
        $data = CallbackLogs::dataCamouflage($callbackLogs);

        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * 回调日志检索条件
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
                $where['agent_id']  = $get['agentId'];
            }
        }
        if(self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 1){
            //平台线路
            $where['client_id']    = self::$adminUser->client_id;
            //代理
            if (!empty($get['agentId'])) {
                $where['agent_id'] = $get['agentId'];
            }
        }
        if (self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 2) {
            $where['client_id'] = self::$adminUser->client_id;
            $where['agent_id']  = self::$adminUser->agent_id;
        }
        //订单号
        if (!empty($get['orderNumber'])) {
            $where['order_number'] = $get['orderNumber'];
        }
        //支付方式
        if (!empty($get['payWay'])) {
            $where['pay_way'] = $get['payWay'];
        }
        //回调来源IP
        if (!empty($get['callbackIp'])) {
            $where['callback_ip'] = $get['callbackIp'];
        }
        //默认当天时间
        list($start, $end) = self::getSelectDayTime($get);
        $where[] = ['created_at', '>=', $start];
        $where[] = ['created_at', '<=', $end];

        return $where;
    }

}
