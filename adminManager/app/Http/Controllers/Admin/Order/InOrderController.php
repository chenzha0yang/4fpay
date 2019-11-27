<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\AdminController;
use App\Models\Order\InOrder;
use App\Models\Config\ApiClients;
use App\Models\Config\CallBackUrl;
use App\Models\Merchant\inMerchant;
use App\Models\Logs\SendCallbacks;
use App\Extensions\Curl;
use App\Extensions\Code;

class InOrderController extends AdminController
{
    /**
     * 定义变量
     * @var array
     */
    private $callRes = [
        "code"  => false,
        "msg"   => '',
    ];

    /**
     * 入款订单列表
     * @return mixed
     */
    public function inOrderSelect()
    {
        //参数验证
        $get = self::requestParam(__FUNCTION__);
        //分页(传参：page、limit)
        InOrder::$limit = $this->getPageOffset(self::limitParam());
        /*********************  查询条件 *********************/
        InOrder::$where = $this->selectWhere($get);
        //排序 按照id降序
        InOrder::$orderBy = 'id desc';
        // 渴求式查询
        InOrder::$Craving = ['payConfigs','apiClients','payType'];
        //查询
        $inOrders = InOrder::getList();
        //计数
        $count    = InOrder::getListCount();
        //销毁
        InOrder::_destroy();
        //数据伪装
        $data = InOrder::dataCamouflage($inOrders);

        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * 入款订单下发
     * @return \Illuminate\Http\JsonResponse
     */
    public function inOrderLower()
    {
        //参数验证
        $post = self::requestParam(__FUNCTION__);
        if (is_numeric($post['inOrderId'])) {
            //订单ID
            InOrder::$where['id'] = $post['inOrderId'];
            //订单数据
            $orderData = InOrder::getOne();
            //销毁
            InOrder::_destroy();

            // 客户端信息
            ApiClients::$where['user_id'] = $orderData->client_user_id;
            $client = ApiClients::getOne();

            if (empty($callbackUrl = $orderData->callback_url)) {
                $callbackUrl = CallBackUrl::getCallbackUrlByAgent([
                    'agentId'    => $orderData->agent_id,
                    'agentNum'   => $orderData->agent_num,
                    'clientName' => $client->client_name,
                    'clientId'   => $client->id,
                ]);
            }

            ApiClients::_destroy();

            if ($orderData) {
                //回调地址不是空
                if ($callbackUrl) {
                    Curl::$url = $callbackUrl;
                    if ($orderData->is_status === 1 && $orderData->issued !== 1) {
                        $data = [
                            "status"  => true,
                            "code"    => "200",
                            "message" => trans('lang.succeeded'),
                            "sendTime" => date('Y-m-d H:i:s', time()),
                            "data"    => json_encode($this->paraDisguise($orderData, $client)),
                        ];
                    } else {
                        $data = [
                            "status"  => false,
                            "code"    => "200",
                            "message" => trans('lang.failed'),
                            "sendTime" => date('Y-m-d H:i:s', time()),
                            "data"    => json_encode(['errorMsg' => trans('lang.orderSure')[2]]),
                        ];
                    }

                    Curl::$request = $data;
                    $callResponse  = Curl::sendRequest();
                    //入款下发日志
                    SendCallbacks::createLog($orderData->order_number, $callResponse, $data, $callbackUrl, 2, 1);
                    //销毁
                    SendCallbacks::_destroy();
                    $bool = $this->editOrderIssued($orderData, $callResponse);

                    if (!$bool) {
                        $this->setCallMessage(trans('lang.issued')[2]);
                    }

                } else {
                    $this->setCallMessage(trans('lang.sendOrderError')[0]);
                }

            } else {
                $this->setCallMessage(trans('lang.sendOrderError')[1]);
            }

        } else {
            $this->setCallMessage(trans('lang.sendOrderError')[2]);
        }
        return $this->responseJson($this->callRes);
    }

    /**
     * 入款订单下发详情
     * @return \Illuminate\Http\JsonResponse
     */
    public function inOrderFind(){
        $get = self::requestParam(__FUNCTION__);
        //入款订单id
        if(is_numeric($get['inOrderId'])){
            InOrder::$where['id'] = $get['inOrderId'];
        }
        //查询入款订单
        $payOrder = InOrder::getOne();
        //销毁
        InOrder::_destroy();
        //根据入款订单查询下发日志
        SendCallbacks::$where['order'] = $payOrder->order_number;
        $sendCallback = SendCallbacks::getOne();
        //销毁
        SendCallbacks::_destroy();
        //数据伪装
        $data = $this->dataCamouflage($payOrder,$sendCallback);

        return $this->responseJson($data);
    }

    /**
     * 设置callRes属性
     *
     * @param $message
     */
    private function setCallMessage($message)
    {
        if (!empty($message)) {
            $this->callRes = $message;
        } else {
            $this->callRes["msg"] = $message;
        }
    }

    /**
     * 修改订单下发状态
     *
     * @param $object
     * @param string $callResponse
     * @return bool
     */
    private function editOrderIssued($object, $callResponse = '')
    {
        //返回信息为SUCCESS，且支付状态成功、下发状态为待处理状态，才能下发
        if ($callResponse === 'SUCCESS' && $object->is_status === 1 && ($object->issued === 0 || $object->issued === 2)) {
            //下发状态值
            $object->issued = Code::ISSUED_SUCCESS;
            $this->setCallMessage(Code::LOWER_SUCCESS);
        } else {
            $this->setCallMessage(Code::FAIL_LOWER);
        }
        return $object->save();
    }

    /**
     * 下发信息参数伪装
     * @param $orderData
     * @param $client
     * @return array
     */
    private function paraDisguise($orderData, $client)
    {
        $data = [];
        $data['order']        = (string)$orderData->order_number;    //订单号
        $data['agentId']      = (string)$orderData->agent_id;        //代理线
        $data['agentNum']     = (string)$orderData->agent_num;       //子代理线
        $data['amount']       = (string)$orderData->money;           //支付金额
        $data['status']       = (int)$orderData->is_status;          //状态
        $data['businessNum']  = (string)$orderData->business_num;    //商户号
        $data['clientSecret'] = $client->secret;
        ksort($data);
        $signStr = '';
        foreach ($data as $key => $val) {
            if (!is_null($val) && $val !== '') {
                $signStr .= "&&&{$key}=#{$val}#&&&";
            }
        }
        unset($data['clientSecret']);
        $data['sign'] = md5(md5($signStr));
        return $data;
    }

    /**
     * 获取入款订单检索条件
     * @param $get
     * @return mixed
     */
    public function selectWhere($get){
        if(self::$adminUser->view_client == 1 && self::$adminUser->view_agent == 1){
            //平台线路
            if(!empty($get['clientUserId'])){
                $where['client_user_id'] = $get['clientUserId'];
            }
            //代理
            if (!empty($get['agentId'])) {
                $where['agent_id'] = $get['agentId'];
            }
        }
        if(self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 1){
            //平台线路
            $where['client_user_id'] = self::$adminUser->client_id;
            //代理
            if (!empty($get['agentId'])) {
                $where['agent_id'] = $get['agentId'];
            }
        }
        if(self::$adminUser->view_client == 2 && self::$adminUser->view_agent == 2){
            $where['client_user_id'] = self::$adminUser->client_id;
            $where['agent_id']       = self::$adminUser->agent_id;
        }
        //订单号
        if (!empty($get['orderNumber'])) {
            $where['order_number'] = $get['orderNumber'];
        }
        //平台订单号
        if (!empty($get['owOrderNumber'])) {
            $where['ow_order_number'] = $get['owOrderNumber'];
        }
        //商户ID
        if (!empty($get['businessNum'])) {
            $where['business_num'] = $get['businessNum'];
        }
        //商户类型
        if (!empty($get['payId'])) {
            $where['pay_id'] = $get['payId'];
        }
        //支付类型
        if (!empty($get['payWay'])) {
            $where['pay_way'] = $get['payWay'];
        }
        //订单状态 1成功 2失败 0未操作
        if (!empty($get['isStatus']) && is_numeric($get['isStatus'])) {
            $where['is_status'] = $get['isStatus'];
        }
        //默认当天时间
        list($start, $end) = self::getSelectDayTime($get);
        $where[] = ['created_at', '>=', $start];
        $where[] = ['created_at', '<=', $end];

        return $where;
    }

    /**
     * 入款订单下发详情数据伪装
     * @param $payOrder
     * @param $sendCallback
     * @return array
     */
    public function dataCamouflage($payOrder,$sendCallback){
        $data = array(
            'orderNumber' => $payOrder->order_number,   //订单号
            'callbackUrl' => $payOrder->callback_url,   //回调地址
            'isAutoSend'  => $sendCallback->is_auto_send,  //1自动下发 2手动补发
            'returnMsg'   => $sendCallback->return_msg,    //同步响应信息
            'httpCode'    => $sendCallback->http_code,     //http响应
            'sendMsg'     => $sendCallback->send_msg,      //下发信息
        );

        return $data;
    }

}
