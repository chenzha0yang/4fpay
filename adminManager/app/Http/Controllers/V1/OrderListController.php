<?php

namespace App\Http\Controllers\V1;

use App\Models\Config\PayConfig;
use App\Models\Order\InOrder;
use App\Http\Controllers\ApiController;

class OrderListController extends ApiController
{
    const OPEN_THIS_API = false;
    /**
     * 逻辑处理方法体，每个类中必须要有此方法
     *
     * @param $params
     * @return array
     */
    public function runApi(&$params)
    {
        // 关闭接口对接权限
        if (self::OPEN_THIS_API) {
            return $this->replaceArray(['code' => '401']);
        }
        // 查询开始时间
        if (empty($params['startTime'])) {
            return $this->replaceArray(['code' => '401']);
        }
        // 查询开始时间
        if (empty($params['endTime'])) {
            $params['endTime'] = date("Y-m-d H:i:s");
        }
        // 查询区间不得大于7天
        $interval = strtotime($params['endTime']) - strtotime($params['startTime']);
        if ($interval > 24 * 60 * 60 * 7 || $interval < 0) {
            return $this->replaceArray(['code' => '1013']);
        }
        // 订单号验证
        if (self::checkParameter($params, 'orderId', '/^[0-9]+$/', false)) {
            return $this->replaceArray(['code' => '1029']);
        }

        $where = [];
        if (!empty($params['business'])) {
            $where['business_num'] = $params['business'];
        }
        if (!empty($params['orderId'])) {
            $where['order_number'] = $params['orderId'];
        }

        // 页码 默认为1
        if (empty($params['page']) || $params['page'] < 1) {
            $params['page'] = 1;
        }

        $limit  = config('app.api-limit.order');
        $offset = ($params['page'] - 1) * $limit;

        $PayOrderModel = InOrder::where('created_at', '>=', $params['startTime'])
            ->where('created_at', '<=', $params['endTime']);
        if (!empty($where)) {
            $PayOrderModel->where($where);
        }
        $PayOrder = $PayOrderModel->limit($limit)->offset($offset)->get();

        $data = [];
        if ($PayOrder) {
            $payName = PayConfig::all()->pluck('conf_name', 'pay_id');
            foreach ($PayOrder as $key => $order) {
                $data[$key]['orderId']   = (string)$order->order_number;        // 订单号
                $data[$key]['ownOrderId']= (string)$order->ow_order_number;     // 平台订单号
                $data[$key]['amount']    = (string)$order->money;               // 订单金额
                $data[$key]['business']  = (string)$order->business_num;        // 商户号
                $data[$key]['status']    = (int)   $order->is_status;           // 订单状态 1 成功 2 失败 0 未支付或暂无回调
                $data[$key]['payType']   = (int)   $order->pay_way;             // 支付方式 1 网银 2 微信 3 支付宝 4 QQ钱包 详见 pay_type 表
                $data[$key]['orderTime'] = (string)$order->created_at;          // 订单时间 datetime 格式
                $data[$key]['payName']   = (string)$payName[$order->pay_id];    // 三方类型名称
            }
        }

        return $this->replaceArray([
            'status' => true,
            'code'   => '200',
            'data'   => $data,
        ]);
    }


}