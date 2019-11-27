<?php

namespace App\Http\Controllers\Admin\Logs;

use App\Http\Controllers\AdminController;
use App\Models\Logs\ApiOperationLog;


class ApiOperationLogsController extends AdminController
{
    public function apiOperationLogSelect()
    {
        $get = self::requestParam(__FUNCTION__);

        if (isset($get['clientId'])) {
            ApiOperationLog::$where['client_id'] = $get['clientId'];
        }

        //默认当天时间
        list($start, $end) = self::getSelectDayTime($get);
        $where[] = ['created_at', '>=', $start];
        $where[] = ['created_at', '<=', $end];

        ApiOperationLog::$orderBy = 'id desc';
        self::setCount(ApiOperationLog::getListCount());
        $res = ApiOperationLog::getList();
        //伪装
        $data = [];
        foreach ($res as $key => $val) {
            $data[$key]['Id']            = $val->id;
            $data[$key]['clientId']      = $val->client_id;
            $data[$key]['agentId']       = $val->agent_line;
            $data[$key]['clientName']    = $val->client_name;
            $data[$key]['operationIp']   = $val->ip;
            $data[$key]['httpPath']      = $val->path;
            $data[$key]['httpName']      = $val->action_name;
            $data[$key]['method']        = $val->method;
            $data[$key]['inputData']     = $val->input;
            $data[$key]['operationTime'] = (string)$val->created_at;

        }

        return $this->responseJson($data);

    }

}
