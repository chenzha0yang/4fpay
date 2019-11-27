<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\AdminController;
use App\Models\Order\OutOrder;
use App\Models\Config\ApiClients;
use App\Models\Config\CallBackUrl;
use App\Models\Merchant\OutMerchant;
use App\Models\Logs\SendCallbacks;
use App\Extensions\Code;
use App\Extensions\Curl;

class OutOrderController extends AdminController
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
     * 出款订单列表
     * @return mixed
     */
    public function outOrderSelect()
    {
        //参数验证
        $get = self::requestParam(__FUNCTION__);
        //分页(传参：page、limit)
        OutOrder::$limit = $this->getPageOffset(self::limitParam());
        /*********************  查询条件 *********************/
        OutOrder::$where = $this->selectWhere($get);
        //排序 按照id降序
        OutOrder::$orderBy = 'id desc';
        // 渴求式查询
        OutOrder::$Craving = ['payConfigs','apiClients'];
        //查询
        $outOrders = OutOrder::getList();
        //计数
        $count     = OutOrder::getListCount();
        //销毁
        OutOrder::_destroy();
        //数据伪装
        $data = OutOrder::dataCamouflage($outOrders);

        self::setCount($count);
        return $this->responseJson($data);
    }

    /**
     * 出款订单下发
     */
    public function outOrderLower()
    {
        //参数验证
        $post = self::requestParam(__FUNCTION__);
        if (is_numeric($post['outOrderId'])) {
            //订单ID
            OutOrder::$where['id'] = $post['outOrderId'];
            //订单数据
            $orderData = OutOrder::getOne();
            //销毁
            OutOrder::_destroy();
            //客户端信息
            $client = ApiClients::getOne($orderData->client_user_id);
            //销毁
            ApiClients::_destroy();
            if (empty($callbackUrl = $orderData->callback_url)) {
                $merchant = OutMerchant::getOne($orderData->merchant_id);
                //销毁
                OutMerchant::_destroy();
                //下发地址
                $callbackUrl = CallBackUrl::getCallbackUrlByAgent([
                    'agentId'    => $merchant->agent_id,
                    'clientName' => $client->client_name,
                    'clientId'   => $client->id,
                ]);
                //销毁
                CallBackUrl::_destroy();
            }

            if ($orderData) {

                if ($callbackUrl) {
                    Curl::$url = $callbackUrl;
                    if ($orderData->is_status == 1) {
                        $data = [
                            "status"  => true,
                            "code"    => "200",
                            "msg" => trans('lang.succeeded'),
                            "data"    => json_encode($this->paraDisguise($orderData, $client)),
                        ];
                    } else {
                        $data = [
                            "status"  => false,
                            "code"    => "200",
                            "msg" => trans('lang.failed'),
                            "data"    => json_encode(['errorMsg' => trans('lang.orderSure')[2]]),
                        ];
                    }
                    Curl::$request = $data;
                    $callResponse  = Curl::sendRequest();
                    //出款下发日志
                    SendCallbacks::createLog($orderData->order_number, $callResponse, $data, $callbackUrl, 2, 2);
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
     * @param $object
     * @param string $callResponse
     * @return mixed
     */
    private function editOrderIssued($object, $callResponse = '')
    {
        if ($callResponse == 'SUCCESS' && $object->is_status == 1 && $object->issued == 0) {
            //下发状态值
            $object->issued = Code::ISSUED_SUCCESS;
            $this->setCallMessage(Code::LOWER_SUCCESS);
        } else {
            $this->setCallMessage(Code::FAIL_LOWER);
        }
        return $object->save();
    }

    /**
     * 出款订单下发详情
     * @return \Illuminate\Http\JsonResponse
     */
    public function outOrderFind(){
        $get = self::requestParam(__FUNCTION__);
        //出款订单id
        if(is_numeric($get['outOrderId'])){
            OutOrder::$where['id'] = $get['outOrderId'];
        }
        //查询出款订单
        $outOrder = OutOrder::getOne();
        //销毁
        OutOrder::_destroy();
        //根据出款订单查询下发日志
        SendCallbacks::$where['order'] = $outOrder->order_number;
        $sendCallback = SendCallbacks::getOne();
        //销毁
        SendCallbacks::_destroy();
        //数据伪装
        $data = $this->dataCamouflage($outOrder,$sendCallback);

        return $this->responseJson($data);
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
     * 出款订单列表检索条件
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
        //商户ID
        if (!empty($get['businessNum'])) {
            $where['business_num'] = $get['businessNum'];
        }
        //商户类型
        if (!empty($get['payId'])) {
            $where['pay_id'] = $get['payId'];
        }
        //订单状态 1成功 2失败 0未操作
        if (is_numeric($get['isStatus'])) {
            $where['is_status'] = $get['isStatus'];
        }
        //默认当天时间
        list($start, $end) = self::getSelectDayTime($get);
        $where[] = ['created_at', '>=', $start];
        $where[] = ['created_at', '<=', $end];

        return $where;
    }

    /**
     * 出款订单下发详情数据伪装
     * @param $outOrder
     * @param $sendCallback
     * @return array
     */
    public function dataCamouflage($outOrder,$sendCallback){
        $data = array(
            'orderNumber' => $outOrder->order_number,   //订单号
            'callbackUrl' => $outOrder->callback_url,   //回调地址
            'isAutoSend'  => $sendCallback->is_auto_send,  //1自动下发 2手动补发
            'returnMsg'   => $sendCallback->return_msg,    //同步响应信息
            'httpCode'    => $sendCallback->http_code,     //http响应
            'sendMsg'     => $sendCallback->send_msg,      //下发信息
        );

        return $data;
    }

}
