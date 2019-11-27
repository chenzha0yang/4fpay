<?php

namespace App\Http\Controllers\Admin\Logs;

use App\Http\Controllers\AdminController;
use App\Models\Logs\OperationLog;
use Illuminate\Http\Request;

class DoLogController extends AdminController
{
    /**
     * 操作日志
     * @return \Illuminate\Http\JsonResponse
     */
    public function doLogSelect()
    {
        //参数验证
        $get = self::requestParam(__FUNCTION__);
        //分页(传参：page、limit)
        OperationLog::$limit = $this->getPageOffset(self::limitParam());
        /*********************  筛选条件 *********************/
        OperationLog::$where = $this->selectWhere($get);
        //排序 按照id降序
        OperationLog::$orderBy = 'id desc';
        // 渴求式查询
        OperationLog::$Craving = ['users'];
        //查询
        $operationLogs = OperationLog::getList();
        //计数
        $count = OperationLog::getListCount();
        //销毁条件
        OperationLog::_destroy();
        //数据伪装
        $data = OperationLog::dataCamouflage($operationLogs);

        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * 操作日志 - 添加(中间件)
     * @param Request $request
     * @return bool
     */
    public static function createDoLogs(Request $request){
        OperationLog::$data = [
            'user_id'   => self::$adminUser->id,            //用户ID
            'path'      => $request->path(),                //访问path
            'method'    => $request->method(),              //提交方式
            'ip'        => self::getClientIp(0, true),                  //ip地址
            'client_id' => self::$adminUser->client_id,
            'agent_id'  => self::$adminUser->agent_id,
            'username'  => self::$adminUser->username,
            'input'     => json_encode($request->all()),
        ];

        return OperationLog::addToData();
    }

    /**
     * 操作日志检索条件
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
            //用户昵称
            if (!empty($get['account'])) {
                $where['username']   = $get['account'];
            }
        }
        if(self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 1){
            //平台线路
            $where['client_id']    = self::$adminUser->client_id;
            //代理
            if (!empty($get['agentId'])) {
                $where['agent_id'] = $get['agentId'];
            }
            //用户
            $where['user_id']      = self::$adminUser->id;
        }
        if (self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 2) {
            $where['client_id'] = self::$adminUser->client_id;
            $where['agent_id']  = self::$adminUser->agent_id;
            $where['user_id']   = self::$adminUser->id;
        }
        //Path
        if (!empty($get['path'])) {
            $where['path'] = $get['path'];
        }
        //登陆ip
        if (!empty($get['operationIp'])) {
            $where['ip'] = $get['operationIp'];
        }
        //默认当天时间
        list($start, $end) = self::getSelectDayTime($get);
        $where[] = ['created_at', '>=', $start];
        $where[] = ['created_at', '<=', $end];

        return $where;
    }

}
