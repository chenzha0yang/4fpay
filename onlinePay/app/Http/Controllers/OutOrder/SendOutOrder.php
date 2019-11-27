<?php

namespace App\Http\Controllers\OutOrder;

use App\Extensions\Common;
use App\Extensions\Curl;
use App\Extensions\RedisConPool;
use App\Http\Controllers\V1\SendOutCallbackController;
use App\Http\Models\CallbackMsg;
use App\Http\Models\OutOrder;
use Illuminate\Console\Command;

class SendOutOrder extends Command
{
    use Common;

    private $Model = null;

    private $queryData = null;

    private $outUrl = null;

    private $OutQyKey = 'outQueryList'; //查询队列

    /***
     * 代付请求
     * @param $data
     */
    public static function sendOrder($data)
    {
        // TODO

        $self = new self();
        $mod  = $data['Model'];
        global $app;
        $self->Model     = $app->make("App\\Http\\PayModels\\Outline\\$mod");
        $payConf         = $data['outMerchantInfo'];
        $self->queryData = ($self->Model)::outGetAllInfo($data, $payConf);
        $self->outUrl    = $data['OutUrl']; //代付请求地址
        $func            = $self->getReqFunc(($self->Model)::$outReqType);
        $OutOrder        = OutOrder::getOrderData($data['order']);//根据订单号 获取出款注单数据
        $orderQyKey      = 'orderqr' . '_' . $data['order'] . '_' . $data['agentId'];
        RedisConPool::set($orderQyKey, $OutOrder);
        //提交出款请求返回结果
        $res = $self->{$func}();
        //三方返回信息插入回调日志
        CallbackMsg::OutCallbackMsg($res, $OutOrder, 4, $data['ServerUrl']);
        if (!$res) {
            //三方请求失败，无返回信息
            OutOrder::updateOrder($OutOrder, [
                'is_status' => 2,
                'remark'    => trans('error.outfail')
            ]);
            // 通知平台  订单出款失败
            SendOutCallbackController::sendCallbackByOrder($OutOrder, 2);
            return;
        }
        //请求成功  订单状态 : 0未操作 1成功 2失败 3查询出款结果中 4加入查询队列失败
        $return = ($self->Model)::outResponse($res, $data['outMerchantInfo']);
        echo PHP_EOL . '代付状态:-' . $return['returnCode'], '代付描述:-' . $return['returnMsg'] . PHP_EOL;
        if ($return['returnCode'] != 'SUCCESS') {
            OutOrder::updateOrder($OutOrder, [
                'is_status' => 2,
                'remark'    => $return['returnMsg']
            ]);
            // 通知平台  订单出款失败
            SendOutCallbackController::sendCallbackByOrder($OutOrder, 2);
            return;
        }
        if ((int)$data['needQuery'] === 1) {
            echo PHP_EOL . 'SendOutOrder.php ' . __LINE__ . '行 加入查询队列: ' . json_encode($data) . PHP_EOL;
            //加入查询查询队列
            $redis = RedisConPool::rPush($self->OutQyKey, json_encode($data));
            if ($redis) {
                OutOrder::updateOrder($OutOrder, [
                    'is_status' => 3,
                    'remark'    => trans('error.outQuerying')
                ]);
            } else {
                //加入查询失败
                OutOrder::updateOrder($OutOrder, [
                    'is_status' => 4,
                    'remark'    => trans('error.outQueryError')
                ]);
                // 通知平台  订单出款加入查询
                SendOutCallbackController::sendCallbackByOrder($OutOrder, 2);
            }
        }
    }

    /***
     * 代付查询
     * @param $data
     * @return false|string
     */
    public static function ListDataDo($data)
    {
        // TODO
        $self = new self();
        $mod  = $data['Model'];
        global $app;
        $self->Model = $app->make("App\\Http\\PayModels\\Outline\\$mod");
        $payConf     = $data['outMerchantInfo'];
        //请求查询接口
        $self->queryData = ($self->Model)::OutQueryOrder($data, $payConf);
        $orderQyKey      = 'orderqr' . '_' . $data['order'] . '_' . $data['agentId'];
        $OutOrder        = RedisConPool::get($orderQyKey);
        if (empty($OutOrder->order_number)) {
            $OutOrder = OutOrder::getOrderData($data['order']);//根据订单号 获取出款注单数据
            RedisConPool::set($orderQyKey, $OutOrder);
        }
        echo PHP_EOL . 'SendOutOrder.php ' . __LINE__ . '行 获取注单信息:' . $OutOrder . PHP_EOL;
        $self->outUrl = ($self->Model)::$OutQryUrl;
        $func         = $self->getReqFunc(($self->Model)::$outReqType);
        //查询返回结果
        $res = $self->{$func}();
        echo PHP_EOL . 'SendOutOrder.php ' . __LINE__ . '行 获取三方返回信息：' . $res . PHP_EOL;
        //查询结果返回信息处理
        if (!$res) {
            //查询无返回，改订单状态并下发
            RedisConPool::del($orderQyKey);
            OutOrder::updateErrorOrder($data['order'], trans('error.outQfail'));
            SendOutCallbackController::sendCallbackByOrder($OutOrder, 2); //出款失败自动下发
            return;
        }
        $return = ($self->Model)::OutQueryRes($res, $data['outMerchantInfo']);
        $remark = json_encode(['returnCode' => $return['returnCode'], 'returnMsg' => $return['returnMsg']], 320);
        echo PHP_EOL . 'SendOutOrder.php ' . __LINE__ . '行 查询处理结果' . $remark . PHP_EOL;
        //查询结果插入日志
        CallbackMsg::OutCallbackMsg($res, $OutOrder, 5, '');
        if (isset($return['returnFail']) && $return['returnFail'] == 'QUERY_STOP') {
            //查询订单代付失败了，或者查询请求报错！停止查询
            RedisConPool::del($orderQyKey);
            OutOrder::updateOrder($OutOrder, ['is_status' => 2, 'remark' => $remark]);
            return;
        }
        if ($return['returnCode'] == 'SUCCESS') {
            //查询三方订单状态成功则更新四方订单并自动下发
            RedisConPool::del($orderQyKey);
            OutOrder::updateOrder($OutOrder, ['is_status' => 1, 'remark' => '订单出款成功']);
            SendOutCallbackController::sendCallbackByOrder($OutOrder, 1);
        } else {
            //查询三方返回状态不对，继续查询
            $redis = RedisConPool::rPush($self->OutQyKey, json_encode($data));
            if ($redis) {
                echo '再次加入查询队列=>==》》》继续查询 》》' . PHP_EOL;
            }
        }
    }

    /**
     * curl请求
     *
     * @return array|bool|object
     */
    private function curlRequest()
    {
        $request = $this->queryData;
        if (isset(($this->Model)::$httpBuildQuery)
            && ($this->Model)::$httpBuildQuery === true
        ) {

            $request = http_build_query($this->queryData);
        }

        if ($this->checkType(($this->Model)::$method, 'HEADER')) {
            if (isset(($this->Model)::$headerToArray)) {
                Curl::$headerToArray = ($this->Model)::$headerToArray;
            }
            Curl::$header = $this->queryData['httpHeaders'];
            $request      = $this->queryData['data'];//提交数据
        }

        if (isset(($this->Model)::$postType)
            && ($this->Model)::$postType === true
        ) {

            $request = ($this->Model)::getRequestByType($this->queryData);
        }

        Curl::$request = $request;//提交数据
        Curl::$url     = $this->outUrl;//出款网关
        Curl::$method  = ($this->Model)::$method;//提交方式

        return Curl::Request();
    }

    /**
     * file_get_contents
     *
     * @return bool|string
     */
    private function fileGetRequest()
    {
        $postData  = $this->queryData;
        $queryData = http_build_query($postData);
        $options   = [
            'http' => [
                'method'  => 'POST',
                'header'  => $this->queryData['httpHeaders'],
                'content' => $queryData,
                'timeout' => 60,// 超时时间（单位:s）
            ],
            "ssl"  => [
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ],
        ];
        $context   = stream_context_create($options);
        ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; rv:14.0) Gecko/20100101 Firefox/14.0.2');
        return file_get_contents($this->outUrl, false, $context);
    }
}