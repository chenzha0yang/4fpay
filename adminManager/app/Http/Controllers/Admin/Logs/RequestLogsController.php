<?php
/**
 * Created by PhpStorm.
 * User: js-00035
 * Date: 2019/3/22
 * Time: 16:13
 */

namespace App\Http\Controllers\Admin\Logs;

use App\Http\Controllers\AdminController;
use App\Models\Logs\RequestLog;

class RequestLogsController extends AdminController
{
    /**
     * @param $request
     * @return mixed
     * 请求日志写入
     */
    public static function requestLogAdd($request)
    {
        $clientName       = isset($request->all()['clientName']) ? $request->all()['clientName'] : '';  //客户名称
        $clientId         = isset($request->all()['clientUserId']) ? $request->all()['clientUserId'] : '';  //客户名称
        $data             = [
            'client_name'   => $clientName,
            'client_id'     => $clientId,  //客户Id
            'request_ip'    => self::getClientIp(0,true),
            'request_aim'   => trans("lang.requestLog.{$request->path()}"),  //请求目的
            'request_route' => $request->path(),  //请求路由
            'request_data'  => json_encode($request->all()),  //请求数据
        ];
        RequestLog::$data = $data;
        return RequestLog::addToData();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * 查询日志
     */
    public function requestLogSelect()
    {
        $get                 = self::requestParam(__FUNCTION__);
        if (isset($get['startTime'])) {
            RequestLog::$where[] = ['created_at', '>=', $get['startTime']];
            RequestLog::$where[] = ['created_at', '<=', $get['endTime']];
        } else {
            RequestLog::$where[] = ['created_at', '>=', date('Y-m-d 00:00:00')];
            RequestLog::$where[] = ['created_at', '<=', date('Y-m-d 23:59:59')];
        }

        if (isset($get['request_data']) && !empty($get['request_data'])) {
            RequestLog::$where[] = ['request_data', 'like', "%{$get['request_data']}%"];
        }
        if (isset($get['router']) && !empty($get['router'])) {
            RequestLog::$where[] = ['request_route', 'like', "%{$get['router']}%"];
        }
        if (isset($get['client']) && !empty($get['client'])) {
            RequestLog::$where[] = ['client_name', 'like', "%{$get['client']}%"];
        }

        $lists               = RequestLog::getList();
        $data                = [];
        foreach ($lists as $key => $val) {
            $data[$key]['id']         = $val->id;
            $data[$key]['clientName'] = $val->client_name;
            $data[$key]['aim']        = $val->request_aim;
            $data[$key]['ip']         = $val->request_ip;
            $data[$key]['route']      = $val->request_route;
            $data[$key]['data']       = $val->request_data;
            $data[$key]['time']       = (string)$val->created_at;
        }
        return $this->responseJson($data);
    }
}
