<?php

namespace App\Http\Controllers\Admin\Index;

use App\Http\Controllers\AdminController;
use App\Models\Order\InOrder;


class EchatsController extends AdminController
{
    public function showChat()
    {
        //组装返回数据结构
        $shelves = [];
        for ($i = 0; $i < 7; $i++) {
            $endDate                   = date('Y-m-d', strtotime(date('Y-m-d 23:59:59')) - 3600 * 24 * $i);
            $shelves[$i]['date']       = $endDate;
            $shelves[$i]['totalNum']   = 0;  //当天总订单数量
            $shelves[$i]['totalFee']   = 0;  //当天总订单金额
            $shelves[$i]['successNum'] = 0;  //当成功订单数量
            $shelves[$i]['successFee'] = 0;  //当成功订单金额
        }
        return $this->responseJson(self::groupByDay($shelves));
    }

    /**
     * @param $shelves
     * @return mixed
     */
    public static function groupByDay($shelves)
    {
        foreach ($shelves as $key => $value) {
            self::getOrderWhere($key);
            $totalList = InOrder::getList();
            InOrder::_destroy();
            foreach ($totalList as $k => $v) {
                if($v->is_status === 1){
                    $shelves[$key]['successNum'] += 1;
                    $shelves[$key]['successFee'] += $v->money;
                    $shelves[$key]['successFee'] = (float)sprintf('%.2f', $shelves[$key]['successFee']);//保留小数后两位
                }
                $shelves[$key]['totalNum'] += 1;
                $shelves[$key]['totalFee'] += $v->money;
                $shelves[$key]['totalFee'] = (float)sprintf('%.2f', $shelves[$key]['totalFee']); //保留小数后两位
            }
        }
        return $shelves;
    }

    /**
     * @param $i
     */
    public static function getOrderWhere($i = 6)
    {
        $j = $i + 1;
        $start = date('Y-m-d 23:59:59', strtotime("-{$j} day"));
        $end   = date('Y-m-d 23:59:59', strtotime("-{$i} day"));
        //查询条件
        InOrder::$where[] = ['created_at', '>=', $start];
        InOrder::$where[] = ['created_at', '<=', $end];

        //按平台、代理线路查询
        if (self::$adminUser->client_id !== 0 && self::$adminUser->agent_id === '0' ) {
            //线路管理员
            InOrder::$where[] = ['client_user_id', '=',self::$adminUser->client_id] ;
        }
        if (self::$adminUser->client_id != 0 && self::$adminUser->agent_id !== '0' ) {
            //代理线管理员
            InOrder::$where[] = ['client_user_id', '=',self::$adminUser->client_id] ;
            InOrder::$where[] = ['agent_id', '=',self::$adminUser->agent_id] ;
        }
        //需要查询的字段
        InOrder::$field = [
            'money',                //订单金额
            'is_status',            //订单状态 1成功 2失败 0未操作
            'issued',               //下发状态，1 成功 2失败 0未下发
            'created_at'            //订单时间
        ];
    }
}