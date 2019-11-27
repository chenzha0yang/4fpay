<?php

namespace App\Http\Controllers\V1;

use App\Extensions\File;
use App\Http\Controllers\APIController;
use App\Http\Models\CallbackMsg;
use App\Http\Models\OutMerchant;
use App\Http\Models\OutOrder;
use Illuminate\Http\Request;

/**
 * 代付异步通知
 * Class OutCallbackController
 * @package App\Http\Controllers\V1
 */
class OutCallbackController extends APIController
{
    /**
     * @param Request $request
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    public function callback(Request $request)
    {
        $mod = $request->callback;
        global $app;
        $PayModel = $app->make("App\Http\PayModels\Outline\\$mod");
        //获取订单号
        $res   = $PayModel::getVerifyResult($request, $mod);
        $order = $res['order'];
        //根据订单号 获取入款注单数据
        $bankOrder = OutOrder::getOrderData($order);
        //查询不到注单信息时不插入回调日志，pay_id / pay_way 方式为0 ，关联字段不能为空
        if (empty($bankOrder)) {
            File::logResult($request->all());
            return trans("outSuccess.{$mod}");
        }
        //订单状态成功则响应三方对应信息
        if ($bankOrder->is_status == 1 && $bankOrder->issued == 1) {
            return trans("outSuccess.{$mod}");
        }
        //根据订单中的商户表ID获取配置信息
        $payMerchant = OutMerchant::findOrFail($bankOrder->merchant_id);
        // 回调验证
        if ($PayModel::OutCallback($request->all(), $payMerchant)) {
            // 验证成功改订单状态
            OutOrder::updateOrder($bankOrder, ['is_status' => 1, 'remark' => '订单出款成功']);
            // 自动下发
            SendOutCallbackController::sendCallbackByOrder($bankOrder, 1);
        } else {
            CallbackMsg::AddCallbackMsg($request, $bankOrder, 1);
        }

        unset($payMerchant);
        unset($bankOrder);

        return trans("outSuccess.{$mod}");
    }
}