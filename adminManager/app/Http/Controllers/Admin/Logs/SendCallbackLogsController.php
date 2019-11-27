<?php

namespace App\Http\Controllers\Admin\Logs;

use App\Http\Controllers\AdminController;
use App\Models\Logs\SendCallbacks;

class SendCallbackLogsController extends AdminController
{
    /**
     * 下发日志
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCallbackLogSelect()
    {
        //参数验证
        $get = self::requestParam(__FUNCTION__);
        //分页(传参：page、limit)
        SendCallbacks::$limit   = $this->getPageOffset(self::limitParam());
        /*********************  筛选条件 *********************/
        SendCallbacks::$where   = $this->selectWhere($get);
        //排序 按照id降序
        SendCallbacks::$orderBy = 'id desc';
        //查询
        $sendCallbackLogs = SendCallbacks::getList();
        //计数
        $count = SendCallbacks::getListCount();
        //销毁
        SendCallbacks::_destroy();
        //数据伪装
        $data = SendCallbacks::dataCamouflage($sendCallbackLogs);

        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * 下发日志检索条件
     * @param $get
     * @return mixed
     */
    public function selectWhere($get){
        if(self::$adminUser->view_client == 1 && self::$adminUser->view_agent == 1){
            //平台线路
            if(!empty($get['clientUserId'])){
                $where['client_id'] = $get['clientUserId'];
            }
            //代理
            if (!empty($get['agentId'])) {
                $where['agent_id'] = $get['agentId'];
            }
        }
        if(self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 1){
            //平台线路
            $where['client_id'] = self::$adminUser->client_id;
            //代理
            if (!empty($get['agentId'])) {
                $where['agent_id'] = $get['agentId'];
            }
        }
        if(self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 2){
            $where['client_id'] = self::$adminUser->client_id;
            $where['agent_id']       = self::$adminUser->agent_id;
        }
        //订单号
        if (!empty($get['orderNumber'])) {
            $where['order'] = $get['orderNumber'];
        }
        //是否自动下发
        if (!empty($get['isAutoSend'])) {
            $where['is_auto_send'] = $get['isAutoSend'];
        }
        //入款出款下发 1为入款下发, 2为出款下发
        if (!empty($get['way'])) {
            $where['way'] = $get['way'];
        }
        //默认当天时间
        list($start, $end) = self::getSelectDayTime($get);
        $where[] = ['created_at', '>=', $start];
        $where[] = ['created_at', '<=', $end];

        return $where;
    }

}
